@php
    $c = $category;
@endphp

<div class="board-form-group">
    <label for="name" class="board-form-label">과목명 <span class="required">*</span></label>
    <input type="text" name="name" id="name" class="board-form-control board-form-control--max-md"
        value="{{ old('name', $c->name ?? '') }}" maxlength="100" required>
    @error('name')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label for="sort_order" class="board-form-label">정렬 순서</label>
    <input type="number" name="sort_order" id="sort_order" class="board-form-control board-form-control--max-xs" min="0" step="1"
        value="{{ old('sort_order', $c->sort_order ?? 0) }}">
    @error('sort_order')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <span class="board-form-label">상태 <span class="required">*</span></span>
    <div class="board-radio-group">
        <label class="board-radio-item">
            <input type="radio" name="status" value="active" @checked(old('status', $c->status ?? 'active') === 'active')>
            <span>사용중</span>
        </label>
        <label class="board-radio-item">
            <input type="radio" name="status" value="inactive" @checked(old('status', $c->status ?? '') === 'inactive')>
            <span>미사용</span>
        </label>
    </div>
    @error('status')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>
