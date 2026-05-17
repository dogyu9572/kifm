---
name: backoffice-menu-scaffold
description: 백오피스 신규 CRUD 메뉴를 만들거나 기존 메뉴를 KIFM 백오피스 레이아웃, 네이밍, 필터, 테이블, 액션 버튼 기준으로 표준화할 때 사용한다.
---

# 백오피스 메뉴 스캐폴드

## 기본 원칙

- 새 마크업을 만들기 전에 기존 백오피스 CRUD 구조를 먼저 재사용한다.
- `bo-*`, `board-*`, `filter-*`와 기존 공용 클래스를 우선 사용한다.
- 라우트명과 뷰 폴더명은 `kebab-case`로 맞춘다.
- 사용자 승인이 없으면 메뉴별 전용 CSS를 만들지 않는다.

## 작업 순서

1. 범위를 확정한다: index, create, edit, show, withdrawn, 일괄 액션, 엑셀 다운로드.
2. 가장 가까운 기존 백오피스 화면을 찾아 레이아웃을 재사용한다.
3. 라우트, 컨트롤러 메서드, 뷰 폴더, JS endpoint, form action 이름을 서로 맞춘다.
4. 필터는 `board-filter`에 두고, 페이지 단위 액션은 `board-page-buttons`에 둔다.
5. 필터, `per_page`, 페이지네이션 query string을 유지한다.
6. 로직이나 검증이 단순하지 않으면 Service/Form Request 분리를 적용한다.

## 검증

- 라우트/뷰/컨트롤러/action 네이밍이 일치한다.
- 변경한 Blade에 인라인 style/event/script가 없다.
- 버튼 색상이 의미에 맞다: 삭제 `danger`, 다운로드 `secondary`, 생성/등록 `success`.
- 일괄 선택 체크박스 id/class와 JS selector가 일치한다.
- 변경한 PHP 파일은 가능한 경우 구문 검사를 수행한다.
