@php
    $e = $event;
    $ptOld = old('presentation_types', $e->presentation_types ?? []);
    if (! is_array($ptOld)) {
        $ptOld = [];
    }
    $speakerRowsForForm = old('speakers');
    if ($speakerRowsForForm === null) {
        $speakerRowsForForm = $e->speakers?->map(fn ($s) => [
            'source' => $s->source,
            'member_id' => $s->member_id,
            'academic_event_abstract_id' => $s->academic_event_abstract_id,
            'name' => $s->name,
            'affiliation' => $s->affiliation,
            'position' => $s->position,
            'abstract_title' => $s->abstract_title,
            'bio' => $s->bio,
            'image_path' => $s->image_path,
            'sort_order' => $s->sort_order,
        ])->toArray() ?? [];
    }
    if ($speakerRowsForForm === []) {
        $speakerRowsForForm = [['source' => 'manual', 'name' => '']];
    }
    $sponsorRowsForForm = old('sponsors');
    if ($sponsorRowsForForm === null) {
        $sponsorRowsForForm = $e->sponsors?->map(fn ($s) => [
            'academic_sponsor_master_id' => $s->academic_sponsor_master_id,
            'name' => $s->name,
            'level' => $s->level,
            'logo_path' => $s->logo_path,
            'sort_order' => $s->sort_order,
        ])->toArray() ?? [];
    }
    if ($sponsorRowsForForm === []) {
        $sponsorRowsForForm = [['name' => '', 'level' => 'exhibitors']];
    }
    $mainSponsorOptionsForForm = [];
    foreach ($sponsorRowsForForm as $si => $sponsorRow) {
        $sponsorName = trim((string) ($sponsorRow['name'] ?? ''));
        if ($sponsorName === '') {
            continue;
        }
        $placement = (string) ($sponsorRow['level'] ?? 'exhibitors');
        $mainSponsorOptionsForForm[] = [
            'sponsor_index' => (int) $si,
            'name' => $sponsorName,
            'placement' => $placement,
            'placement_label' => $sponsorLevelLabels[$placement] ?? $placement,
        ];
    }
    $mainSponsorOptionByIndex = collect($mainSponsorOptionsForForm)->keyBy('sponsor_index');
    $submittedMainSlots = old('main_sponsor_slots', null);
    $mainSlotsForForm = [];
    if (is_array($submittedMainSlots)) {
        foreach ($submittedMainSlots as $slot) {
            $sponsorIndex = isset($slot['sponsor_index']) ? (int) $slot['sponsor_index'] : null;
            $option = $sponsorIndex !== null ? $mainSponsorOptionByIndex->get($sponsorIndex) : null;
            if (! $option) {
                continue;
            }
            $mainSlotsForForm[] = [
                'sponsor_index' => (int) $option['sponsor_index'],
                'name' => $option['name'],
                'placement' => $option['placement'],
                'placement_label' => $option['placement_label'],
                'active' => (string) ($slot['active'] ?? '0'),
                'sort_order' => (int) ($slot['sort_order'] ?? ($sponsorIndex + 1)),
            ];
        }
    } elseif ($e->exists && $e->relationLoaded('mainSponsorSlots') && $e->mainSponsorSlots?->isNotEmpty()) {
        $idxBySponsorId = [];
        foreach ($e->sponsors ?? [] as $idx => $sp) {
            $idxBySponsorId[$sp->id] = $idx;
        }
        foreach ($e->mainSponsorSlots as $slot) {
            $sponsorIndex = $idxBySponsorId[$slot->academic_event_sponsor_id] ?? null;
            $option = $sponsorIndex !== null ? $mainSponsorOptionByIndex->get($sponsorIndex) : null;
            if (! $option) {
                continue;
            }
            $mainSlotsForForm[] = [
                'sponsor_index' => (int) $option['sponsor_index'],
                'name' => $option['name'],
                'placement' => $option['placement'],
                'placement_label' => $option['placement_label'],
                'sort_order' => (int) $slot->sort_order,
                'active' => '1',
            ];
        }
    }
    usort($mainSlotsForForm, fn ($a, $b) => ((int) $a['sort_order'] <=> (int) $b['sort_order']) ?: ((int) $a['sponsor_index'] <=> (int) $b['sponsor_index']));
    $sessionCategoryLabels = [
        'oral' => '구연 발표',
        'poster' => '포스터 발표',
        'special' => '특별 강연',
        'symposium' => '심포지엄',
    ];
    $abstractFieldsRows = old('abstract_fields', null);
    if ($abstractFieldsRows === null) {
        $abstractFieldsRows = $e->fields?->map(fn ($f) => ['name' => $f->name, 'sort_order' => (int) $f->sort_order])->toArray() ?? [['name' => '', 'sort_order' => 1]];
    }
    if (! is_array($abstractFieldsRows)) {
        $abstractFieldsRows = [['name' => '', 'sort_order' => 1]];
    }
    $activeTab = old('active_tab', request()->query('tab', 'base'));
    $allowedAcademicTabs = ['base', 'prereg', 'abstract', 'speakers', 'sponsors', 'sessions'];
    if (! in_array($activeTab, $allowedAcademicTabs, true)) {
        $activeTab = 'base';
    }
