@php
    $isEdit = $hotel->exists;
@endphp

<div class="board-form-group">
    <label class="board-form-label" for="hotel_name">숙소명 <span class="required">*</span></label>
    <input type="text" name="name" id="hotel_name" class="board-form-control board-form-control--max-lg" value="{{ old('name', $hotel->name) }}" maxlength="100" required placeholder="숙소명을 입력하세요">
</div>

<div class="board-form-group">
    <span class="board-form-label">상태 <span class="required">*</span></span>
    <div class="board-radio-group bo-radio-group-wrap">
        <div class="board-radio-item">
            <input type="radio" id="hotel_status_active" name="status" value="active" class="board-radio-input" @checked(old('status', $hotel->status ?? 'active') === 'active')>
            <label for="hotel_status_active">활성화</label>
        </div>
        <div class="board-radio-item">
            <input type="radio" id="hotel_status_inactive" name="status" value="inactive" class="board-radio-input" @checked(old('status', $hotel->status ?? 'active') === 'inactive')>
            <label for="hotel_status_inactive">비활성화</label>
        </div>
    </div>
</div>

<div class="board-form-group">
    <label class="board-form-label" for="hotel_phone">연락처 <span class="required">*</span></label>
    <input type="text" name="phone" id="hotel_phone" class="board-form-control board-form-control--max-md" value="{{ old('phone', $hotel->phone) }}" maxlength="30" required placeholder="예: 02-1234-5678">
</div>

<div class="board-form-group">
    <label class="board-form-label" for="hotel_distance">거리 (학술행사장 기준)</label>
    <input type="text" name="distance" id="hotel_distance" class="board-form-control board-form-control--max-lg" value="{{ old('distance', $hotel->distance) }}" maxlength="100" placeholder="예: 도보 5분">
</div>

<div class="board-form-group">
    <label class="board-form-label" for="bo-hotel-address">주소 <span class="required">*</span></label>
    <div class="input-with-button">
        <input type="text" name="address" id="bo-hotel-address" class="board-form-control" value="{{ old('address', $hotel->address) }}" maxlength="255" required readonly placeholder="주소 검색 버튼을 눌러 입력하세요">
        <button type="button" class="btn btn-secondary btn-sm" id="bo-hotel-address-search">주소 검색</button>
    </div>
    @error('address')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label class="board-form-label" for="bo-hotel-address-detail">상세 주소</label>
    <input type="text" name="address_detail" id="bo-hotel-address-detail" class="board-form-control board-form-control--max-lg" value="{{ old('address_detail', $hotel->address_detail) }}" maxlength="255" placeholder="동·호수 등">
    @error('address_detail')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label class="board-form-label" for="hotel_homepage_url">홈페이지 URL</label>
    <input type="url" name="homepage_url" id="hotel_homepage_url" class="board-form-control board-form-control--max-lg" value="{{ old('homepage_url', $hotel->homepage_url) }}" maxlength="500" placeholder="https://">
    @error('homepage_url')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label class="board-form-label" for="hotel_description">숙소 설명</label>
    <textarea name="description" id="hotel_description" class="board-form-control board-form-textarea" rows="8" data-backoffice-ckeditor="true">{{ old('description', $hotel->description) }}</textarea>
    @error('description')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="board-form-group">
    <label for="hotel_image" class="board-form-label">숙소 이미지 업로드</label>
    <div class="board-file-upload">
        <div class="board-file-input-wrapper">
            <input type="file" class="board-file-input" id="hotel_image" name="image" accept=".jpg,.jpeg,.png">
            <div class="board-file-input-content">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                <span class="board-file-input-subtext">JPG, PNG (최대 5MB) · 권장 600×400px</span>
            </div>
        </div>

        @if (! empty($hotel?->image_path))
            <div class="board-existing-files" id="existingHotelImageWrapper">
                <div class="board-attachment-list">
                    <div class="board-attachment-item existing-file" data-index="0">
                        <input type="hidden" name="existing_image" value="{{ $hotel->image_path }}">
                        <i class="fas fa-file"></i>
                        <a href="{{ asset('storage/' . $hotel->image_path) }}" target="_blank" rel="noopener">
                            <span class="board-attachment-name">{{ basename($hotel->image_path) }}</span>
                        </a>
                        <button type="button" class="board-attachment-remove" data-existing-hotel-image-remove="1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="board-file-preview" id="hotelImagePreview"></div>
    </div>
    @error('image')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>
