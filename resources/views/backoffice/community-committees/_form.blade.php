@php
    $roleLabels = $roleLabels ?? ['chairman' => '위원장', 'secretary' => '간사', 'member' => '위원'];
    $isEdit = $committee->exists;
    $membersFromOld = old('committee_members');
    $membersForRows = [];

    if (is_array($membersFromOld)) {
        foreach ($membersFromOld as $idx => $item) {
            $membersForRows[] = [
                'user_id' => (int) ($item['user_id'] ?? 0),
                'role' => $item['role'] ?? 'member',
                'name' => $item['name'] ?? '',
                'email' => $item['email'] ?? '',
                'phone' => $item['phone'] ?? '',
                'organization' => $item['organization'] ?? '',
                'login_id' => $item['login_id'] ?? '',
                'key' => $item['key'] ?? ('old-' . $idx),
            ];
        }
    } elseif ($committee->relationLoaded('committeeMembers')) {
        foreach ($committee->committeeMembers as $memberRow) {
            $member = $memberRow->user;
            if (! $member) {
                continue;
            }
            $membersForRows[] = [
                'user_id' => $member->id,
                'role' => $memberRow->role ?? 'member',
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone_number,
                'organization' => $member->workplace_name,
                'login_id' => $member->login_id,
                'key' => 'member-' . $member->id,
            ];
        }
    }

    $memberCount = count($membersForRows);
@endphp

