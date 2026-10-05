# 01 · Cat Homepage (웹해킹)

PHP로 "고양이 홈페이지"를 직접 만들면서, 각 단계에서 생기는 웹 취약점을 **공격(Hacking)** 하고 다시 **방어(Mitigation)** 한다.

## 다루는 취약점

1. **Broken Access Control** — 로그인 안 해도 개인 페이지 접근 (+ OR 로직 결함)
2. **Client-side Cookie Poisoning** — 쿠키 변조로 인증 우회
3. **추측 가능한 쿠키값** — 예측 가능한 세션 식별자
4. **Brute Force** — Python `requests` / Burp Suite 무차별 대입
5. **Session Hijacking** — 세션 탈취
6. **SQL Injection** — 로그인 쿼리 우회, 그리고 Prepared Statement 방어

## 폴더 구조 (단계별 스냅샷)

```
1_html_web/        정적 HTML 로그인 화면
2_endpoint_login/  엔드포인트 + 하이퍼링크 (개인페이지 분리)
3_form_login/      PHP form 로그인  ← 취약점 ③ Broken Access Control
...                (단계가 진행되며 폴더 추가)
exploits/          각 공격의 writeup + PoC 스크립트
```

## 실행 / 개발 루프 (Docker)

경로에 공백·한글·괄호가 있어 볼륨 마운트 대신 **파일 복사 방식**을 쓴다.

```bash
# (최초 1회) PHP 컨테이너 생성 — 호스트 8000 ↔ 컨테이너 8000
MSYS_NO_PATHCONV=1 docker run -dit --name cat_web -p 8000:8000 -w /app php:8.2-cli bash

# 특정 단계를 배포하고 서버 켜기 (예: 3_form_login)
docker exec cat_web sh -c 'rm -rf /app/*'
docker cp ./3_form_login/. cat_web:/app/
docker exec -d cat_web sh -c 'php -S 0.0.0.0:8000 -t /app'

# 확인
curl -s http://localhost:8000/        # 또는 브라우저에서 http://localhost:8000
```

> 과정 원문: Notion "Hackademy - basic". 참고용 완성 코드:
> grape942/php_cat_homepage_tutorial · aptheparker/cat-homepage · KiminSon/cat-homepage

## 진행 현황

- [x] 1. HTML 로그인 화면 (`1_html_web/`)
- [x] 2. 엔드포인트 / 하이퍼링크 (`2_endpoint_login/`)
- [x] 3. form 로그인 + **Broken Access Control** 공격 → [writeup](exploits/03_broken_access_control.md)
- [ ] 4. Cookie 로그인 / Cookie Poisoning
- [ ] 5. Cookie 인증 / 추측 가능한 쿠키값
- [ ] 6. Brute Force (requests.py / Burp Suite)
- [ ] 7. Session / Session Hijacking
- [ ] 8. Database / SQL Injection / 방어

---

## 1. HTML 로그인 화면 — `1_html_web/`

header / form(username·password·submit) / footer 로 구성된 정적 로그인 화면.
`type="password"`로 비밀번호 마스킹, `<style>`로 꾸밈. **기능은 없는 화면만** 있는 상태.

## 2. 엔드포인트 / 하이퍼링크 — `2_endpoint_login/`

웹사이트의 각 페이지 = 각 파일 = **엔드포인트**. `/`(루트)는 `index`를 부른다.
개인페이지를 별도 파일(`login-success.html`)로 분리하고, `<a href>`로 링크한다.
→ 아직 HTML뿐이라 "로그인한 사람만 보기" 같은 **논리 처리는 불가능**. 그래서 PHP가 필요.

## 3. form 로그인 + Broken Access Control — `3_form_login/`

`index.php`에서 POST로 받은 아이디/비번을 검사하고, 맞으면 `login-success.php`로 보낸다.
로그인 계정은 `grapeuser` / `secret1234`.

여기서 **2개의 취약점**이 드러난다:

- **A. Broken Access Control** — `login-success.php`가 인증 여부를 확인하지 않아,
  로그인 없이 URL 직접 접근만으로 개인정보가 노출된다.
- **B. OR 로직 결함** — 인증 조건이 `아이디==valid OR 비번==valid`라 하나만 맞아도 통과.

실제 Docker에서 재현 결과와 방어책 → **[exploits/03_broken_access_control.md](exploits/03_broken_access_control.md)**
