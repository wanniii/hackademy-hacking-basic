# 04 · Quokka RAT — 탐지 · 방어 (Blue Team)

RAT(Remote Access Trojan)을 **탐지하고 막는** 관점의 정리.
공격 구현 대신, "감염 시 어디에 흔적이 남고, 무엇을 감시하면 잡히는가"에 집중한다.

> 아래 점검 명령은 **본인 소유 시스템/격리된 교육 환경에서, 상태를 확인(read-only)**
> 하는 용도다. 설정을 바꾸는 명령(방화벽 차단 등)은 환경과 정책에 맞게 신중히 적용한다.

---

## 1. 큰 그림 — RAT은 어디서 꼬리를 밟히나

RAT이 동작하려면 반드시 세 가지 흔적을 남긴다. 방어는 이 셋을 각각 감시한다.

1. **네트워크**: C2와 통신해야 한다 → 비정상 **아웃바운드 연결**, 주기적 비콘.
2. **프로세스**: 명령을 실행해야 한다 → 네트워크 프로세스가 **셸/스크립트 해석기**를
   자식으로 생성, 비정상 부모-자식 관계.
3. **지속성**: 재부팅 후에도 살아야 한다 → **자동시작 지점**(cron/Run 키/서비스/launchd)에
   등록된 낯선 항목.

---

## 2. 아웃바운드 연결 감시 (네트워크)

리버스 쉘은 피해자가 **밖으로 나가는** 연결을 만든다. 들어오는 연결만 보면 놓친다.

**무엇을 의심하나**
- 평소 네트워크를 쓰지 않던 프로세스가 외부로 연결.
- 일정 간격으로 반복되는 짧은 연결(**비콘**), 사용자 활동과 무관한 야간 트래픽.
- 알려지지 않은 IP/도메인, 비표준 포트, 사무용 PC에서의 원시 TCP 세션.
- DNS 터널링, 비정상적으로 긴/무작위 도메인 조회.

**현재 연결·리스닝 포트 확인 (read-only)**

```bash
# Linux/macOS
ss -tunap           # 또는: netstat -tunap  / lsof -i -nP
```

```powershell
# Windows (PowerShell)
Get-NetTCPConnection -State Established |
  Select-Object LocalAddress,LocalPort,RemoteAddress,RemotePort,OwningProcess |
  Sort-Object RemoteAddress
# 어떤 프로세스인지:
Get-Process -Id <OwningProcess>
```

**방어 조치**
- **아웃바운드 기본 차단(egress filtering)**: 필요한 목적지/포트만 허용(allowlist).
- 프록시·DNS 로그 중앙 수집, 비콘 탐지(연결 주기·크기 규칙화).
- 알려진 악성 IP/도메인 위협 인텔 차단, TLS 검사(가능한 환경에서).

---

## 3. 프로세스 · 지속성 점검

### 3-1. 수상한 프로세스

**무엇을 의심하나**
- `python`/`powershell`/`bash` 등이 **네트워크 연결을 들고** 셸을 자식으로 생성.
- 웹서버·문서뷰어 같은 프로세스가 `cmd.exe`/`/bin/sh`를 띄움(비정상 부모-자식).
- 정상 프로세스명과 비슷하게 위장한 이름(`svch0st`, `chrome ` 뒤 공백 등), 임시폴더에서 실행.

```bash
# Linux/macOS: 프로세스 트리 + 실행 경로
ps -ef --forest 2>/dev/null || ps aux
ls -l /proc/<PID>/exe        # Linux: 실제 실행 파일 경로
```

```powershell
# Windows: 부모-자식 관계
Get-CimInstance Win32_Process |
  Select-Object ProcessId,ParentProcessId,Name,CommandLine |
  Format-Table -Auto
```

### 3-2. 지속성(자동시작) 지점 점검

재부팅 생존을 위해 공격자가 자신을 등록하는 대표 위치들. **정기적으로 베이스라인과
비교**해 낯선 항목을 찾는다.

**Linux**

```bash
# cron
crontab -l 2>/dev/null
ls -l /etc/cron.* /etc/crontab /var/spool/cron/ 2>/dev/null
# systemd 서비스/타이머
systemctl list-unit-files --type=service --state=enabled
systemctl list-timers --all
# 셸 시작 스크립트
ls -l ~/.bashrc ~/.bash_profile ~/.profile /etc/profile.d/ 2>/dev/null
```

**Windows**