<div class="bo-form-section">
    <h3 class="bo-section-title">위원회 상세</h3>
    <div class="bo-form-list">
        <div class="board-form-group">
            <label class="board-form-label">썸네일</label>
            <div class="board-file-upload">
                <div class="board-file-input-wrapper">
                    <input type="file" name="thumbnail" id="thumbnail" class="board-file-input" accept=".jpg,.jpeg,.png,.gif,.webp" data-max-file-size-mb="5">
                    <div class="board-file-input-content">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                        <span class="board-file-input-subtext">1개, 파일 5MB 이하</span>
                    </div>
                </div>
                @if ($isEdit && $committee->thumbnail_path)
                    <div class="board-existing-files">
                        <div class="board-attachment-list">
                            <div class="board-attachment-item existing-file" id="bo-thumbnail-existing-item">
                                <i class="fas fa-image"></i>
                                <a href="{{ asset('storage/' . $committee->thumbnail_path) }}" target="_blank" rel="noopener">
                                    <span class="board-attachment-name">{{ basename($committee->thumbnail_path) }}</span>
                                </a>
                                <button type="button" class="board-attachment-remove" data-remove-existing-target="thumbnail">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="checkbox" name="delete_thumbnail" id="delete_thumbnail" value="1" class="bo-hidden">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="board-file-preview" id="thumbnailPreview"></div>
            </div>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">상단 배너 이미지</label>
            <div class="board-file-upload">
                <div class="board-file-input-wrapper">
                    <input type="file" name="banner" id="banner_file" class="board-file-input" accept=".jpg,.jpeg,.png,.webp" data-max-file-size-mb="5">
                    <div class="board-file-input-content">
                        <i class="fas fa-image"></i>
                        <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                        <span class="board-file-input-subtext">권장 사이즈: 1920 × 400px (JPG, PNG, 5MB 이하)</span>
                    </div>
                </div>
                @if ($isEdit && $committee->banner_path)
                    <div class="board-existing-files mt-2">
                        <div class="board-attachment-list">
                            <div class="board-attachment-item existing-file" id="bo-banner-existing-item">
                                <i class="fas fa-file-image"></i>
                                <a href="{{ asset('storage/' . $committee->banner_path) }}" target="_blank" rel="noopener">
                                    <span class="board-attachment-name">{{ basename($committee->banner_path) }}</span>
                                </a>
                                <button type="button" class="board-attachment-remove" data-remove-existing-target="banner">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="checkbox" name="delete_banner" id="delete_banner" value="1" class="bo-hidden">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="board-file-preview" id="bannerFilePreview"></div>
            </div>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">위원회명 <span class="required">*</span></label>
            <input type="text" name="name" class="board-form-control board-form-control--max-lg" value="{{ old('name', $committee->name) }}" required>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">위원회 소개</label>
            <textarea name="description" class="board-form-control board-form-textarea" rows="3">{{ old('description', $committee->description) }}</textarea>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">정원</label>
            <div class="d-inline-flex align-items-center flex-nowrap">
                <input type="number" min="1" name="member_limit" id="member_limit" class="board-form-control board-form-control--max-xs" value="{{ old('member_limit', $committee->member_limit) }}">
                <span class="ml-2 mr-3">명</span>
                <label class="board-radio-item mb-0 d-inline-flex align-items-center text-nowrap">
                    <input type="checkbox" name="no_member_limit" id="no_member_limit" value="1" @checked(old('no_member_limit', $committee->member_limit === null))>
                    <span class="ml-1 text-nowrap">제한 없음</span>
                </label>
            </div>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">노출 여부 <span class="required">*</span></label>
            <div class="board-radio-group">
                <label class="board-radio-item">
                    <input type="radio" name="visibility_yn" value="Y" @checked(old('visibility_yn', $committee->visibility_yn ?? 'Y') === 'Y')> <span>노출</span>
                </label>
                <label class="board-radio-item">
                    <input type="radio" name="visibility_yn" value="N" @checked(old('visibility_yn', $committee->visibility_yn ?? 'Y') === 'N')> <span>미노출</span>
                </label>
            </div>
            <small class="board-form-text">* 미노출 설정 시 목록에는 표시되지 않으나 링크로는 접속 가능합니다.</small>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">위원회 유형 <span class="required">*</span></label>
            <div class="board-radio-group">
                @foreach ($typeLabels as $code => $label)
                    <label class="board-radio-item">
                        <input type="radio" name="committee_type" value="{{ $code }}" @checked(old('committee_type', $committee->committee_type) === $code)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">필수 참가 설정</label>
            <label class="board-radio-item">
                <input type="checkbox" name="is_mandatory" value="Y" @checked(old('is_mandatory', $committee->is_mandatory ?? 'N') === 'Y')>
                <span>해당 위원회를 필수 참가 위원회로 설정</span>
            </label>
            <small class="board-form-text">* 설정 시 정회원 로그인 시 참가가 권장됩니다.</small>
        </div>

        <div class="board-form-group">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="board-form-label mb-0">소속 위원 관리 <span id="committee_member_count" class="text-secondary">({{ $memberCount }}명)</span></label>
                <div class="js-member-selector" data-search-url="{{ route('backoffice.community-committees.search-members') }}">
                    <input type="hidden" class="js-member-id">
                    <input type="hidden" class="js-member-label">
                    <input type="text" class="bo-hidden js-member-display" readonly>
                    <button type="button" class="btn btn-secondary btn-sm js-open-member-modal"><i class="fas fa-search"></i> 위원 추가</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th class="w8">No</th>
                            <th class="w12">역할</th>
                            <th class="w10">이름</th>
                            <th>이메일</th>
                            <th class="w12">연락처</th>
                            <th class="w15">직장명</th>
                            <th class="w10">관리</th>
                        </tr>
                    </thead>
                    <tbody id="committee-members-body">
                        @forelse ($membersForRows as $idx => $member)
                            <tr class="js-committee-member-row" data-member-id="{{ $member['user_id'] }}">
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <select name="committee_members[{{ $idx }}][role]" class="board-form-control board-form-control--max-xs">
                                        @foreach ($roleLabels as $roleCode => $roleLabel)
                                            <option value="{{ $roleCode }}" @selected($member['role'] === $roleCode)>{{ $roleLabel }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    {{ $member['name'] }}
                                    <input type="hidden" name="committee_members[{{ $idx }}][user_id]" value="{{ $member['user_id'] }}">
                                    <input type="hidden" name="committee_members[{{ $idx }}][name]" value="{{ $member['name'] }}">
                                    <input type="hidden" name="committee_members[{{ $idx }}][email]" value="{{ $member['email'] }}">
                                    <input type="hidden" name="committee_members[{{ $idx }}][phone]" value="{{ $member['phone'] }}">
                                    <input type="hidden" name="committee_members[{{ $idx }}][organization]" value="{{ $member['organization'] }}">
                                    <input type="hidden" name="committee_members[{{ $idx }}][login_id]" value="{{ $member['login_id'] }}">
                                </td>
                                <td>{{ $member['email'] }}</td>
                                <td>{{ $member['phone'] }}</td>
                                <td>{{ $member['organization'] }}</td>
                                <td><button type="button" class="btn btn-outline-danger btn-sm js-remove-committee-member">삭제</button></td>
                            </tr>
                        @empty
                            <tr class="js-committee-member-empty">
                                <td colspan="7" class="text-center">등록된 인원이 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">업무 및 운영 내규</label>
            <textarea name="regulation" class="board-form-control board-form-textarea" rows="8" data-backoffice-ckeditor data-source-editing="true">{{ old('regulation', $committee->regulation) }}</textarea>
        </div>

        <div class="board-form-group">
            <label class="board-form-label">업무 프로토콜</label>
            <textarea name="protocol" class="board-form-control board-form-textarea" rows="8" data-backoffice-ckeditor data-source-editing="true">{{ old('protocol', $committee->protocol) }}</textarea>
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

