@php
    $isEdit = $master->exists;
    $requireLogo = $requireLogo ?? ! $isEdit;
@endphp

<div class="board-form-group">
    <label class="board-form-label" for="sponsor_name">스폰서명 <span class="required">*</span></label>
    <input type="text" name="name" id="sponsor_name" class="board-form-control board-form-control--max-lg" value="{{ old('name', $master->name) }}" maxlength="100" required placeholder="스폰서명을 입력하세요">
</div>

<div class="board-form-group">
    <span class="board-form-label">상태 <span class="required">*</span></span>
    <div class="board-radio-group bo-radio-group-wrap">
        <div class="board-radio-item">
            <input type="radio" id="status_active" name="status" value="active" class="board-radio-input" @checked(old('status', $master->status ?? 'active') === 'active')>
            <label for="status_active">활성화</label>
        </div>
        <div class="board-radio-item">
            <input type="radio" id="status_inactive" name="status" value="inactive" class="board-radio-input" @checked(old('status', $master->status ?? 'active') === 'inactive')>
            <label for="status_inactive">비활성화</label>
        </div>
    </div>
</div>

<div class="board-form-group">
    <label for="logo" class="board-form-label">스폰서 로고 업로드 @if (! $isEdit)<span class="required">*</span>@endif</label>
    <div class="board-file-upload">
        <div class="board-file-input-wrapper">
            <input type="file" class="board-file-input" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif" @if ($requireLogo) required @endif>
            <div class="board-file-input-content">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                <span class="board-file-input-subtext">이미지 1개, 5MB 이하 (JPG, PNG, GIF)</span>
            </div>
        </div>

        @if (! empty($master?->logo_path))
            <div class="board-existing-files" id="existingLogoWrapper">
                <div class="board-attachment-list">
                    <div class="board-attachment-item existing-file" data-index="0">
                        <input type="hidden" name="existing_logo" value="{{ $master->logo_path }}">
                        <i class="fas fa-file"></i>
                        <a href="{{ asset('storage/' . $master->logo_path) }}" target="_blank" rel="noopener">
                            <span class="board-attachment-name">{{ basename($master->logo_path) }}</span>
                        </a>
                        <button type="button" class="board-attachment-remove" data-existing-logo-remove="1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="board-file-preview" id="logoPreview"></div>
    </div>
    <small class="board-form-text">권장 사이즈: 300×100px (JPG, PNG, GIF)</small>
    @error('logo')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>