```powershell
# Run / RunOnce 레지스트리 키
Get-ItemProperty HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\Run
Get-ItemProperty HKCU:\SOFTWARE\Microsoft\Windows\CurrentVersion\Run
# 예약 작업 / 서비스 / 시작 프로그램
Get-ScheduledTask | Where-Object State -ne 'Disabled'
Get-CimInstance Win32_StartupCommand | Select-Object Name,Command,Location
Get-Service | Where-Object Status -eq 'Running'
```

> 종합 도구: Sysinternals **Autoruns**(모든 자동시작 지점 한 번에), **Process Explorer**.

**macOS**

```bash
# launchd (LaunchAgents / LaunchDaemons)
ls -l ~/Library/LaunchAgents /Library/LaunchAgents /Library/LaunchDaemons 2>/dev/null
launchctl list
# 로그인 항목
osascript -e 'tell application "System Events" to get the name of every login item'
```

---

## 4. 트로이목마 예방 (최초 감염 차단)

RAT은 보통 사용자가 **스스로 실행**해서 들어온다. 실행 전에 막는 것이 가장 싸다.

- **출처 확인**: 신뢰할 수 있는 공식 배포처에서만 설치. 메일 첨부·불법 다운로드 금지.
- **코드 서명 검증**: 서명 없는/서명 깨진 실행파일 경계. 확장자 위장(`invoice.pdf.exe`) 주의.
- **문서 매크로 비활성화**: Office 매크로 기본 차단, 신뢰할 수 없는 매크로 실행 금지.
- **확장자·숨김 확장자 표시** 켜기, 알 수 없는 실행파일은 격리 환경(샌드박스)에서 먼저 확인.
- **사용자 교육**: "선물/긴급/당첨" 류의 사회공학적 유도에 대한 인식 제고.

---

## 5. 최소권한 · EDR (피해 억제)

감염되더라도 **권한이 낮고 행위가 탐지되면** 피해가 제한된다.

- **최소권한(Least Privilege)**: 일상 작업은 관리자/root 아닌 계정으로. sudo/UAC 남용 자제.
- **응용 허용목록(Application Allowlisting)**: 승인된 실행파일만 실행 허용(AppLocker/WDAC 등).
- **네트워크 분할**: 세그먼트 분리로 측면 이동(lateral movement) 억제.
- **EDR/AV**: 행위 기반 탐지(네트워크→셸 생성, 지속성 등록, 비정상 자식 프로세스),
  중앙 로그 수집·경보. 패치·업데이트 상시 적용.
- **로깅 강화**: 프로세스 생성 로그(Windows Event 4688/Sysmon, auditd),
  명령행(command line) 기록 보존.

---

## 6. 사고대응(IR) 체크리스트

감염이 의심될 때의 순서. **성급히 재부팅/삭제하면 증거가 날아간다** — 보존 먼저.

1. **격리(Contain)**: 해당 호스트를 네트워크에서 분리(유선 분리/격리 VLAN).
   계정 자격증명 재설정 범위 판단. *단, 휘발성 증거 수집 전 전원 차단은 신중히.*
2. **보존(Preserve)**: 메모리 덤프·현재 연결(`ss`/`Get-NetTCPConnection`)·프로세스
   목록·자동시작 항목·관련 로그를 **분리·격리 전/중에** 수집해 보관.
3. **식별(Identify)**: 최초 침투 경로(피싱·다운로드), C2 목적지(IP/도메인/포트),
   지속성 등록 위치, 영향 받은 계정·호스트 범위 파악.
4. **제거(Eradicate)**: 지속성 항목 제거, 악성 바이너리 삭제, 취약점 패치, 자격증명 교체.
   신뢰 회복이 어려우면 **재설치(reimage)**가 가장 확실.
5. **복구(Recover)**: 깨끗한 백업에서 복원, 모니터링을 강화한 채 단계적 복귀.
6. **교훈(Lessons learned)**: 탐지 규칙 추가(비콘·부모-자식 관계),
   egress 정책 보완, 사용자 교육 반영. 타임라인과 IOC 문서화.

**수집해두면 좋은 지표(IOC)**: C2 IP/도메인/포트, 악성 파일 해시·경로, 자동시작 등록
키·작업 이름, 생성된 계정, 비정상 프로세스 명령행.

---

> ⚠️ 교육·방어 목적 문서. 점검 명령은 **본인 소유·격리 환경**에서만 사용하고,
> 차단·삭제 등 변경은 조직 정책과 사고대응 절차에 따라 수행한다.
