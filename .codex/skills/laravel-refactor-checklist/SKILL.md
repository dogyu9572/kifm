---
name: laravel-refactor-checklist
description: Laravel 리팩터링에 사용한다. 컨트롤러 경량화, 서비스 분리, Form Request 정리, 쿼리 구조화, 모델 책임 정리, Blade 로직 제거 작업에 적용한다.
---

# Laravel 리팩터링 체크리스트

## 리팩터링 순서

1. 요청 범위를 고정하고 관련 없는 정리는 하지 않는다.
2. 컨트롤러에서 요청/응답 orchestration이 아닌 로직을 찾는다.
3. 비즈니스 로직은 기존 또는 신규 Service로 옮긴다.
4. 요청 구조나 검증 규칙이 단순하지 않으면 Form Request로 옮긴다.
5. 모델은 관계, scope, cast, accessor/mutator 중심으로 유지한다.
6. Blade는 표시 역할에 집중시키고, 필요한 값은 미리 계산해 전달한다.
7. 사용자 요청이 없으면 라우트명, query string, redirect, flash message를 보존한다.

## 위험 점검

- N+1 위험: 관계 데이터를 렌더링하면 필요한 `with()`를 확인한다.
- 동작 변화: 기존 input key, output key, redirect 대상, error key를 비교한다.
- 부분 수정: 요청에 없는 optional field를 null로 덮어쓰지 않는다.

## 검증

- 변경한 PHP 파일은 `php -l`로 구문 검사를 수행한다.
- 영향받은 라우트/뷰의 네이밍 불일치를 확인한다.
- 실행하지 못한 테스트와 그 테스트가 커버할 위험을 보고한다.
