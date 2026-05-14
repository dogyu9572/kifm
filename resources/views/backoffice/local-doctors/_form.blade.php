@php
    /** @var \App\Models\LocalDoctor|null $localDoctor */
    $doc = $localDoctor;
    $selectedCats = old('category_ids', $doc ? $doc->doctorCategories->pluck('id')->all() : []);
    /** @var array<string, array<int, string>> $sigunguBySido */
    $sigunguBySido = $sigunguBySido ?? [];
    if (! is_array($selectedCats)) {
        $selectedCats = [];
    }
    $funcSel = old('functional_tests_selected', $doc && is_array($doc->functional_tests_selected) ? $doc->functional_tests_selected : []);
    if (! is_array($funcSel)) {
        $funcSel = [];
    }
    $treatSel = old('treatment_areas_selected', $doc && is_array($doc->treatment_areas_selected) ? $doc->treatment_areas_selected : []);
    if (! is_array($treatSel)) {
        $treatSel = [];
    }
    $allowMemberEdit = (bool) old('allow_member_edit', $doc ? $doc->allow_member_edit : true);
    $selectedMember = $doc?->member;
    $selectedMemberId = old('member_id', $doc?->member_id);
    $selectedMemberLabel = old('member_label');
    if ($selectedMemberLabel === null) {
        $selectedMemberLabel = old('member_display');
    }
    if ($selectedMemberLabel === null && $selectedMember) {
        $selectedMemberLabel =
            $selectedMember->name .
            ' (' .
            ($selectedMember->login_id ?? '-') .
            ' / ' .
            ($selectedMember->email ?? '-') .
            ')';
    }
    $sidos = config('local_doctor_regions.sidos', []);
    $docSidoNormalized = '';
    if ($doc?->sido) {
        $ns = \App\Services\Backoffice\LocalDoctorRegionNormalizer::normalizeSido((string) $doc->sido);
        $docSidoNormalized = $ns['sido'] !== '' ? $ns['sido'] : (string) $doc->sido;
    }
    $formSido = (string) old('sido', $docSidoNormalized);
    $formSigungu = (string) old('sigungu', (string) ($doc?->sigungu ?? ''));
    $photoUrl = null;
    if ($doc?->photo_path) {
        $photoUrl = str_starts_with((string) $doc->photo_path, 'http')
            ? $doc->photo_path
            : \Illuminate\Support\Facades\Storage::disk('public')->url($doc->photo_path);
    }
@endphp

