<div class="board-form-group">
    <label class="board-form-label">그룹 선택 <span class="required">*</span></label>
    <div class="board-options-list board-options-horizontal">
        <div class="board-option-item">
            <input type="radio" id="group_no_1" name="group_no" value="1" @checked((string) old('group_no', (int) ($societyExecutive->group_no ?? 1)) === '1')>
            <label for="group_no_1">1그룹</label>
        </div>
        <div class="board-option-item">
            <input type="radio" id="group_no_2" name="group_no" value="2" @checked((string) old('group_no', (int) ($societyExecutive->group_no ?? 1)) === '2')>
            <label for="group_no_2">2그룹</label>
        </div>
        <div class="board-option-item">
            <input type="radio" id="group_no_3" name="group_no" value="3" @checked((string) old('group_no', (int) ($societyExecutive->group_no ?? 1)) === '3')>
            <label for="group_no_3">3그룹</label>
        </div>
    </div>
    <small class="board-form-text">1그룹은 최상단, 3그룹은 하단 영역에 노출됩니다.</small>
</div>

<div class="board-form-group">
    <label for="name" class="board-form-label">이름 <span class="required">*</span></label>
    <input type="text" class="board-form-control" id="name" name="name" value="{{ old('name', $societyExecutive->name ?? '') }}" maxlength="100" required>
</div>

<div class="board-form-group">
    <label for="position" class="board-form-label">직책 <span class="required">*</span></label>
    <input type="text" class="board-form-control" id="position" name="position" value="{{ old('position', $societyExecutive->position ?? '') }}" maxlength="100" required>
</div>

<div class="board-form-group">
    <label for="organization" class="board-form-label">소속 <span class="required">*</span></label>
    <input type="text" class="board-form-control" id="organization" name="organization" value="{{ old('organization', $societyExecutive->organization ?? '') }}" maxlength="200" required>
</div>

<div class="board-form-group">
    <label for="email" class="board-form-label">이메일</label>
    <input type="email" class="board-form-control" id="email" name="email" value="{{ old('email', $societyExecutive->email ?? '') }}" maxlength="150">
</div>

<div class="board-form-group">
    <label for="sort_order" class="board-form-label">정렬 순서</label>
    <input type="number" class="board-form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $societyExecutive->sort_order ?? 1) }}" min="1">
    <small class="board-form-text">같은 그룹 내에서 숫자가 높을수록 상단에 배치됩니다.</small>
</div>

<div class="board-form-group" id="photo-upload-group">
    <label for="photo" class="board-form-label">사진 업로드</label>
    <div class="board-file-upload">
        <div class="board-file-input-wrapper">
            <input type="file" class="board-file-input" id="photo" name="photo" accept=".jpg,.jpeg,.png,.gif">
            <div class="board-file-input-content">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                <span class="board-file-input-subtext">이미지 1개, 5MB 이하 (JPG, PNG, GIF)</span>
            </div>
        </div>

        @if (!empty($societyExecutive?->photo_path))
            <div class="board-existing-files" id="existingPhotoWrapper">
                <div class="board-attachment-list">
                    <div class="board-attachment-item existing-file" data-index="0">
                        <input type="hidden" name="existing_photo" value="{{ $societyExecutive->photo_path }}">
                        <i class="fas fa-file"></i>
                        <a href="{{ asset('storage/' . $societyExecutive->photo_path) }}" target="_blank" rel="noopener">
                            <span class="board-attachment-name">{{ basename($societyExecutive->photo_path) }}</span>
                        </a>
                        <button type="button" class="board-attachment-remove" data-existing-photo-remove="1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="board-file-preview" id="photoPreview"></div>
    </div>
    <small class="board-form-text">1그룹 선택 시에만 사용자 페이지에 사진이 노출됩니다.</small>
</div>

<div class="board-form-group">
    <label for="note" class="board-form-label">비고</label>
    <textarea class="board-form-control board-form-textarea" id="note" name="note" rows="6">{{ old('note', $societyExecutive->note ?? '') }}</textarea>
    <small class="board-form-text">비고는 관리자 내부 메모로만 사용됩니다.</small>
</div>

<div class="board-form-group">
    <label class="board-form-label">사용 여부 <span class="required">*</span></label>
    <div class="board-options-list board-options-horizontal">
        <div class="board-option-item">
            <input type="radio" id="is_active_1" name="is_active" value="1" @checked((string) old('is_active', (int) ($societyExecutive->is_active ?? true)) === '1')>
            <label for="is_active_1">사용</label>
        </div>
        <div class="board-option-item">
            <input type="radio" id="is_active_0" name="is_active" value="0" @checked((string) old('is_active', (int) ($societyExecutive->is_active ?? true)) === '0')>
            <label for="is_active_0">미사용</label>
        </div>
    </div>
</div>
