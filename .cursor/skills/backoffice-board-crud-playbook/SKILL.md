---
name: backoffice-board-crud-playbook
description: 백오피스 신규/수정 게시판 메뉴를 prototype/spec 기준으로 구현하고 시더·마이그레이션·검증까지 일괄 처리한다. 게시판 메뉴 추가, board-posts slug 확장, AdminMenu/BoardSeeder/BoardTemplateSeeder 동시 반영이 필요할 때 사용한다.
---

# Backoffice Board CRUD Playbook

## 목적
백오피스 게시판 작업을 분석부터 실행/검증까지 동일한 절차로 수행한다.

## 입력
- 메뉴명/slug
- prototype URL
- 관련 spec 경로(또는 키워드)

## 실행 순서
1. **요구사항 분석**
   - prototype/spec/ejs/현재 코드 비교
   - `기구현/부분구현/미구현`으로 정리

2. **데이터/연결 반영**
   - `BoardTemplateSeeder` 템플릿 추가
   - `BoardSeeder` 보드 추가
   - `AdminMenuSeeder` 메뉴 추가(계층/순서 점검)
   - `create_board_{slug}_table` 마이그레이션 추가

3. **뷰 구성**
   - `resources/views/backoffice/board-posts/{slug}/index|create|edit|show.blade.php`
   - notices 레이아웃 패턴 우선 재사용
   - 앨범 계열은 `thumbnail` 노출/업로드 포함

4. **서비스 분기(필요 시)**
   - `BoardPostService` 필터 키 확장
   - 목록 검색 파라미터와 form name 동기화

5. **검증**
   - `php -l` 변경 PHP 파일
   - 린트 점검
   - 인라인 금지 패턴 점검:
     - `style=`
     - `onclick=`
     - `<style`
     - `<script`
   - 필요 시 실행:
     - `php artisan migrate --force`
     - `php artisan db:seed --class=BoardTemplateSeeder --force`
     - `php artisan db:seed --class=BoardSeeder --force`
     - `php artisan db:seed --class=AdminMenuSeeder --force`
     - `php artisan optimize:clear`

## 메뉴 정합성 체크
- `url`이 있는 메뉴는 보통 자식을 렌더하지 않는 경우가 있으므로 `parent_id` 설계를 먼저 확인한다.
- 중복 메뉴는 즉시 정리:
  - 삭제가 부담되면 `is_active=0`로 비활성
  - 최종 트리에서 사용자 노출 기준으로 `order` 조정

## 실패 시 트러블슈팅
- **첨부 500**
  - `storage`/`bootstrap/cache` 권한 확인
  - `config/view.php`의 compiled path 확인
  - 컨트롤러 try-catch + 로그로 원인 노출
- **DB 접속 실패(SQLSTATE[HY000][2002])**
  - 샌드박스/권한 이슈 가능성 우선 확인
  - 필요 시 권한 확장으로 명령 재실행
- **시더 반영 불일치**
  - `AdminMenuSeeder`의 id/parent_id/order/is_active 재점검
  - 시더 실행 후 캐시 정리

## 출력 형식
- 분석 결과
- 수정 파일 목록
- 실행한 명령과 결과
- 남은 리스크/후속 점검
