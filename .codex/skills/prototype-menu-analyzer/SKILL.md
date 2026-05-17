---
name: prototype-menu-analyzer
description: 백오피스 관리자 메뉴를 구현하거나 수정하기 전에 사용한다. prototype URL, spec 문서, prototype view, 현재 Laravel 코드를 비교해 수정 전 갭 분석을 만든다.
---

# Prototype 메뉴 분석

## 입력

- 메뉴명, 라우트, slug, prototype 페이지명 중 하나 이상을 기준으로 분석한다.
- 매핑이 불명확하면 가장 가까운 후보를 추정하되, 수정 전에 사용자에게 확인한다.

## 참조 순서

1. Prototype URL: `https://kifm.hk-test.co.kr/prototype/{menu-page}.html`
2. Specs: `docs/planning/admin-prototype-main/docs/specs/{category}`
3. Prototype views: `docs/planning/admin-prototype-main/views/pages/{prototype-dir}`
4. Current Laravel: `app/Http/Controllers/Backoffice`, `resources/views/backoffice`, `public/js/backoffice`

## 기본 매핑

- 회원관리: `member-list`, `04-member`, `member`
- 관리자관리: `admin-list`, `02-settings`, `admin`
- 배너관리: `banner-list`, `15-banner`, `homepage`
- 팝업관리: `popup-list`, `15-banner`, `homepage`
- 학술행사 목록: `event-list`, `07-event`, `event`
- 쿠폰관리: `coupon-list`, `05-payment`, `payment/coupon`

## 수정 전 출력

- 화면 요구사항
- 데이터/상태/권한 요구사항
- 구현 상태: 기구현/부분구현/미구현
- 최소 수정 계획
- 확인 필요 항목
