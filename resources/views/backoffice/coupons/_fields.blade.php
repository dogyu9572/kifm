{{-- 변수: $coupon, $selectedCategories, $categoryLabels --}}

<h3 class="board-form-section-title">기본 정보</h3>

<div class="board-form-group">
    <label for="coupon_name" class="board-form-label">쿠폰명 <span class="required">*</span></label>
    <input type="text" name="coupon_name" id="coupon_name" class="board-form-control board-form-control--max-md"
        value="{{ old('coupon_name', optional($coupon)->coupon_name ?? '') }}" maxlength="200" required>
    @error('coupon_name')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label for="coupon_code" class="board-form-label">쿠폰 코드 <span class="required">*</span></label>
    <div class="bo-coupon-code-row">
        <input type="text" name="coupon_code" id="coupon_code" class="board-form-control board-form-control--coupon-code"
            value="{{ old('coupon_code', optional($coupon)->coupon_code ?? '') }}" maxlength="50" required
            pattern="[A-Z0-9_-]+" title="영문 대문자, 숫자, -, _">
        <button type="button" class="btn btn-outline-secondary" id="bo-coupon-generate-btn">
            자동 생성
        </button>
    </div>
    <small class="board-form-text">직접 입력하거나 자동 생성 버튼을 클릭하세요. 저장 시 중복 여부를 확인합니다.</small>
    @error('coupon_code')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<h3 class="board-form-section-title">적용 조건</h3>

<div class="board-form-group">
    <span class="board-form-label">적용 결제 항목 <span class="required">*</span></span>
    <div class="board-payment-checkboxes">
        @foreach ($categoryLabels as $code => $label)
            <label class="checkbox-label">
                <input type="checkbox" name="payment_categories[]" value="{{ $code }}" class="bo-coupon-category-checkbox"
                    @checked(in_array($code, $selectedCategories, true))>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
    @error('payment_categories')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<h3 class="board-form-section-title">할인 설정</h3>

<div class="board-form-group">
    <span class="board-form-label">할인 방식 <span class="required">*</span></span>
    <div class="board-radio-group" id="bo-discount-type-group">
        <label class="board-radio-item">
            <input type="radio" name="discount_type" value="FIXED" class="bo-discount-type-radio"
                @checked(old('discount_type', optional($coupon)->discount_type ?? 'FIXED') === 'FIXED')>
            <span>정액 할인</span>
        </label>
        <label class="board-radio-item">
            <input type="radio" name="discount_type" value="RATE" class="bo-discount-type-radio"
                @checked(old('discount_type', optional($coupon)->discount_type ?? '') === 'RATE')>
            <span>정률 할인</span>
        </label>
    </div>
    @error('discount_type')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div id="bo-discount-fixed-panel" class="bo-discount-panel">
    <div class="board-form-group">
        <label for="discount_amount" class="board-form-label">할인 금액 (원) <span class="required">*</span></label>
        <input type="number" name="discount_amount" id="discount_amount" class="board-form-control board-form-control--max-xs" min="0" step="1"
            value="{{ old('discount_amount', (optional($coupon)->discount_type === 'FIXED' ? optional($coupon)->discount_value : null)) }}">
        @error('discount_amount')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div id="bo-discount-rate-panel" class="bo-discount-panel bo-hidden">
    <div class="board-form-group">
        <label for="discount_rate" class="board-form-label">할인율 (%) <span class="required">*</span></label>
        <input type="number" name="discount_rate" id="discount_rate" class="board-form-control board-form-control--max-xs" min="1" max="100" step="1"
            value="{{ old('discount_rate', (optional($coupon)->discount_type === 'RATE' ? optional($coupon)->discount_value : null)) }}">
        @error('discount_rate')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<h3 class="board-form-section-title">유효기간</h3>

<div class="board-form-group">
    <label for="valid_from" class="board-form-label">시작일 <span class="required">*</span></label>
    <input type="date" name="valid_from" id="valid_from" class="board-form-control board-form-control--max-xs"
        value="{{ old('valid_from', optional($coupon)->valid_from?->format('Y-m-d')) }}">
    @error('valid_from')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label for="valid_to" class="board-form-label">종료일 <span class="required">*</span></label>
    <input type="date" name="valid_to" id="valid_to" class="board-form-control board-form-control--max-xs"
        value="{{ old('valid_to', optional($coupon)->valid_to?->format('Y-m-d')) }}">
    @error('valid_to')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<h3 class="board-form-section-title">사용 설정</h3>

<div class="board-form-group">
    <span class="board-form-label">사용 여부 <span class="required">*</span></span>
    <div class="board-radio-group">
        <label class="board-radio-item">
            <input type="radio" name="status" value="ACTIVE"
                @checked(old('status', optional($coupon)->status ?? 'ACTIVE') === 'ACTIVE')>
            <span>사용</span>
        </label>
        <label class="board-radio-item">
            <input type="radio" name="status" value="INACTIVE"
                @checked(old('status', optional($coupon)->status ?? '') === 'INACTIVE')>
            <span>미사용</span>
        </label>
    </div>
    @error('status')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>
