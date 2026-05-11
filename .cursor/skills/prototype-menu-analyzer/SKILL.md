---
name: prototype-menu-analyzer
description: 관리자 메뉴 개발 전에 prototype URL, specs, prototype views, 현재 Laravel 코드를 매핑해 선분석한다. Use when 사용자가 특정 메뉴(예: 회원관리, 배너관리, 학술행사)를 만들거나 수정하라고 요청할 때.
---

# Prototype Menu Analyzer

## 목적
특정 관리자 메뉴 요청 시, 구현 전에 기준 산출물을 자동으로 읽고 갭 분석을 만든다.

## 입력
- 메뉴명 또는 페이지명 (예: 회원관리, member-list)

## 참조 우선순위
1. Prototype URL  
   - `https://kifm.hk-test.co.kr/prototype/{menu-page}.html`
2. 명세  
   - `docs/planning/admin-prototype-main/docs/specs/{category}`
3. 프로토타입 코드  
   - `docs/planning/admin-prototype-main/views/pages/{prototype-dir}`
4. 현재 구현 코드  
   - `app/Http/Controllers/Backoffice`
   - `resources/views/backoffice`
   - `public/js/backoffice`

## 메뉴 매핑표 (기본)
- 회원관리
  - page: `member-list`
  - specs: `04-member`
  - views: `member`
- 관리자관리
  - page: `admin-list`
  - specs: `02-settings`
  - views: `admin`
- 배너관리
  - page: `banner-list`
  - specs: `15-banner`
  - views: `homepage`
- 팝업관리
  - page: `popup-list`
  - specs: `15-banner`
  - views: `homepage`
- 학술행사 목록
  - page: `event-list`
  - specs: `07-event`
  - views: `event`
- 쿠폰관리
  - page: `coupon-list`
  - specs: `05-payment`
  - views: `payment/coupon`

## 실행 절차
1. 메뉴명을 매핑표로 해석한다. (없으면 가장 가까운 specs 카테고리를 추정하고 사용자 확인)
2. Prototype 화면/명세/프로토타입 뷰를 읽어 요구사항을 정리한다.
3. 현재 Laravel 구현과 비교해 `기구현/부분구현/미구현`으로 분류한다.
4. 수정 범위를 최소화해 구현 계획을 세운다.
5. 구현 후 요구사항 체크리스트로 누락을 확인한다.

## 출력 형식
- 분석 결과
  - 화면 요구사항
  - 데이터/상태/권한 요구사항
  - 갭 분석 (`기구현/부분구현/미구현`)
- 구현 계획
  - 수정 파일 목록
  - 구현 순서 (백엔드 → 뷰 → JS → 검증)
- 확인 필요 항목
  - 모호한 요구사항
  - 범위 확장 필요 여부

## 주의사항
- 명세와 충돌하는 임의 UI/동작 추가 금지
- 사용자 요청 범위 밖 파일 자동 수정 금지
- 위험 변경(DB 구조, 권한 정책, 운영 영향)은 구현 전 확인
