# Hackademy – Hacking Basic

GRAPE 동아리 교육용 과정 **Hackademy (basic)** 의 Hacking-Basic 파트 실습 기록.
각 챌린지를 직접 만들고/분석하며 취약점을 익히고, 풀이(writeup)와 코드를 남긴다.

> ⚠️ 모든 실습은 로컬 Docker 시뮬레이터 등 **격리된 교육 환경** 안에서만 진행한다.
> 실제 운영 시스템 대상의 행위가 아니며, 교육/방어 목적의 학습 자료다.

## 구성

| 폴더 | 분야 | 내용 |
|------|------|------|
| [`01-cat-homepage`](01-cat-homepage/) | 웹해킹 | PHP 고양이 홈페이지를 직접 만들고, Broken Access Control · Cookie Poisoning · Brute Force · Session Hijacking · SQL Injection 실습 및 방어 |
| [`02-graple-story`](02-graple-story/) | 리버싱 | C로 RPG 게임 제작 → objdump 정적분석 + 바이너리 패치 / gdb 런타임 변조로 게임핵 |
| [`03-photodot`](03-photodot/) | 포너블 | C 이미지필터 앱의 스택 버퍼 오버플로우 → ret2win 으로 hidden() 임의 코드 실행 |
| [`04-quokka-rat`](04-quokka-rat/) | 트로이목마 분석 | RAT·C2·리버스 쉘 **개념**과 무기화 단계 **원리**(코드 없음) + 탐지·방어 문서 — **개념·방어 문서(실습 코드 제외)** |

## 환경

- Docker Desktop (Windows)
- 과정 원문: Notion "Hackademy - basic"

## 진행 현황

- [x] 레포 세팅
- [x] 01 Cat Homepage (웹해킹) — 접근제어·쿠키·브루트포스·세션·SQLi + 방어
- [x] 02 Graple Story (리버싱) — 바이너리 패치 + gdb 런타임 변조로 게임핵
- [x] 03 PhotoDot (포너블) — 스택 버퍼 오버플로우 ret2win 익스플로잇
- [x] 04 Quokka RAT (트로이목마 분석) — 개념·방어 문서 (실습 코드 제외)
