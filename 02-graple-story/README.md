# 02 · Graple Story (리버싱 / 게임핵)

C언어로 턴제 RPG "Graple Story"를 만들고, **내가 컴파일한 바이너리를 리버싱**해서
게임핵(치트)으로 클리어한다. 정적 분석(디스어셈블 + 바이너리 패치)과
동적 분석(gdb 런타임 메모리 변조)을 모두 실습한다.

## 스토리

- **graple_story_1** — 고블린(체력20). 정상 플레이로 클리어 가능.
- **graple_story_2** — 드래곤(체력200/공격30), 플레이어(체력100/공격6).
  플레이어가 너무 약해 **정상 플레이로는 절대 못 이기는 "망겜"** → 해킹으로 클리어.

## 환경 (Docker)

```bash
# gcc + gdb + binutils 컨테이너
docker run -dit --name graple_rev -w /work gcc:13 bash
docker exec graple_rev bash -c 'apt-get update -qq && apt-get install -y -qq gdb'

# 소스 복사 후 컴파일
docker cp ./src/. graple_rev:/work/
docker exec graple_rev bash -c 'cd /work && gcc -o graple_story_2 graple_story_2.c'
```

> 컴파일된 바이너리는 빌드 산출물이라 커밋하지 않는다(`.gitignore`). 소스에서 재생성.

## 리버싱 분석

`objdump -d -M intel graple_story_2` 로 `main` 을 보면 초기값 세팅이 보인다:

```asm
mov DWORD PTR [rbp-0x4], 0x64   ; playerHealth = 100
mov DWORD PTR [rbp-0xc], 0x6    ; playerAttack = 6
mov DWORD PTR [rbp-0x8], 0xc8   ; enemyHealth  = 200  (드래곤)  ← 타깃
mov DWORD PTR [rbp-0x10],0x1e   ; enemyAttack  = 30
```

승리 조건은 `enemyHealth < 0`. 드래곤 체력만 낮추면 한 방에 깬다.

## 게임핵 ① — 정적 바이너리 패치 ([patch_dragon.py](exploits/patch_dragon.py))

드래곤 체력 세팅 명령의 기계어 `C7 45 F8 C8 00 00 00` 에서 즉시값 `0xC8`(200)을 `0x01`로 바꾼다.

```bash
python exploits/patch_dragon.py graple_story_2   # -> graple_story_2_hacked
echo 0 | ./graple_story_2_hacked
```

결과 — 공격 한 번에:

```
현재 드래곤 체력 : -5
드래곤을 쓰러뜨렸습니다. 신난다!!
```

패치 후 디스어셈블: `mov DWORD PTR [rbp-0x8], 0x1`.

## 게임핵 ② — 동적 분석(gdb 런타임 변조) ([hack.gdb](exploits/hack.gdb))

파일을 건드리지 않고, 실행 중 메모리의 드래곤 체력 값을 바꾼다.

```bash
printf "0\n" > in.txt
gdb -q -batch -x exploits/hack.gdb ./graple_story_2
```

`break *0x4011f8` 로 초기화 직후 멈춰 `set {int}($rbp-0x8)=1` 로 체력을 1로 만들고 continue →
동일하게 드래곤 처치.

## 배운 것

- 프로그램의 값/로직은 소스가 없어도 **기계어 수준에서 바꿀 수 있다**(게임핵의 원리).
- 정적(파일 패치) vs 동적(디버거 런타임 변조) 두 접근.
- 방어: 변조 탐지(무결성 검사), 안티 디버깅, 서버 권위(중요 값은 서버가 계산) 등.

## 진행 현황

- [x] graple_story_1 / 2 작성·컴파일
- [x] 정적 리버싱(objdump) + 바이너리 패치로 드래곤 처치
- [x] 동적 분석(gdb) 런타임 메모리 변조로 드래곤 처치

> ⚠️ 모든 작업은 내가 작성·컴파일한 바이너리를 로컬 Docker 컨테이너에서 분석한 것. 교육용 실습.
