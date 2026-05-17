# KIFM Codex Project Rules

이 파일은 KIFM 프로젝트의 Codex 전용 규칙이다. 기존 작업 규칙을 Codex가 바로 사용할 수 있도록 통합한 원본으로 취급한다.

## 우선순위

- 규칙 충돌 시 `금지 > 우선 > 허용(예외)` 순서로 판단한다.
- 기능 안정성, 기존 동작 보존, 유지보수성을 우선한다.
- 사용자가 지정한 범위 밖의 리팩터링, 문구 변경, UI 변경, 파일 대량 수정은 하지 않는다.
- 질문형 요청, 검토 요청, 의도가 애매한 요청은 설명과 계획을 먼저 제시하고 코드 수정은 보류한다.
- 명시적 실행 요청(`수정해줘`, `적용해줘`, `진행해줘`)이 있고 영향 범위가 작고 명확할 때만 바로 수정한다.
- DB 스키마/데이터, 인증/권한, 공통 레이아웃, 공통 JS, 마이그레이션/시더 실행은 사용자 확인 후 진행한다.

## Laravel 아키텍처

- 컨트롤러는 요청 처리, 서비스 호출, 응답 반환만 담당한다.
- 비즈니스 로직은 `app/Services/`로 분리한다.
- 유효성 검사는 Form Request를 우선 사용한다.
- 모델은 관계, 스코프, 액세서/뮤테이터 중심으로 유지하고 도메인 처리 로직을 넣지 않는다.
- Blade에서 필요한 계산/가공 데이터는 컨트롤러에서 서비스 호출 후 완성된 형태로 전달한다.
- 관계 데이터 조회 시 `with()`를 사용해 N+1을 방지한다.
- 클래스 의존성은 파일 상단 `use`로 import한다.

## Blade, CSS, JS

- Blade는 데이터 표시 전용으로 작성한다.
- Blade 내부 `<style>`, `<script>`, `style=""`, `onclick=""` 사용을 금지한다.
- CSS는 `public/css/`, JS는 `public/js/`에서 관리한다.
- 화면별 동작은 외부 JS 파일로 분리하고, 기존 로딩 패턴을 따른다.
- 유사한 create/edit, index/detail 화면은 공통 partial/component 추출 가능성을 먼저 확인한다.
- 작업 완료 전 변경한 Blade 범위에서 `style=`, `onclick=`, `<style`, `<script` 잔존 여부를 정적 검색한다.

## 백오피스 UI

- 신규/수정 백오피스 화면은 기존 백오피스 CRUD 레이아웃을 우선 재사용한다.
- 기준 레퍼런스는 `resources/views/backoffice/board-posts/notices`의 `index`, `create`, `edit`, `show` 화면이다.
- 목록 보조 레퍼런스는 `resources/views/backoffice/academic-events/index.blade.php`, `resources/views/backoffice/edu-training-payments/index.blade.php`다.
- 목록 화면 기본 순서는 `session 알림 -> board-container -> board-page-header/buttons -> board-card/body -> board-filter -> board-list-header -> table-responsive/board-table -> pagination`이다.
- 필터는 `board-filter` 블록 안에만 둔다.
- 페이지 액션 버튼은 `board-page-buttons`에 둔다.
- 선택 삭제는 `btn btn-danger`, 엑셀 다운로드는 `btn btn-secondary`, 신규/등록/생성은 `btn btn-success`를 기본으로 한다.
- 기존 CSS 클래스와 컴포넌트 구조를 재사용하고 임의의 신규 디자인 패턴을 만들지 않는다.
- 사용자 요청 없이 화면 구성, 간격, 버튼 스타일, 색상, 문구를 바꾸지 않는다.
- UI 수정 요청은 먼저 기준 화면/파일과 수정 대상, 수정하지 않을 대상을 분명히 한다.

## 백오피스 네이밍과 CRUD

- 신규 백오피스 공용 클래스/셀렉터 접두사는 `bo-`를 기본으로 한다.
- 공용 레이아웃 클래스에 `member-*`, `post-*`, `popup-*` 같은 도메인 종속 접두사를 쓰지 않는다.
- 라우트 경로명과 `resources/views/backoffice` 하위 폴더명은 `kebab-case`로 일치시킨다.
- 신규 메뉴마다 전용 CSS 파일을 만들지 말고 `public/css/backoffice/backoffice-crud.css`, `boards.css` 등 기존 공용 CSS를 우선 사용한다.
- 테이블 선택 체크박스는 `bo-row-checkbox`, 인라인 폼은 `bo-inline-form`을 기본으로 한다.

## 사용자 공개 화면

