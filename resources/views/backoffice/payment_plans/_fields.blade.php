{{-- 결제 항목 폼 필드 (create/edit 공통). 변수: $plan, $selectedGrades, $selectedTypes, $categoryLabels, $gradeLabels, $memberTypeLabels --}}

<h3 class="board-form-section-title">기본 정보</h3>

<div class="board-form-group">
    <label for="plan_name" class="board-form-label">결제항목명 <span class="required">*</span></label>
    <input type="text" name="plan_name" id="plan_name" class="board-form-control"
        value="{{ old('plan_name', optional($plan)->plan_name ?? '') }}" maxlength="200" required>
    @error('plan_name')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label for="bo-payment-category" class="board-form-label">결제 항목 <span class="required">*</span></label>
    <select name="category" id="bo-payment-category" class="board-form-control board-form-control--max-md" required>
        <option value="">선택하세요</option>
        @foreach ($categoryLabels as $code => $label)
            <option value="{{ $code }}" @selected(old('category', optional($plan)->category ?? '') === $code)>{{ $label }}</option>
        @endforeach
    </select>
    @error('category')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<h3 class="board-form-section-title">적용 대상</h3>

<div class="board-form-group">
    <span class="board-form-label">회원 여부 <span class="required">*</span></span>
    <div class="board-radio-group" id="bo-member-status-group">
        <label class="board-radio-item">
            <input type="radio" name="member_status" value="member" class="bo-member-status-radio"
                @checked(old('member_status', optional($plan)->member_status ?? 'member') === 'member')>
            <span>회원</span>
        </label>
        <label class="board-radio-item">
            <input type="radio" name="member_status" value="non-member" class="bo-member-status-radio"
                @checked(old('member_status', optional($plan)->member_status ?? '') === 'non-member')>
            <span>비회원</span>
        </label>
    </div>
    @error('member_status')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div id="bo-member-only-fields"
    class="bo-member-only-block{{ old('member_status', optional($plan)->member_status ?? 'member') === 'non-member' ? ' bo-hidden' : '' }}">
    <div class="board-form-group">
        <span class="board-form-label">회원 등급 <span class="required">*</span></span>
        <div class="board-payment-checkboxes">
            @foreach ($gradeLabels as $code => $label)
                <label class="checkbox-label">
                    <input type="checkbox" name="grades[]" value="{{ $code }}" class="bo-grade-checkbox"
                        @checked(in_array($code, $selectedGrades, true))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('grades')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="board-form-group">
        <span class="board-form-label">구분 <span class="required">*</span></span>
        <div class="board-payment-checkboxes">
            @foreach ($memberTypeLabels as $code => $label)
                <label class="checkbox-label">
                    <input type="checkbox" name="member_types[]" value="{{ $code }}" class="bo-type-checkbox"
                        @checked(in_array($code, $selectedTypes, true))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('member_types')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="board-form-group">
        <span class="board-form-label">임원 여부 <span class="required">*</span></span>
        <div class="board-radio-group">
            <label class="board-radio-item">
                <input type="radio" name="executive" value="executive"
                    @checked(old('executive', optional($plan)->executive ?? '') === 'executive')>
                <span>임원</span>
            </label>
            <label class="board-radio-item">
                <input type="radio" name="executive" value="non-executive"
                    @checked(old('executive', optional($plan)->executive ?? 'non-executive') === 'non-executive')>
                <span>임원 아님</span>
            </label>
        </div>
        @error('executive')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<h3 class="board-form-section-title">결제 금액</h3>

<div id="bo-amount-placeholder" class="bo-amount-panel bo-muted-text board-form-group">
    결제 항목을 선택하면 금액 입력란이 표시됩니다.
</div>

<div id="bo-amount-conference" class="bo-amount-panel bo-hidden">
    <div class="board-form-group">
        <label for="price_early" class="board-form-label">사전등록비 (원) <span class="required">*</span></label>
        <input type="number" name="price_early" id="price_early" class="board-form-control board-form-control--max-xs" min="0" step="1"
            value="{{ old('price_early', optional($plan)->price_early ?? '') }}">
        @error('price_early')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
    <div class="board-form-group">
        <label for="price_site" class="board-form-label">현장등록비 (원) <span class="required">*</span></label>
        <input type="number" name="price_site" id="price_site" class="board-form-control board-form-control--max-xs" min="0" step="1"
            value="{{ old('price_site', optional($plan)->price_site ?? '') }}">
        @error('price_site')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div id="bo-amount-single" class="bo-amount-panel bo-hidden">
    <div class="board-form-group">
        <label for="price" class="board-form-label">금액 (원) <span class="required">*</span></label>
        <input type="number" name="price" id="price" class="board-form-control board-form-control--max-xs" min="0" step="1"
            value="{{ old('price', optional($plan)->price ?? '') }}">
        @error('price')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<h3 class="board-form-section-title">사용 설정</h3>

<div class="board-form-group">
    <span class="board-form-label">사용 여부 <span class="required">*</span></span>
    <div class="board-radio-group">
        <label class="board-radio-item">
            <input type="radio" name="use_status" value="active"
                @checked(old('use_status', optional($plan)->use_status ?? 'active') === 'active')>
            <span>사용</span>
        </label>
        <label class="board-radio-item">
            <input type="radio" name="use_status" value="inactive"
                @checked(old('use_status', optional($plan)->use_status ?? '') === 'inactive')>
            <span>미사용</span>
        </label>
    </div>
    @error('use_status')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>
