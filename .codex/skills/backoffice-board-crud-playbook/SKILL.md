---
name: backoffice-board-crud-playbook
description: KIFM 백오피스 board-posts 작업에 사용한다. 보드 slug, 뷰, BoardSeeder, BoardTemplateSeeder, AdminMenuSeeder, 마이그레이션, BoardPostService 필터를 추가하거나 수정할 때 적용한다.
---

# 백오피스 게시판 CRUD 플레이북

## 사전 확인

1. 보드 slug, 메뉴명, 상위 메뉴, 보드 타입을 확인한다.
2. 수정 전에 prototype/spec/현재 Laravel 구현을 비교한다.
3. 공용 board-posts 확장인지, 커스텀 컨트롤러 흐름인지 결정한다.
4. DB, 마이그레이션, 시더 실행은 데이터 변경 가능성이 있으므로 실행 전에 사용자 확인을 받는다.

## 점검할 파일

- `database/seeders/BoardTemplateSeeder.php`
- `database/seeders/BoardSeeder.php`
- `database/seeders/AdminMenuSeeder.php`
- `database/migrations/*create_board_{slug}_table.php`
- `resources/views/backoffice/board-posts/{slug}/index.blade.php`
- `resources/views/backoffice/board-posts/{slug}/create.blade.php`
- `resources/views/backoffice/board-posts/{slug}/edit.blade.php`
- `resources/views/backoffice/board-posts/{slug}/show.blade.php`
- `app/Services/Backoffice/BoardPostService.php`

## 구현 규칙

- prototype이 다른 확립된 패턴을 요구하지 않으면 기존 notices 계열 레이아웃을 우선 사용한다.
- slug와 테이블 네이밍을 일관되게 유지한다. 보드 테이블명이 필요한 곳은 프로젝트 관례에 맞춰 underscore 규칙을 따른다.
- 사이드바의 `parent_id`, `order`, `is_active`를 일관되게 유지한다.
- 검색/필터 파라미터 이름은 뷰와 서비스에서 동일하게 맞춘다.
- 앨범/썸네일 계열 보드는 업로드, 표시, 삭제 동작을 보존한다.

## 검증

- 변경한 PHP 파일은 `php -l`로 구문 검사를 수행한다.
- 변경한 Blade에서 인라인 style/event/script를 검색한다.
- 메뉴 시더의 계층과 URL을 확인한다.
- 목록 필터, create/edit/show 라우트, 페이지네이션 전제를 확인한다.
- 사용자가 승인하지 않아 마이그레이션/시더를 실행하지 않았다면 최종 보고에 명시한다.