- 사용자 공개 화면은 디자인, 문구, 항목, DOM 순서, 클래스 조합을 임의 변경하지 않는다.
- 허용되는 변경은 사용자가 명시한 범위 또는 기존 하드코딩 위치를 같은 마크업 구조로 DB/API 데이터에 연결하는 경우다.
- 폼 검증 오류는 각 입력 컨트롤 바로 아래에 `@error('field')`로 표시한다.
- 상단 전체 오류 목록만으로 처리하지 않는다.
- 오류 문구는 기존 프론트 유틸 클래스(예: `c_red`)를 우선 사용한다.
- 보조 비동기 액션 결과는 프로젝트 기존 패턴 또는 `window.alert`를 사용한다.
- 휴대폰 번호 저장/중복 비교는 숫자만 사용하고, 화면 표시는 외부 JS에서 하이픈 표시를 처리한다.
- 사용자 공개 목록 페이지네이션은 1페이지 또는 0건이어도 렌더링한다.
- 프론트 목록에서 `hasPages()`로 페이지네이션 전체를 숨기지 않는다.

## 관리자 메뉴/게시판 선분석

- 백오피스 메뉴 개발 전 prototype URL, 명세, prototype view, 현재 Laravel 구현을 비교한다.
- 기본 참조 경로:
  - Prototype URL: `https://kifm.hk-test.co.kr/prototype/{menu-page}.html`
  - Specs: `docs/planning/admin-prototype-main/docs/specs/{category}`
  - Prototype views: `docs/planning/admin-prototype-main/views/pages/{prototype-dir}`
  - Laravel: `app/Http/Controllers/Backoffice`, `resources/views/backoffice`, `public/js/backoffice`
- 구현 전 `화면 요소/동작`, `데이터/상태/권한`, `기구현/부분구현/미구현`, `수정 범위`를 정리한다.
- 신규 보드 slug는 underscore 규칙을 사용한다.
- 신규 게시판/메뉴는 필요 시 `BoardTemplateSeeder`, `BoardSeeder`, `AdminMenuSeeder`, `create_board_{slug}_table`, board-posts 뷰, `BoardPostService` 필터 분기를 함께 점검한다.

## 마이그레이션과 시더

- 테이블/컬럼은 소문자와 언더스코어를 사용한다.
- 기존 CREATE 마이그레이션을 기준으로 최종 스키마를 관리하고 MODIFY/ADD 성격 파일 남발을 피한다.
- `migrate:reset`, `migrate:fresh`, `migrate:rollback`은 기본 금지한다.
- 마이그레이션/시더 실행 전 사용자 확인을 받는다.
- 기존 데이터가 있는 환경에서는 파일 수정과 실행을 분리해서 보고한다.

## 개발 환경

- 터미널 명령은 WSL(Ubuntu) 환경 기준으로 실행한다.
- PHP/Composer/Artisan 명령은 Docker 컨테이너 내부 실행을 우선 검토한다.
- Laravel Sail + Docker + MySQL 8.0, `utf8mb4`, `STRICT_TRANS_TABLES` 기준을 따른다.
- DB 연결 정보는 `.env` 기준으로 해석한다.

## Codex Skills

아래 Codex 스킬은 `.codex/skills/*/SKILL.md`에 있다. 작업 유형이 맞으면 해당 스킬을 먼저 읽고 절차를 따른다.

- `backoffice-board-crud-playbook`: 백오피스 게시판/board-posts/시더/마이그레이션 동시 작업
- `backoffice-menu-scaffold`: 백오피스 신규 메뉴 CRUD 스캐폴딩
- `backoffice-self-verification-harness`: 백오피스 변경 후 자기검증
- `frontend-public-form-validation`: 사용자 공개 폼 유효성, 휴대폰, 오류 표시
- `laravel-refactor-checklist`: Laravel 계층 분리 리팩터링
- `prototype-menu-analyzer`: 관리자 메뉴 prototype/spec/current code 선분석
- `rules-conflict-audit`: Codex 규칙과 스킬 충돌 감사

## Codex Subagents

- 서브에이전트 정의는 `.codex/subagents/README.md`에 둔다.
- 현재 프로젝트 표준 역할은 `rule-auditor`, `laravel-layer-reviewer`, `harness-checker`다.
- Codex에서는 사용자가 명시적으로 서브에이전트 또는 병렬 에이전트 작업을 요청한 경우에만 spawn_agent를 사용한다.
- 파일 4개 이상, 계층 다중 변경, DB/인증/권한/공통 레이아웃/공통 JS, 퍼블리셔 산출물 포함 작업은 서브에이전트 사용을 제안한다.

## 변경 완료 전 점검

- 인라인 코드 점검: `style=`, `onclick=`, `<style`, `<script`
- 연결 점검: route 이름, 컨트롤러 메서드, JS endpoint URL
- 네이밍 점검: 공용 레이아웃 클래스에 도메인 종속 접두사 없음
- 영향 점검: 기존 메뉴 경로, 버튼 동작, 필터 유지, 페이지네이션
- PHP 파일은 가능한 경우 `php -l`로 구문 검증한다.

## 최종 보고

최종 보고에는 아래를 포함한다.

- 적용한 정리 항목
- 실행한 검증 목록과 결과
- 실패 또는 미실행 항목과 사유
- 남은 위험/추가 점검 필요 항목
