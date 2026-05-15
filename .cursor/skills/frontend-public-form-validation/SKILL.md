---
name: frontend-public-form-validation
description: 사용자 공개(프론트) 폼의 유효성·오류 표시를 필드별 빨간 텍스트·보조 버튼 알럿 패턴으로 맞출 때 사용한다. member·mypage·신청 폼 등 layouts.frontend 계열.
---

# 사용자 공개 폼 유효성 표시 스킬

## 언제 쓰는지
- `resources/views/backoffice`가 **아닌** 사용자 페이지에 **입력 폼**을 새로 만들거나 수정할 때
- 서버 검증(`FormRequest`) 추가·변경 후 **오류 UI를 통일**할 때
- 상단 회색 박스/전체 오류 목록을 **필드별 표시로 바꿀** 때

## 반드시 읽을 규칙
- `.cursor/rules/21-frontend-public-forms-validation.mdc` (본 패턴의 SSOT)
- `.cursor/rules/20-blade-and-assets.mdc` (인라인 금지, JS/CSS 분리)

## 구현 절차
1. **검증 규칙 정리**: `FormRequest::rules()` 키와 커스텀 `withValidator`에서 붙는 속성명을 목록으로 적는다.
2. **상단 일괄 오류 제거**: `@if ($errors->any())` + 전체 루프만 있는 블록이 있으면 삭제한다.
3. **필드별 `@error`**: 각 라벨·입력 묶음 **직후**에 `@error('필드')` … `@enderror`를 둔다. 메시지는 `<p class="c_red" role="alert">` 한 줄 패턴을 기본으로 한다. (프로젝트에 이미 쓰는 동일 유틸이 있으면 그걸로 통일.)
4. **배열 필드**: `foo.*` 오류는 해당 `foo` UI 블록 하단에서만 모은다. Blade에 장문 로직이 필요하면 컨트롤러에서 필드별 메시지 배열만 전달한다.
5. **Fetch/Ajax 보조 버튼**: 성공·실패 안내는 `window.alert`로 통일한다. 서버 폼 검증 실패와 섞지 않는다.
6. **자산**: 화면별 동작은 `public/js/frontend/<화면>.js` 등에 두고 `@push('scripts')`로 로드한다.

## 레퍼런스 구현
- 회원가입: `resources/views/member/register.blade.php` + `public/js/frontend/member-register.js` + `App\Http\Requests\FrontendMemberRegisterRequest`

## 자기검증
- 해당 Blade에서 상단 `@foreach ($errors->all() as …)` 단독 블록 없음
- `style=`, `onclick=`, Blade 내부 `<style>`, `<script>` 없음
- `php -l`로 변경한 PHP 파일 구문 확인
