# 01 · Cat Homepage (웹해킹)

PHP로 "고양이 홈페이지"를 직접 만들면서, 각 단계에서 생기는 웹 취약점을 **공격(Hacking)** 하고 다시 **방어(Mitigation)** 한다.

## 다루는 취약점

1. **Broken Access Control** — 로그인 안 해도 개인 페이지 접근
2. **Client-side Cookie Poisoning** — 쿠키 변조로 인증 우회
3. **추측 가능한 쿠키값** — 예측 가능한 세션 식별자
4. **Brute Force** — Python `requests` / Burp Suite 무차별 대입
5. **Session Hijacking** — 세션 탈취
6. **SQL Injection** — 로그인 쿼리 우회, 그리고 Prepared Statement 방어

## 실행 (Docker)

과정 원문 기준 환경:

```bash
# Ubuntu 컨테이너 생성 (호스트 8000 ↔ 컨테이너 8000)
docker run -dit --name php_cat_homepage -p 8000:8000 ubuntu:22.04

# 컨테이너 접속 후 PHP 설치
docker exec -it php_cat_homepage bash
apt update && apt install -y php

# src 를 컨테이너로 복사하거나 마운트한 뒤, 그 디렉토리에서
php -S 0.0.0.0:8000
```

호스트 브라우저에서 `http://localhost:8000` 접속.

> 참고용 완성 코드(원문 링크):
> - grape942/php_cat_homepage_tutorial
> - aptheparker/cat-homepage
> - KiminSon/cat-homepage

## 진행 현황

- [x] 1. HTML 로그인 화면 (`src/index.html`)
- [ ] 2. 엔드포인트 / form 로그인
- [ ] 3. Broken Access Control (공격→방어)
- [ ] 4. Cookie 로그인 / Cookie Poisoning
- [ ] 5. Cookie 인증 / 추측 가능한 쿠키값
- [ ] 6. Brute Force (requests.py / Burp Suite)
- [ ] 7. Session / Session Hijacking
- [ ] 8. Database / SQL Injection / 방어

---

## 1. HTML 로그인 화면

`src/index.html` — header / form(username·password·submit) / footer 로 구성된 로그인 화면.
`type="password"` 로 비밀번호를 `***` 처리, `<style>` 로 꾸밈.

아직 **기능은 없는 정적 화면**이다. 다음 단계부터 PHP로 로그인 처리를 붙이면서 취약점이 생긴다.