@endphp

<div class="bo-round-toolbar mb-3">
    <div class="bo-round-tabs" role="tablist">
        <button type="button" class="btn btn-sm btn-outline-primary js-academic-tab-btn @if ($activeTab === 'base') active @endif" data-tab="base">기본 설정</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-academic-tab-btn @if ($activeTab === 'prereg') active @endif" data-tab="prereg">사전·현장등록</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-academic-tab-btn @if ($activeTab === 'abstract') active @endif" data-tab="abstract">초록등록</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-academic-tab-btn @if ($activeTab === 'speakers') active @endif" data-tab="speakers">연자</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-academic-tab-btn @if ($activeTab === 'sponsors') active @endif" data-tab="sponsors">스폰서</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-academic-tab-btn @if ($activeTab === 'sessions') active @endif" data-tab="sessions">세션</button>
    </div>
</div>
<input type="hidden" name="active_tab" id="bo-academic-event-active-tab" value="{{ $activeTab }}">

<div class="js-academic-tab-panel @if ($activeTab !== 'base') bo-hidden @endif" data-tab-panel="base">
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">연도 <span class="required">*</span></label>
            <select name="year" class="board-form-control board-form-control--max-md @error('year') is-invalid @enderror" required>
                @foreach ($yearOptions as $y)
                    <option value="{{ $y }}" @selected((int) old('year', $e->year) === $y)>{{ $y }}년</option>
                @endforeach
            </select>
            @error('year')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">시즌 <span class="required">*</span></label>
            <select name="season" class="board-form-control board-form-control--max-md @error('season') is-invalid @enderror" required>
                <option value="">시즌 선택</option>
                @foreach ($seasonLabels as $code => $label)
                    <option value="{{ $code }}" @selected(old('season', $e->season) === $code)>{{ $label }}</option>
                @endforeach
            </select>
            @error('season')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">폴더명 <span class="required">*</span></label>
        @if ($isEdit)
            <input type="text" class="board-form-control" value="{{ old('folder_name', $e->folder_name) }}" maxlength="120" disabled>
        @else
            <input type="text" name="folder_name" class="board-form-control @error('folder_name') is-invalid @enderror"
                value="{{ old('folder_name', $e->folder_name) }}" maxlength="120" required>
        @endif
        @error('folder_name')<span class="bo-inline-error">{{ $message }}</span>@enderror
        @if ($isEdit)
            <p class="board-form-help">생성 후 수정할 수 없습니다.</p>
        @endif
    </div>
    <div class="board-form-group">
        <label class="board-form-label">행사명 <span class="required">*</span></label>
        <input type="text" name="title" class="board-form-control @error('title') is-invalid @enderror" value="{{ old('title', $e->title) }}" required>
        @error('title')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">타이틀 1</label>
            <input type="text" name="main_title_1" class="board-form-control @error('main_title_1') is-invalid @enderror" value="{{ old('main_title_1', $e->main_title_1) }}" maxlength="255">
            @error('main_title_1')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">타이틀 2</label>
            <input type="text" name="main_title_2" class="board-form-control @error('main_title_2') is-invalid @enderror" value="{{ old('main_title_2', $e->main_title_2) }}" maxlength="255">
            @error('main_title_2')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">행사 자료 설명</label>
        <input type="text" name="event_material_description" class="board-form-control" value="{{ old('event_material_description', $e->event_material_description) }}">
        <div class="board-file-upload mt-2">
            <div class="board-file-input-wrapper">
                <input type="file" name="event_material" id="academic_event_material_file" class="board-file-input" accept=".pdf,.zip,.doc,.docx" data-max-file-size-mb="50">
                <div class="board-file-input-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="board-file-input-text">행사 자료 파일을 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">PDF, ZIP, DOC, DOCX (최대 50MB)</span>
                </div>
            </div>
            @if ($isEdit && $e->event_material_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-academic-event-material-existing-item">
                            <i class="fas fa-file-alt"></i>
                            <a href="{{ asset('storage/' . $e->event_material_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ basename($e->event_material_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="event_material">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_event_material" id="delete_event_material" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="academicEventMaterialFilePreview"></div>
        </div>
        @if ($isEdit && $e->event_material_path)
            <small class="board-form-text d-block mt-1">※ 기존 파일은 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>
    <div class="board-form-group">
        <label class="board-form-label">행사 방식 <span class="required">*</span></label>
        <div class="board-radio-group">
            <label class="board-radio-item"><input type="radio" name="event_type" value="offline" class="bo-event-type-input" @checked(old('event_type', $e->event_type) === 'offline')> <span>오프라인</span></label>
            <label class="board-radio-item"><input type="radio" name="event_type" value="online" class="bo-event-type-input" @checked(old('event_type', $e->event_type) === 'online')> <span>온라인</span></label>
        </div>
    </div>
    <div class="board-form-group bo-online-only @if (old('event_type', $e->event_type) !== 'online') bo-hidden @endif">
        <label class="board-form-label">온라인 URL <span class="required">*</span></label>
        <input type="url" name="online_url" class="board-form-control" value="{{ old('online_url', $e->online_url) }}">
    </div>
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">공개여부 <span class="required">*</span></label>
            <div class="board-radio-group">
                <label class="board-radio-item"><input type="radio" name="is_public" value="Y" @checked(old('is_public', $e->is_public) === 'Y')> <span>공개</span></label>
                <label class="board-radio-item"><input type="radio" name="is_public" value="N" @checked(old('is_public', $e->is_public) === 'N')> <span>비공개</span></label>
            </div>
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">메인 노출</label>
            <div class="board-radio-group">
                <label class="board-radio-item"><input type="radio" name="main_exposure" value="Y" @checked(old('main_exposure', $e->main_exposure) === 'Y')> <span>노출</span></label>
                <label class="board-radio-item"><input type="radio" name="main_exposure" value="N" @checked(old('main_exposure', $e->main_exposure ?? 'N') === 'N')> <span>미노출</span></label>
            </div>
        </div>
    </div>
    <div class="board-form-group bo-offline-only @if (old('event_type', $e->event_type) === 'online') bo-hidden @endif">
        <label class="board-form-label">개최장소</label>
        <input type="text" name="venue" class="board-form-control" value="{{ old('venue', $e->venue) }}" maxlength="2000">
    </div>
    <div class="board-form-group bo-offline-only @if (old('event_type', $e->event_type) === 'online') bo-hidden @endif">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <label class="board-form-label mb-0">행사장 층별 안내</label>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="bo-add-venue-floor-btn">층 추가</button>
        </div>
        <table class="board-table" id="bo-venue-floors-table">
            <colgroup>
                <col>
                <col>
                <col class="bo-venue-floor-action-col">
            </colgroup>
            <thead><tr><th>층명</th><th>파일</th><th></th></tr></thead>
            <tbody id="bo-venue-floors-body">
                @foreach (old('venue_floors', $e->venueFloors?->map(fn ($f) => ['floor_name' => $f->floor_name, 'file_path' => $f->file_path, 'sort_order' => $f->sort_order])->toArray() ?? [['floor_name' => '', 'file_path' => null]]) as $fi => $row)
                    <tr class="bo-repeat-row">
                        <td><input type="text" name="venue_floors[{{ $fi }}][floor_name]" class="board-form-control" value="{{ $row['floor_name'] ?? '' }}"></td>
                        <td>
                            <input type="file" name="venue_floor_files[]" class="board-form-control" accept="image/*,.pdf">
                            @if (! empty($row['file_path']))
                                <input type="hidden" name="venue_floors[{{ $fi }}][file_path]" value="{{ $row['file_path'] }}">
                                <input type="hidden" name="venue_floors[{{ $fi }}][delete_file]" value="0" data-venue-floor-delete-file>
                                <div class="board-existing-files mt-2 mb-0 p-2">
                                    <div class="board-attachment-list">
                                        <div class="board-attachment-item existing-file">
                                            <i class="fas fa-file-image"></i>
                                            <a href="{{ asset('storage/' . $row['file_path']) }}" target="_blank" rel="noopener">
                                                <span class="board-attachment-name">{{ \App\Support\BackofficeFile::displayName($row['file_path']) }}</span>
                                            </a>
                                            <button type="button" class="board-attachment-remove" data-remove-existing-venue-floor-file>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">행사 시작</label>
            <input type="datetime-local" name="start_at" class="board-form-control board-form-control--max-md" value="{{ old('start_at', $e->start_at?->format('Y-m-d\TH:i')) }}">
            <label class="board-form-check"><input type="checkbox" name="start_time_omit" value="1" @checked(old('start_time_omit', $e->start_time_omit))> 시간 미입력</label>
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">행사 종료</label>
            <input type="datetime-local" name="end_at" class="board-form-control board-form-control--max-md" value="{{ old('end_at', $e->end_at?->format('Y-m-d\TH:i')) }}">
            <label class="board-form-check"><input type="checkbox" name="end_time_omit" value="1" @checked(old('end_time_omit', $e->end_time_omit))> 시간 미입력</label>
        </div>
    </div>
    <div class="bo-form-section mt-4">
        <h3 class="bo-section-title">인사말 설정</h3>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">인사말 제목</label>
        <input type="text" name="greeting_title" class="board-form-control" value="{{ old('greeting_title', $e->greeting_title) }}">
    </div>
    <div class="board-form-group">
        <label class="board-form-label">인사말 내용</label>
        <textarea name="greeting_content" id="greeting_content" class="board-form-control board-form-textarea" rows="6" data-backoffice-ckeditor="true">{{ old('greeting_content', $e->greeting_content) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">인사말 이미지</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="greeting_image" id="academic_greeting_image_file" class="board-file-input" accept=".jpg,.jpeg,.png,.gif" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-image"></i>
                    <span class="board-file-input-text">이미지를 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">JPG, PNG, GIF (최대 20MB)</span>
                </div>
            </div>
            @if ($isEdit && $e->greeting_image_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-academic-greeting-existing-item">
                            <i class="fas fa-file-image"></i>
                            <a href="{{ asset('storage/' . $e->greeting_image_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ basename($e->greeting_image_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="greeting_image">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_greeting_image" id="delete_greeting_image" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="academicGreetingImagePreview"></div>
        </div>
        @if ($isEdit && $e->greeting_image_path)
            <small class="board-form-text d-block mt-1">※ 기존 이미지는 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>
    <div class="bo-form-section mt-5">
        <h3 class="bo-section-title">조직위원회 설정</h3>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">조직위원회</label>
        <textarea name="committee_content" id="committee_content" class="board-form-control board-form-textarea" rows="5" data-backoffice-ckeditor="true">{{ old('committee_content', $e->committee_content) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">PC 메인배너</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="pc_banner" id="academic_pc_banner_file" class="board-file-input" accept=".jpg,.jpeg,.png,.gif" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-image"></i>
                    <span class="board-file-input-text">배너 이미지를 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">JPG, PNG, GIF (최대 20MB)</span>
                </div>
            </div>
            @if ($isEdit && $e->pc_banner_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-academic-pc-banner-existing-item">
                            <i class="fas fa-file-image"></i>
                            <a href="{{ asset('storage/' . $e->pc_banner_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ basename($e->pc_banner_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="pc_banner">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_pc_banner" id="delete_pc_banner" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="academicPcBannerFilePreview"></div>
        </div>
        @if ($isEdit && $e->pc_banner_path)
            <small class="board-form-text d-block mt-1">※ 기존 배너는 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>
    <div class="board-form-group">
        <label class="board-form-label">행사 썸네일</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="thumbnail" id="academic_thumbnail_file" class="board-file-input" accept=".jpg,.jpeg,.png,.gif" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-image"></i>
                    <span class="board-file-input-text">썸네일 파일을 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">JPG, PNG, GIF 파일만 가능 (최대 20MB)</span>
                </div>
            </div>
            @if ($isEdit && $e->thumbnail_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-academic-thumbnail-existing-item">
                            <i class="fas fa-file-image"></i>
                            <a href="{{ asset('storage/' . $e->thumbnail_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ basename($e->thumbnail_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="thumbnail">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_thumbnail" id="delete_thumbnail" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="academicThumbnailFilePreview"></div>
        </div>
        @if ($isEdit && $e->thumbnail_path)
            <small class="board-form-text d-block mt-1">※ 기존 썸네일은 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>
    {{--
    <div class="board-form-group">
        <label class="board-form-label">Exhibition 이미지</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="exhibition_image" id="academic_exhibition_image_file" class="board-file-input" accept=".jpg,.jpeg,.png,.gif" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-image"></i>
                    <span class="board-file-input-text">Exhibition 이미지를 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">JPG, PNG, GIF 파일만 가능 (최대 20MB)</span>
                </div>
            </div>
            @if ($isEdit && $e->exhibition_image_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-academic-exhibition-existing-item">
                            <i class="fas fa-file-image"></i>
                            <a href="{{ asset('storage/' . $e->exhibition_image_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ basename($e->exhibition_image_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="exhibition_image">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_exhibition_image" id="delete_exhibition_image" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="academicExhibitionImageFilePreview"></div>
        </div>
        @if ($isEdit && $e->exhibition_image_path)
            <small class="board-form-text d-block mt-1">※ 기존 Exhibition 이미지는 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>
    --}}
    <div class="bo-form-section mt-5">
        <h3 class="bo-section-title">오시는 길 설정</h3>
    </div>
    <div class="bo-offline-only @if (old('event_type', $e->event_type) === 'online') bo-hidden @endif">
        <div class="bo-form-row">
            <label class="bo-form-label" for="bo-academic-event-address">주소</label>
            <div class="bo-form-field">
                <div class="input-with-button">
                    <input type="text" name="address" id="bo-academic-event-address" class="board-form-control" value="{{ old('address', $e->address) }}" maxlength="500">
                    <button type="button" class="btn btn-secondary btn-sm" id="bo-academic-event-address-search">주소 검색</button>
                </div>
                @error('address')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label class="bo-form-label" for="bo-academic-event-address-detail">상세주소</label>
            <div class="bo-form-field">
                <input type="text" name="address_detail" id="bo-academic-event-address-detail" class="board-form-control board-form-control--max-lg" value="{{ old('address_detail', $e->address_detail) }}" maxlength="300">
                @error('address_detail')
                    <span class="bo-inline-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">도보 안내</label>
        <textarea name="walking_guide" id="walking_guide" class="board-form-control board-form-textarea" rows="4" data-backoffice-ckeditor="true">{{ old('walking_guide', $e->walking_guide) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">셔틀버스 안내</label>
        <textarea name="shuttle_guide" id="shuttle_guide" class="board-form-control board-form-textarea" rows="4" data-backoffice-ckeditor="true">{{ old('shuttle_guide', $e->shuttle_guide) }}</textarea>
    </div>
    <div class="bo-form-section mt-5">
        <h3 class="bo-section-title">메인 페이지 노출 스폰서</h3>
    </div>
    <p class="board-form-help">홈페이지 메인에 노출할 스폰서를 「스폰서」 탭에 등록된 항목 중에서 선택하고 노출 순서를 설정합니다.</p>
    <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="bo-add-main-slot-btn">스폰서 추가</button>
    </div>
    <div class="board-form-group">
        <table class="board-table sortable-table" id="bo-main-slots-table" data-sponsor-options="{{ e(json_encode($mainSponsorOptionsForForm, JSON_UNESCAPED_UNICODE)) }}">
            <thead>
                <tr>
                    <th>스폰서</th>
                    <th>배치</th>
                    <th class="text-center">순서</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="bo-main-slots-body">
                @forelse ($mainSlotsForForm as $mi => $row)
                    <tr class="bo-repeat-row">
                        <td>
                            <input type="hidden" name="main_sponsor_slots[{{ $mi }}][active]" value="0">
                            <input type="hidden" name="main_sponsor_slots[{{ $mi }}][active]" value="1">
                            <select name="main_sponsor_slots[{{ $mi }}][sponsor_index]" class="board-form-control bo-main-sponsor-select">
                                @foreach ($mainSponsorOptionsForForm as $option)
                                    <option value="{{ $option['sponsor_index'] }}" data-placement="{{ $option['placement'] }}" data-placement-label="{{ $option['placement_label'] }}" @selected((int) ($row['sponsor_index'] ?? 0) === (int) $option['sponsor_index'])>{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="bo-main-sponsor-placement-cell">
                            {{ $row['placement_label'] }}
                            <input type="hidden" name="main_sponsor_slots[{{ $mi }}][placement]" value="{{ $row['placement'] }}">
                        </td>
                        <td class="text-center sort-handle-cell">
                            <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                            <input type="hidden" name="main_sponsor_slots[{{ $mi }}][sort_order]" value="{{ (int) ($row['sort_order'] ?? ((int) $mi + 1)) }}">
                        </td>
                        <td><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted bo-main-slots-empty-row">노출 스폰서가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="js-academic-tab-panel @if ($activeTab !== 'prereg') bo-hidden @endif" data-tab-panel="prereg">
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">사전등록 시작일</label>
            <input type="date" name="pre_reg_start" class="board-form-control board-form-control--max-md" value="{{ old('pre_reg_start', $e->pre_reg_start?->format('Y-m-d')) }}">
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">사전등록 마감일</label>
            <input type="date" name="pre_reg_end" class="board-form-control board-form-control--max-md" value="{{ old('pre_reg_end', $e->pre_reg_end?->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">현장등록 시작일</label>
            <input type="date" name="onsite_reg_start" class="board-form-control board-form-control--max-md" value="{{ old('onsite_reg_start', $e->onsite_reg_start?->format('Y-m-d')) }}">
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">현장등록 마감일</label>
            <input type="date" name="onsite_reg_end" class="board-form-control board-form-control--max-md" value="{{ old('onsite_reg_end', $e->onsite_reg_end?->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">현장등록</label>
        <div class="board-radio-group">
            <label class="board-radio-item"><input type="radio" name="onsite_reg_allowed" value="allowed" @checked(old('onsite_reg_allowed', $e->onsite_reg_allowed ?? 'allowed') === 'allowed')> <span>허용</span></label>
            <label class="board-radio-item"><input type="radio" name="onsite_reg_allowed" value="disallowed" @checked(old('onsite_reg_allowed', $e->onsite_reg_allowed) === 'disallowed')> <span>불가</span></label>
        </div>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">사전등록 안내</label>
        <textarea name="pre_reg_guide" id="pre_reg_guide" class="board-form-control board-form-textarea" rows="5" data-backoffice-ckeditor="true">{{ old('pre_reg_guide', $e->pre_reg_guide) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">등록비 안내</label>
        <textarea name="reg_fee_guide" id="reg_fee_guide" class="board-form-control board-form-textarea" rows="5" data-backoffice-ckeditor="true">{{ old('reg_fee_guide', $e->reg_fee_guide) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">증빙서류 발급 안내</label>
        <textarea name="cert_doc_guide" id="cert_doc_guide" class="board-form-control board-form-textarea" rows="5" data-backoffice-ckeditor="true">{{ old('cert_doc_guide', $e->cert_doc_guide) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">참가 등록 안내</label>
        <textarea name="reg_info_guide" id="reg_info_guide" class="board-form-control board-form-textarea" rows="5" data-backoffice-ckeditor="true">{{ old('reg_info_guide', $e->reg_info_guide) }}</textarea>
    </div>
</div>

<div class="js-academic-tab-panel @if ($activeTab !== 'abstract') bo-hidden @endif" data-tab-panel="abstract">
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">초록 접수 시작일</label>
            <input type="date" name="abstract_start" class="board-form-control board-form-control--max-md" value="{{ old('abstract_start', $e->abstract_start?->format('Y-m-d')) }}">
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">초록 접수 마감일</label>
            <input type="date" name="abstract_end" class="board-form-control board-form-control--max-md" value="{{ old('abstract_end', $e->abstract_end?->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">수정 제출 마감일</label>
            <input type="date" name="abstract_revision_end" class="board-form-control board-form-control--max-md" value="{{ old('abstract_revision_end', $e->abstract_revision_end?->format('Y-m-d')) }}">
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">초록 심사 마감일</label>
            <input type="date" name="abstract_judging_end" class="board-form-control board-form-control--max-md" value="{{ old('abstract_judging_end', $e->abstract_judging_end?->format('Y-m-d')) }}">
        </div>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">심사 결과 발표일</label>
        <input type="date" name="abstract_result_date" class="board-form-control board-form-control--max-md" value="{{ old('abstract_result_date', $e->abstract_result_date?->format('Y-m-d')) }}">
    </div>
    <div class="board-form-group">
        <label class="board-form-label">초록집 업로드</label>
        <div class="board-file-upload mt-2">
            <div class="board-file-input-wrapper">
                <input type="file" name="abstract_book" id="academic_abstract_book_file" class="board-file-input" accept=".pdf,.zip" data-max-file-size-mb="50">
                <div class="board-file-input-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="board-file-input-text">초록집 파일을 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">PDF, ZIP (최대 50MB)</span>
                </div>
            </div>
            @if ($isEdit && $e->abstract_book_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-academic-abstract-book-existing-item">
                            <i class="fas fa-file-alt"></i>
                            <a href="{{ asset('storage/' . $e->abstract_book_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ basename($e->abstract_book_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="abstract_book">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_abstract_book" id="delete_abstract_book" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="academicAbstractBookFilePreview"></div>
        </div>
        @if ($isEdit && $e->abstract_book_path)
            <small class="board-form-text d-block mt-1">※ 기존 파일은 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>
    <div class="board-form-group">
        <label class="board-form-label">발표 구분</label>
        <div class="d-flex flex-wrap align-items-center">
            @foreach ($presentationTypeLabels as $code => $label)
                <label class="board-radio-item mb-0 @unless ($loop->last) mr-5 @endunless">
                    <input type="checkbox" name="presentation_types[]" value="{{ $code }}" @checked(in_array($code, $ptOld, true))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <small class="board-form-text d-block mt-2">※ 코드 관리에서 설정된 발표 구분을 선택하여 활성화할 수 있습니다.</small>
    </div>
    <div class="board-form-group">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <label class="board-form-label mb-0">발표 분야 관리</label>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="bo-add-abstract-field-btn">분야 추가</button>
        </div>
        <table class="board-table sortable-table" id="bo-abstract-fields-table">
            <thead>
                <tr>
                    <th class="text-center">순서</th>
                    <th>분야명</th>
                    <th class="text-center">관리</th>
                </tr>
            </thead>
            <tbody id="bo-abstract-fields-body">
                @foreach ($abstractFieldsRows as $fi => $row)
                    <tr class="bo-repeat-row">
                        <td class="text-center sort-handle-cell">
                            <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                            <input type="hidden" name="abstract_fields[{{ $fi }}][sort_order]" value="{{ (int) old('abstract_fields.' . $fi . '.sort_order', $row['sort_order'] ?? ((int) $fi + 1)) }}">
                        </td>
                        <td>
                            <input type="text" name="abstract_fields[{{ $fi }}][name]" class="board-form-control" value="{{ $row['name'] ?? '' }}" placeholder="분야명을 입력하세요">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">제출 기한 및 방식 안내</label>
        <textarea name="submission_guide" id="submission_guide" class="board-form-control board-form-textarea" rows="4" data-backoffice-ckeditor="true">{{ old('submission_guide', $e->submission_guide) }}</textarea>
    </div>
    <div class="board-form-group">
        <label class="board-form-label">주의사항 안내</label>
        <textarea name="abstract_notes" id="abstract_notes" class="board-form-control board-form-textarea" rows="4" data-backoffice-ckeditor="true">{{ old('abstract_notes', $e->abstract_notes) }}</textarea>
    </div>
</div>

<div class="js-academic-tab-panel @if ($activeTab !== 'speakers') bo-hidden @endif" data-tab-panel="speakers">
    <div class="text-right mb-2">
        @if ($isEdit && $e->exists)
            <button type="button" class="btn btn-sm btn-primary mb-1" id="bo-speaker-add-abstract-btn">연자 추가 (초록 연동)</button>
        @else
            <span class="board-form-help d-inline-block mr-2 mb-1">초록 연동은 행사 저장 후 이용할 수 있습니다.</span>
        @endif
        <div class="d-inline-block js-member-selector ml-2" id="bo-academic-speaker-member-selector" data-search-url="{{ route('backoffice.academic-events.search-members') }}">
            <input type="hidden" class="js-member-id" value="" tabindex="-1" aria-hidden="true">
            <input type="hidden" class="js-member-label" value="" tabindex="-1" aria-hidden="true">
            <input type="text" class="board-form-control js-member-display d-none" readonly value="" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button type="button" class="btn btn-sm btn-primary mb-1 js-open-member-modal" id="bo-speaker-add-member-btn">연자 추가 (회원)</button>
        </div>
        <button type="button" class="btn btn-sm btn-secondary mb-1 ml-2" id="bo-speaker-add-manual-btn">연자 직접 입력</button>
    </div>
    <table class="board-table bo-academic-inline-table" id="bo-speakers-table">
        <thead>
            <tr>
                <th>이름</th>
                <th>소속</th>
                <th>직책</th>
                <th>이미지</th>
                <th>초록 제목</th>
                <th>약력</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="bo-speakers-body">
            @foreach ($speakerRowsForForm as $si => $row)
                <tr class="bo-repeat-row bo-speaker-row">
                    <td>
                        <input type="hidden" name="speakers[{{ $si }}][source]" class="bo-speaker-source" value="{{ $row['source'] ?? 'manual' }}">
                        <input type="hidden" name="speakers[{{ $si }}][member_id]" class="bo-speaker-member-id" value="{{ $row['member_id'] ?? '' }}">
                        <input type="hidden" name="speakers[{{ $si }}][academic_event_abstract_id]" class="bo-speaker-abstract-id" value="{{ $row['academic_event_abstract_id'] ?? '' }}">
                        <input type="hidden" name="speakers[{{ $si }}][sort_order]" value="{{ (int) ($row['sort_order'] ?? ((int) $si + 1)) }}">
                        <input type="hidden" name="speakers[{{ $si }}][image_path]" value="{{ $row['image_path'] ?? '' }}">
                        <input type="text" name="speakers[{{ $si }}][name]" class="board-form-control bo-speaker-name" value="{{ $row['name'] ?? '' }}">
                    </td>
                    <td><input type="text" name="speakers[{{ $si }}][affiliation]" class="board-form-control bo-speaker-affiliation" value="{{ $row['affiliation'] ?? '' }}"></td>
                    <td><input type="text" name="speakers[{{ $si }}][position]" class="board-form-control bo-speaker-position" value="{{ $row['position'] ?? '' }}"></td>
                    <td>
                        <input type="file" name="speaker_images[{{ $si }}]" class="board-form-control" accept="image/*">
                        @if (! empty($row['image_path']))
                            <span class="board-form-help">{{ basename($row['image_path']) }}</span>
                        @endif
                    </td>
                    <td><input type="text" name="speakers[{{ $si }}][abstract_title]" class="board-form-control" value="{{ $row['abstract_title'] ?? '' }}"></td>
                    <td>
                        <input type="hidden" name="speakers[{{ $si }}][bio]" class="bo-speaker-bio" value="{{ $row['bio'] ?? '' }}">
                        <button type="button" class="btn btn-sm btn-outline-primary bo-speaker-bio-btn">{{ ! empty($row['bio']) ? '약력 수정' : '약력 입력' }}</button>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="js-academic-tab-panel @if ($activeTab !== 'sponsors') bo-hidden @endif" data-tab-panel="sponsors">
    <div class="text-right mb-2">
        <button type="button" class="btn btn-sm btn-primary mb-1" id="bo-sponsor-add-master-btn">스폰서 추가</button>
        <button type="button" class="btn btn-sm btn-secondary mb-1 ml-2" id="bo-sponsor-add-manual-btn">스폰서 직접 입력</button>
    </div>
    <div class="board-form-group mb-2">
        <label class="board-form-label">스폰서 구분</label>
        <select id="bo-sponsor-level-filter" class="board-form-control board-form-control--max-sm">
            <option value="all">전체</option>
            @foreach ($sponsorLevelLabels as $code => $label)
                <option value="{{ $code }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <table class="board-table sortable-table bo-academic-inline-table" id="bo-sponsors-table">
        <thead>
            <tr>
                <th>순서</th>
                <th>스폰서명</th>
                <th>배치</th>
                <th>로고</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="bo-sponsors-body">
            @foreach ($sponsorRowsForForm as $si => $row)
                <tr class="bo-repeat-row bo-sponsor-row">
                    <td class="text-center sort-handle-cell">
                        <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                    </td>
                    <td>
                        <input type="hidden" name="sponsors[{{ $si }}][academic_sponsor_master_id]" class="bo-sponsor-master-id" value="{{ $row['academic_sponsor_master_id'] ?? '' }}">
                        <input type="hidden" name="sponsors[{{ $si }}][sort_order]" value="{{ (int) ($row['sort_order'] ?? ((int) $si + 1)) }}">
                        <input type="hidden" name="sponsors[{{ $si }}][logo_path]" value="{{ $row['logo_path'] ?? '' }}">
                        <input type="text" name="sponsors[{{ $si }}][name]" class="board-form-control bo-sponsor-name" value="{{ $row['name'] ?? '' }}">
                    </td>
                    <td>
                        <select name="sponsors[{{ $si }}][level]" class="board-form-control">
                            @foreach ($sponsorLevelLabels as $code => $label)
                                <option value="{{ $code }}" @selected(($row['level'] ?? 'exhibitors') === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="file" name="sponsor_logos[{{ $si }}]" class="board-form-control" accept="image/*">
                        @if (! empty($row['logo_path']))
                            <span class="board-form-help">{{ basename($row['logo_path']) }}</span>
                        @endif
                    </td>
                    <td><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="js-academic-tab-panel @if ($activeTab !== 'sessions') bo-hidden @endif" data-tab-panel="sessions">
    @if ($isEdit)
        <div class="text-right mb-2">
            <a href="{{ route('backoffice.academic-events.sessions.create', $e) }}" class="btn btn-sm btn-success mb-1">세션 추가</a>
        </div>
    @else
        <p class="board-form-help mb-2 text-right">학술행사 등록 후 세션을 추가할 수 있습니다.</p>
    @endif
    <table class="board-table bo-academic-inline-table bo-academic-sessions-table">
        <thead>
            <tr>
                <th>세션명</th>
                <th>분류</th>
                <th>날짜</th>
                <th>시간</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @if ($isEdit)
                @forelse ($e->sessions ?? [] as $sess)
                    <tr>
                        <td>{{ $sess->name }}</td>
                        <td>{{ $sessionCategoryLabels[$sess->category] ?? '-' }}</td>
                        <td>{{ $sess->session_date?->format('Y-m-d') }}</td>
                        <td>{{ substr((string) $sess->start_time, 0, 5) }}~{{ substr((string) $sess->end_time, 0, 5) }}</td>
                        <td>
                        <a href="{{ route('backoffice.academic-events.sessions.abstracts', [$e, $sess]) }}" class="btn btn-sm btn-success">초록 관리</a>
                            <a href="{{ route('backoffice.academic-events.sessions.edit', [$e, $sess]) }}" class="btn btn-sm btn-primary">수정</a>
                            <button type="button" class="btn btn-sm btn-danger js-academic-session-delete-btn" data-delete-form-id="bo-session-delete-form-{{ $sess->id }}">삭제</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">등록된 세션이 없습니다.</td></tr>
                @endforelse
            @else
                <tr><td colspan="5" class="text-center">등록된 세션이 없습니다.</td></tr>
            @endif
        </tbody>
    </table>
</div>

<table class="d-none">
    <tbody>
        <tr id="bo-template-venue-floor" class="bo-template">
            <td><input type="text" name="venue_floors[__I__][floor_name]" class="board-form-control"></td>
            <td><input type="file" name="venue_floor_files[]" class="board-form-control" accept="image/*,.pdf"></td>
            <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button></td>
        </tr>
        <tr id="bo-template-abstract-field" class="bo-template">
            <td class="text-center sort-handle-cell">
                <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                <input type="hidden" name="abstract_fields[__I__][sort_order]" value="">
            </td>
            <td>
                <input type="text" name="abstract_fields[__I__][name]" class="board-form-control" placeholder="분야명을 입력하세요">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-secondary bo-remove-row-btn">삭제</button>
            </td>
        </tr>
    </tbody>
</table>

<div id="bo-speaker-bio-modal" class="modal fade bo-academic-modal d-none" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title mb-0" id="bo-speaker-bio-modal-title">연자 약력</h5>
                <button type="button" class="close bo-modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <textarea id="bo-speaker-bio-textarea" class="board-form-control board-form-textarea" rows="10"></textarea>
            </div>
            <div class="modal-footer bo-academic-modal-footer">
                <button type="button" class="btn btn-primary" id="bo-speaker-bio-save-btn">저장</button>
                <button type="button" class="btn btn-secondary bo-modal-close" data-dismiss="modal">취소</button>
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

<div class="modal js-abstract-search-modal bo-member-search-modal" id="bo-abstract-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">초록 검색 (이 행사)</h5>
            <button type="button" class="close js-close-abstract-modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="board-filter member-search-modal-filter">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">발표 분류</label>
                        <select class="filter-select js-abstract-presentation-type" id="bo-abstract-filter-presentation-type">
                            <option value="all">전체</option>
                            @foreach ($presentationTypeLabels as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">상태</label>
                        <select class="filter-select js-abstract-status" id="bo-abstract-filter-status">
                            <option value="all">전체</option>
                            @foreach (\App\Services\Backoffice\AcademicEventAbstractService::statusLabels() as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">검색어</label>
                        <input type="text" class="filter-input js-abstract-keyword" id="bo-abstract-filter-keyword" placeholder="저자명 또는 초록 제목">
                    </div>
                    <div class="filter-group">
                        <div class="filter-buttons">
                            <button type="button" class="btn btn-primary js-search-abstract"><i class="fas fa-search"></i> 검색</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>제목</th>
                            <th>저자</th>
                            <th>등록구분</th>
                            <th>발표유형</th>
                            <th>접수일</th>
                            <th>상태</th>
                            <th>파일수령</th>
                            <th>선택</th>
                        </tr>
                    </thead>
                    <tbody class="js-abstract-results">
                        <tr>
                            <td colspan="9" class="text-center">검색 버튼을 눌러 초록을 조회하세요.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 js-abstract-pagination"></div>
        </div>
    </div>
</div>

<div id="bo-sponsor-modal" class="modal fade bo-academic-modal d-none" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title mb-0">스폰서 검색</h5>
                <button type="button" class="close bo-modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="board-form-inline bo-sponsor-search-form">
                    <input type="text" id="bo-sponsor-search-keyword" class="board-form-control" placeholder="스폰서명">
                    <button type="button" class="btn btn-primary" id="bo-sponsor-search-btn">검색</button>
                </div>
                <div class="table-responsive mt-2">
                    <table class="board-table">
                        <thead><tr><th>스폰서명</th><th></th></tr></thead>
                        <tbody id="bo-sponsor-search-tbody">
                            <tr><td colspan="2" class="text-center">스폰서 목록을 조회하고 있습니다.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3" id="bo-sponsor-search-pagination"></div>
            </div>
        </div>
    </div>
</div>