<div class="bo-form-section">
    <h3 class="bo-section-title">회원 연결</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label">연결 회원</label>
            <div class="bo-form-field">
                <div class="input-with-button bo-member-select-inline js-member-selector" data-search-url="{{ route('backoffice.local-doctors.search-members') }}">
                    <input type="hidden" name="member_id" class="js-member-id" value="{{ $selectedMemberId }}">
                    <input type="hidden" name="member_label" class="js-member-label" value="{{ $selectedMemberLabel }}">
                    <input type="text" class="board-form-control js-member-display" value="{{ $selectedMemberLabel }}" placeholder="회원 검색 버튼을 눌러 선택하세요." readonly>
                    <button type="button" class="btn btn-secondary btn-sm js-open-member-modal">회원 검색</button>
                </div>
                @error('member_id')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label class="bo-form-label">회원 수정 허용</label>
            <div class="bo-form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="allow_member_edit" value="1" @checked($allowMemberEdit)>
                    <span>회원이 본인 병원 정보 수정 허용</span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="modal js-member-modal bo-member-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">회원 검색</h5>
            <button type="button" class="close js-close-member-modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="board-filter member-search-modal-filter">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">검색 구분</label>
                        <select class="filter-select js-member-search-field">
                            <option value="all">전체</option>
                            <option value="id">ID</option>
                            <option value="name">이름</option>
                            <option value="phone">휴대폰</option>
                            <option value="email">이메일</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">검색어</label>
                        <input type="text" class="filter-input js-member-keyword" placeholder="검색어를 입력하세요">
                    </div>
                    <div class="filter-group">
                        <div class="filter-buttons">
                            <button type="button" class="btn btn-primary js-search-member"><i class="fas fa-search"></i> 검색</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>이름</th>
                            <th>아이디</th>
                            <th>연락처</th>
                            <th>이메일</th>
                            <th>선택</th>
                        </tr>
                    </thead>
                    <tbody class="js-member-results">
                        <tr>
                            <td colspan="6" class="text-center">검색 버튼을 눌러 회원을 조회하세요.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 js-member-pagination"></div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-5">
    <h3 class="bo-section-title">의사 정보</h3>
    <div class="bo-form-list">
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="board-form-group mb-0">
                    <label class="board-form-label">의사 사진</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" name="photo" id="doctor_photo_file" class="board-file-input" accept=".jpg,.jpeg,.png,.gif" data-max-file-size-mb="5">
                            <div class="board-file-input-content">
                                <i class="fas fa-image"></i>
                                <span class="board-file-input-text">이미지 파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">JPG, PNG, GIF 파일만 가능 (최대 5MB)</span>
                            </div>
                        </div>
                        @if ($doc && $doc->photo_path)
                            <div class="board-existing-files mt-2">
                                <div class="board-attachment-list">
                                    <div class="board-attachment-item existing-file" id="bo-doctor-photo-existing-item">
                                        <i class="fas fa-file-image"></i>
                                        <a href="{{ $photoUrl }}" target="_blank" rel="noopener">
                                            <span class="board-attachment-name">{{ basename($doc->photo_path) }}</span>
                                        </a>
                                        <button type="button" class="board-attachment-remove" data-remove-existing-target="doctor_photo">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="checkbox" name="delete_photo" id="delete_photo" value="1" class="bo-hidden">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="board-file-preview" id="doctorPhotoFilePreview"></div>
                    </div>
                    @error('photo')
                        <span class="bo-inline-error d-block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label" for="doctor_name">선생님 성함 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="text" name="doctor_name" id="doctor_name" class="board-form-control board-form-control--max-md"
                            value="{{ old('doctor_name', optional($doc)->doctor_name ?? '') }}" maxlength="100" required>
                        @error('doctor_name')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="bo-form-row">
                    <label class="bo-form-label" for="license_no">면허번호 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="text" name="license_no" id="license_no" class="board-form-control board-form-control--max-md"
                            value="{{ old('license_no', optional($doc)->license_no ?? '') }}" maxlength="50" required>
                        @error('license_no')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="bo-form-row">
                    <label class="bo-form-label" for="introduction">병원 소개</label>
                    <div class="bo-form-field">
                        <textarea name="introduction" id="introduction" class="board-form-control" rows="8" data-backoffice-ckeditor="true">{{ old('introduction', optional($doc)->introduction ?? '') }}</textarea>
                        @error('introduction')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-5">
    <h3 class="bo-section-title">병원 정보</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label" for="hospital_name">병원명 <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" name="hospital_name" id="hospital_name" class="board-form-control board-form-control--max-lg"
                    value="{{ old('hospital_name', optional($doc)->hospital_name ?? '') }}" maxlength="200" required>
                @error('hospital_name')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label" for="sido">시/도 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <select name="sido" id="sido" class="board-form-control board-form-control--max-md" required>
                            <option value="">선택</option>
                            @foreach ($sidos as $sidoOption)
                                <option value="{{ $sidoOption }}" @selected($formSido === $sidoOption)>{{ $sidoOption }}</option>
                            @endforeach
                        </select>
                        @error('sido')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label" for="sigungu">시/군/구 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <select name="sigungu" id="sigungu" class="board-form-control board-form-control--max-md" required>
                            <option value="">선택</option>
                            @php
                                $sigunguList = $formSido !== '' && isset($sigunguBySido[$formSido])
                                    ? $sigunguBySido[$formSido]
                                    : [];
                            @endphp
                            @foreach ($sigunguList as $sg)
                                <option value="{{ $sg }}" @selected($formSigungu === $sg)>{{ $sg }}</option>
                            @endforeach
                            @if ($formSigungu !== '' && ! in_array($formSigungu, $sigunguList, true))
                                <option value="{{ $formSigungu }}" selected>{{ $formSigungu }}</option>
                            @endif
                        </select>
                        @error('sigungu')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="bo-form-row">
            <label class="bo-form-label" for="address">주소 (도로명) <span class="required">*</span></label>
            <div class="bo-form-field">
                <div class="input-with-button">
                    <input type="text" name="address" id="address" class="board-form-control" value="{{ old('address', optional($doc)->address ?? '') }}" maxlength="500" required>
                    <button type="button" class="btn btn-secondary btn-sm" id="bo-local-doctor-address-search">주소 검색</button>
                </div>
                @error('address')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label class="bo-form-label" for="address_detail">상세주소</label>
            <div class="bo-form-field">
                <input type="text" name="address_detail" id="address_detail" class="board-form-control board-form-control--max-lg"
                    value="{{ old('address_detail', optional($doc)->address_detail ?? '') }}" maxlength="200">
                @error('address_detail')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label" for="homepage">홈페이지 URL</label>
                    <div class="bo-form-field">
                        <input type="text" name="homepage" id="homepage" class="board-form-control board-form-control--max-lg"
                            value="{{ old('homepage', optional($doc)->homepage ?? '') }}" maxlength="500" placeholder="https://">
                        @error('homepage')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bo-member-dual-col">
                <div class="bo-form-row">
                    <label class="bo-form-label" for="phone">전화번호 <span class="required">*</span></label>
                    <div class="bo-form-field">
                        <input type="text" name="phone" id="phone" class="board-form-control board-form-control--max-md"
                            value="{{ old('phone', optional($doc)->phone ?? '') }}" maxlength="80" required>
                        @error('phone')
                            <span class="bo-inline-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="bo-form-row">
            <span class="bo-form-label">진료 카테고리 <span class="required">*</span></span>
            <div class="bo-form-field">
                <div class="board-payment-checkboxes">
                    @foreach ($categories as $cat)
                        <label class="checkbox-label">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" @checked(in_array((int) $cat->id, array_map('intval', $selectedCats), true))>
                            <span>{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('category_ids')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-5">
    <h3 class="bo-section-title">시행하고 있는 기능의학 검사</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <span class="bo-form-label">검사 항목</span>
            <div class="bo-form-field">
                <div class="board-payment-checkboxes">
                    @foreach ($functionalTests as $item)
                        @php $fid = $item['id'] ?? ''; @endphp
                        <label class="checkbox-label">
                            <input type="checkbox" name="functional_tests_selected[]" value="{{ $fid }}" @checked(in_array($fid, $funcSel, true))>
                            <span>{{ $item['label'] ?? $fid }}</span>
                        </label>
                    @endforeach
                </div>
                @error('functional_tests_selected')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-5">
    <h3 class="bo-section-title">치료 가능 영역</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <span class="bo-form-label">치료 영역</span>
            <div class="bo-form-field">
                <div class="board-payment-checkboxes">
                    @foreach ($treatmentAreas as $item)
                        @php $tid = $item['id'] ?? ''; @endphp
                        <label class="checkbox-label">
                            <input type="checkbox" name="treatment_areas_selected[]" value="{{ $tid }}" @checked(in_array($tid, $treatSel, true))>
                            <span>{{ $item['label'] ?? $tid }}</span>
                        </label>
                    @endforeach
                </div>
                @error('treatment_areas_selected')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-2">
    <h3 class="bo-section-title">기타 증상</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label" for="other_symptoms">내용</label>
            <div class="bo-form-field">
                <textarea name="other_symptoms" id="other_symptoms" class="board-form-control" rows="4">{{ old('other_symptoms', optional($doc)->other_symptoms ?? '') }}</textarea>
                @error('other_symptoms')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section mt-2">
    <h3 class="bo-section-title">질환 및 증후군</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label" for="diseases_text">내용</label>
            <div class="bo-form-field">
                <textarea name="diseases_text" id="diseases_text" class="board-form-control" rows="4">{{ old('diseases_text', optional($doc)->diseases_text ?? '') }}</textarea>
                @error('diseases_text')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

@if ($doc && ! empty($doc->legacy_csv_extras))
    <div class="bo-form-section mt-2">
        <h3 class="bo-section-title">레거시 CSV 보조 데이터</h3>
        <div class="bo-form-list">
            <div class="bo-form-row">
                <div class="bo-form-field">
                    <div class="table-responsive border rounded">
                        <pre class="board-form-text bg-light p-3 small mb-0 text-left">{{ json_encode($doc->legacy_csv_extras, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="bo-form-section mt-2">
    <h3 class="bo-section-title">운영 상태</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label class="bo-form-label" for="status">상태 <span class="required">*</span></label>
            <div class="bo-form-field">
                <select name="status" id="status" class="board-form-control board-form-control--max-xs" required>
                    @foreach ($statusLabels as $code => $label)
                        <option value="{{ $code }}" @selected(old('status', optional($doc)->status ?? 'active') === $code)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        @if ($doc)
            <div class="bo-member-dual-row">
                <div class="bo-member-dual-col">
                    <div class="bo-form-row">
                        <label class="bo-form-label" for="view_count">조회수</label>
                        <div class="bo-form-field">
                            <input type="number" name="view_count" id="view_count" class="board-form-control board-form-control--max-xs" min="0" step="1"
                                value="{{ old('view_count', optional($doc)->view_count ?? 0) }}">
                            @error('view_count')
                                <span class="bo-inline-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="bo-member-dual-col">
                    <div class="bo-form-row">
                        <label class="bo-form-label" for="sort_order">정렬 순서</label>
                        <div class="bo-form-field">
                            <input type="number" name="sort_order" id="sort_order" class="board-form-control board-form-control--max-xs" step="1"
                                value="{{ old('sort_order', optional($doc)->sort_order ?? 0) }}">
                            @error('sort_order')
                                <span class="bo-inline-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
