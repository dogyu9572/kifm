<h3 class="bo-section-title">기본 정보</h3>

<div class="bo-edu-form-row">
    <div class="board-form-group mb-0">
        <span class="board-form-label">시즌 <span class="required">*</span></span>
        <div class="board-radio-group">
            @foreach ($seasonLabels as $code => $label)
                <label class="board-radio-item">
                    <input type="radio" name="season" value="{{ $code }}"
                        @checked(old('season', optional($eduTraining)->season ?? 'spring') === $code)>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('season')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
    <div class="board-form-group mb-0">
        <label for="year" class="board-form-label">연도 <span class="required">*</span></label>
        <select name="year" id="year" class="board-form-control board-form-control--max-xs" required>
            @foreach ($yearOptions as $year)
                <option value="{{ $year }}" @selected((int) old('year', optional($eduTraining)->year ?? $yearOptions[0]) === $year)>
                    {{ $year }}년
                </option>
            @endforeach
        </select>
        @error('year')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="board-form-group">
    <label for="title" class="board-form-label">연수명 <span class="required">*</span></label>
    <input type="text" name="title" id="title" class="board-form-control board-form-control--max-lg"
        value="{{ old('title', optional($eduTraining)->title ?? '') }}" maxlength="200" required>
    @error('title')
        <span class="bo-inline-error">{{ $message }}</span>
    @enderror
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <span class="board-form-label">차수 사용 여부 <span class="required">*</span></span>
        <div class="board-radio-group">
            <label class="board-radio-item">
                <input type="radio" name="use_round" value="1" class="js-use-round"
                    @checked((bool) old('use_round', optional($eduTraining)->use_round ?? true))>
                <span>사용 (다회차 관리)</span>
            </label>
            <label class="board-radio-item">
                <input type="radio" name="use_round" value="0" class="js-use-round"
                    @checked(! (bool) old('use_round', optional($eduTraining)->use_round ?? true))>
                <span>미사용 (단발성 교육)</span>
            </label>
        </div>
        <small class="board-form-text">※ 사용 선택 시 차수별로 일시·장소 등을 나누어 관리합니다.</small>
        @error('use_round')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div id="bo-training-method-single" class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <label for="training_method_select" class="board-form-label">연수방식 <span class="required">*</span></label>
        <select id="training_method_select" class="board-form-control board-form-control--max-xs">
            @foreach ($methodLabels as $code => $label)
                <option value="{{ $code }}" @selected(old('training_method', optional($eduTraining)->training_method ?? 'offline') === $code)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <small class="board-form-text">※ 단발성 교육일 때만 사용합니다. 다회차일 때는 차수별 연수 방식을 입력하세요.</small>
        @error('training_method')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<h3 class="bo-section-title">소개 · 안내</h3>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <label for="overview" class="board-form-label">개요</label>
        <textarea name="overview" id="overview" class="board-form-control board-form-textarea" rows="10"
            data-backoffice-ckeditor data-source-editing="true">{{ old('overview', optional($eduTraining)->overview ?? '') }}</textarea>
        @error('overview')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <label for="program" class="board-form-label">프로그램</label>
        <textarea name="program" id="program" class="board-form-control board-form-textarea" rows="10"
            data-backoffice-ckeditor data-source-editing="true">{{ old('program', optional($eduTraining)->program ?? '') }}</textarea>
        @error('program')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <label for="registration_info" class="board-form-label">사전등록 안내 및 인정의 제도</label>
        <textarea name="registration_info" id="registration_info" class="board-form-control board-form-textarea" rows="10"
            data-backoffice-ckeditor data-source-editing="true">{{ old('registration_info', optional($eduTraining)->registration_info ?? '') }}</textarea>
        @error('registration_info')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <label for="introduction" class="board-form-label">연수 소개</label>
        <input type="text" name="introduction" id="introduction" class="board-form-control board-form-control--max-lg"
            value="{{ old('introduction', optional($eduTraining)->introduction ?? '') }}" maxlength="255">
        @error('introduction')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <label for="textbook_file" class="board-form-label">교재 다운로드</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="textbook_file" id="textbook_file" class="board-file-input"
                    accept=".pdf,.ppt,.pptx,.hwp" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="board-file-input-text">교재 파일을 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">PDF, PPT, HWP 파일만 가능 (최대 20MB)</span>
                </div>
            </div>
            @if (! empty(optional($eduTraining)->textbook_file_path))
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file">
                            <i class="fas fa-file"></i>
                            <a href="{{ asset('storage/' . $eduTraining->textbook_file_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ \App\Support\BackofficeFile::displayName($eduTraining->textbook_file_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-existing-textbook-remove="1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="textbookPreview"></div>
        </div>
        <div id="bo-removed-textbook"></div>
        @error('textbook_file')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

@php
    $gradeKeys = array_keys($gradeLabels);
    $defaultGrades = [];
    foreach ($gradeKeys as $gk) {
        $defaultGrades[$gk] = ['eligible' => false, 'price' => ''];
    }
    $bundleGrades = old('bundle_grades');
    if (! is_array($bundleGrades)) {
        $bundleGrades = $defaultGrades;
        $bundleSource = optional($eduTraining)->rounds?->first()?->grade_prices;
        $bundleSource = is_array($bundleSource) ? ($bundleSource[\App\Services\Backoffice\EduTrainingService::BUNDLE_GRADE_PRICE_KEY] ?? []) : [];
        foreach ($defaultGrades as $gk => $_) {
            if (isset($bundleSource[$gk]) && is_array($bundleSource[$gk])) {
                $bundleGrades[$gk] = [
                    'eligible' => filter_var($bundleSource[$gk]['eligible'] ?? false, FILTER_VALIDATE_BOOL),
                    'price' => $bundleSource[$gk]['price'] ?? '',
                ];
            }
        }
    }

    $existingRounds = old('rounds');
    if (! is_array($existingRounds)) {
        $existingRounds = optional($eduTraining)->rounds
            ? $eduTraining->rounds->map(function ($round) use ($defaultGrades) {
                $gp = is_array($round->grade_prices) ? $round->grade_prices : [];
                $gradesBlock = $defaultGrades;
                foreach ($defaultGrades as $gk => $_) {
                    if (isset($gp[$gk]) && is_array($gp[$gk])) {
                        $gradesBlock[$gk] = [
                            'eligible' => filter_var($gp[$gk]['eligible'] ?? false, FILTER_VALIDATE_BOOL),
                            'price' => $gp[$gk]['price'] ?? '',
                        ];
                    }
                }

                return [
                    'round_label' => $round->round_label,
                    'training_method' => $round->training_method,
                    'lecture_date' => optional($round->lecture_date)->format('Y-m-d'),
                    'location_link' => $round->location_link,
                    'apply_start_date' => optional($round->apply_start_date)->format('Y-m-d'),
                    'apply_end_date' => optional($round->apply_end_date)->format('Y-m-d'),
                    'capacity' => $round->capacity,
                    'is_capacity_unlimited' => $round->is_capacity_unlimited ? 1 : 0,
                    'duration_hours' => $round->duration_hours,
                    'score' => $round->score,
                    'grades' => $gradesBlock,
                    'status' => $round->status,
                ];
            })->values()->all()
            : [];
    }
    if (count($existingRounds) === 0) {
        $existingRounds = [[
            'round_label' => '1차',
            'training_method' => old('training_method', optional($eduTraining)->training_method ?? 'offline'),
            'lecture_date' => '',
            'location_link' => '',
            'apply_start_date' => '',
            'apply_end_date' => '',
            'capacity' => '',
            'is_capacity_unlimited' => 0,
            'duration_hours' => '',
            'score' => '',
            'grades' => $defaultGrades,
            'status' => old('status', optional($eduTraining)->status ?? 'PUBLIC'),
        ]];
    }
@endphp

<h3 class="bo-section-title" id="bo-round-section-title">차수별 설정</h3>

<div id="bo-rounds-section">
    <div class="bo-round-toolbar">
        <div id="bo-round-tabs" class="bo-round-tabs" role="tablist"></div>
        <div class="bo-round-tab-actions">
            <button type="button" class="btn btn-sm btn-outline-danger" id="bo-remove-current-round">− 현재 차수 삭제</button>
            <button type="button" class="btn btn-sm btn-outline-primary" id="bo-add-round-btn">+ 차수/기수 추가</button>
        </div>
    </div>

    <div class="board-card mb-3" id="bo-round-bundle-section">
        <div class="board-card-body">
            <h4 class="bo-section-title mb-3 mt-0">[전체 차시 일괄 결제 금액]</h4>
            <small class="board-form-text d-block mb-2">
                ※ 입력한 등급만 사용자 결제 페이지 최상단에 전체 차시 항목으로 표시됩니다.
            </small>
            <div class="bo-grade-row-list">
                @foreach ($gradeLabels as $gKey => $gLabel)
                    @php
                        $g = is_array($bundleGrades[$gKey] ?? null)
                            ? $bundleGrades[$gKey]
                            : ['eligible' => false, 'price' => ''];
                        $gEligible = ! empty($g['eligible']);
                    @endphp
                    <div class="bo-grade-row bo-grade-row-item">
                        <label class="board-radio-item mb-0">
                            <input type="hidden" name="bundle_grades[{{ $gKey }}][eligible]" value="0">
                            <input type="checkbox" value="1" class="js-bundle-grade-eligible"
                                name="bundle_grades[{{ $gKey }}][eligible]"
                                @checked($gEligible)>
                            <span>{{ $gLabel }}</span>
                        </label>
                        <div
                            class="bo-training-price-wrap d-flex align-items-center gap-2 js-bundle-grade-price-wrap @if (! $gEligible) bo-hidden @endif">
                            <input type="number" step="1" min="0"
                                class="board-form-control board-form-control--max-xs js-bundle-grade-price"
                                name="bundle_grades[{{ $gKey }}][price]"
                                value="{{ $g['price'] ?? '' }}"
                                placeholder="전체 차시 금액 (원)"
                                autocomplete="off">
                            <span class="board-form-text mb-0">원</span>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('bundle_grades')
                <span class="bo-inline-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div id="bo-round-panels">
        @foreach ($existingRounds as $idx => $round)
            <div class="bo-round-panel board-card mb-3 @if ($idx > 0) bo-hidden @endif" data-panel-index="{{ $idx }}">
                <div class="board-card-body">
                    <h4 class="bo-section-title mb-3 mt-0">
                        [<span class="js-round-heading-label">{{ $round['round_label'] ?? ($idx + 1) . '차' }}</span> 연수 설정]
                    </h4>
                    <input type="hidden" name="rounds[{{ $idx }}][round_label]" value="{{ $round['round_label'] ?? ($idx + 1) . '차' }}">

                    <div class="bo-edu-form-row">
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">연수 방식 <span class="required">*</span></label>
                            <select name="rounds[{{ $idx }}][training_method]" class="board-form-control board-form-control--max-xs js-round-method">
                                @foreach ($methodLabels as $methodCode => $methodLabel)
                                    <option value="{{ $methodCode }}" @selected(($round['training_method'] ?? '') === $methodCode)>{{ $methodLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">강의 일시 <span class="required">*</span></label>
                            <input type="date" class="board-form-control board-form-control--max-xs"
                                name="rounds[{{ $idx }}][lecture_date]" value="{{ $round['lecture_date'] ?? '' }}">
                        </div>
                    </div>

                    <div class="board-form-group">
                        <label class="board-form-label">장소 / 링크 <span class="required">*</span></label>
                        <input type="text" class="board-form-control board-form-control--max-lg"
                            name="rounds[{{ $idx }}][location_link]" value="{{ $round['location_link'] ?? '' }}">
                        <small class="board-form-text">※ 온라인의 경우 웨비나 링크를 입력하세요.</small>
                    </div>

                    <div class="bo-edu-form-row">
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">신청 시작일</label>
                            <input type="date" class="board-form-control board-form-control--max-xs"
                                name="rounds[{{ $idx }}][apply_start_date]" value="{{ $round['apply_start_date'] ?? '' }}">
                        </div>
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">신청 종료일</label>
                            <input type="date" class="board-form-control board-form-control--max-xs"
                                name="rounds[{{ $idx }}][apply_end_date]" value="{{ $round['apply_end_date'] ?? '' }}">
                        </div>
                    </div>

                    <div class="bo-edu-form-row">
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">정원</label>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <input type="number" class="board-form-control board-form-control--max-xs js-capacity-input"
                                    name="rounds[{{ $idx }}][capacity]" value="{{ $round['capacity'] ?? '' }}" min="1">
                                <label class="board-radio-item mb-0">
                                    <input type="checkbox" value="1" class="js-capacity-unlimited"
                                        name="rounds[{{ $idx }}][is_capacity_unlimited]"
                                        @checked((int) ($round['is_capacity_unlimited'] ?? 0) === 1)>
                                    <span>제한 없음</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bo-edu-form-row">
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">연수 시간 <span class="required">*</span></label>
                            <input type="number" step="0.5" min="0" class="board-form-control board-form-control--max-xs"
                                name="rounds[{{ $idx }}][duration_hours]" value="{{ $round['duration_hours'] ?? '' }}">
                        </div>
                        <div class="board-form-group mb-0">
                            <label class="board-form-label">이수 평점 <span class="required">*</span></label>
                            <input type="number" step="0.5" min="0" class="board-form-control board-form-control--max-xs"
                                name="rounds[{{ $idx }}][score]" value="{{ $round['score'] ?? '' }}">
                        </div>
                    </div>

                    <div class="board-form-group bo-edu-form-row-full mb-0">
                        <span class="board-form-label">등록비 설정</span>
                        <small class="board-form-text d-block mb-2">
                            ※ 체크된 회원 등급만 등록 신청이 가능합니다. 금액을 입력하면 유료 연수로 설정됩니다.
                        </small>
                        <div class="bo-grade-row-list">
                            @foreach ($gradeLabels as $gKey => $gLabel)
                                @php
                                    $g = is_array($round['grades'][$gKey] ?? null)
                                        ? $round['grades'][$gKey]
                                        : ['eligible' => false, 'price' => ''];
                                    $gEligible = ! empty($g['eligible']);
                                @endphp
                                <div class="bo-grade-row bo-grade-row-item">
                                    <label class="board-radio-item mb-0">
                                        <input type="hidden" name="rounds[{{ $idx }}][grades][{{ $gKey }}][eligible]" value="0">
                                        <input type="checkbox" value="1" class="js-grade-eligible"
                                            name="rounds[{{ $idx }}][grades][{{ $gKey }}][eligible]"
                                            @checked($gEligible)>
                                        <span>{{ $gLabel }}</span>
                                    </label>
                                    <div
                                        class="bo-training-price-wrap d-flex align-items-center gap-2 js-grade-price-wrap @if (! $gEligible) bo-hidden @endif">
                                        <input type="number" step="1" min="0"
                                            class="board-form-control board-form-control--max-xs js-grade-price"
                                            name="rounds[{{ $idx }}][grades][{{ $gKey }}][price]"
                                            value="{{ $g['price'] ?? '' }}"
                                            placeholder="등록비 (원)"
                                            autocomplete="off">
                                        <span class="board-form-text mb-0">원</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="board-form-group mb-0">
                        <label class="board-form-label">차수 공개 여부</label>
                        <select name="rounds[{{ $idx }}][status]" class="board-form-control board-form-control--max-xs">
                            @foreach ($statusLabels as $statusCode => $statusLabel)
                                <option value="{{ $statusCode }}" @selected(($round['status'] ?? 'PUBLIC') === $statusCode)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @error('rounds')
        <div><span class="bo-inline-error">{{ $message }}</span></div>
    @enderror
</div>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <span class="board-form-label">첨부파일</span>
        <div class="bo-attachment-row mb-2">
            <div class="board-file-upload">
                <div class="board-file-input-wrapper">
                    <input type="file" name="attachment_files[]" class="board-file-input bo-attachment-file-input"
                        id="attachment_files" accept=".pdf,.ppt,.pptx,.hwp" multiple data-max-file-size-mb="20">
                    <div class="board-file-input-content">
                        <i class="fas fa-paperclip"></i>
                        <span class="board-file-input-text">첨부파일을 선택하거나 여기로 드래그하세요</span>
                        <span class="board-file-input-subtext">PDF, PPT, HWP 파일만 가능 (최대 20MB)</span>
                    </div>
                </div>
                @if ($attachments->isNotEmpty())
                    <div class="board-existing-files mt-2">
                        <div class="board-attachment-list">
                            @foreach ($attachments as $att)
                                <div class="board-attachment-item existing-file" data-index="{{ $att->id }}">
                                    <i class="fas fa-file"></i>
                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" rel="noopener">
                                        <span class="board-attachment-name">{{ $att->original_name ?: \App\Support\BackofficeFile::displayName($att->file_path) }}</span>
                                    </a>
                                    <button type="button" class="board-attachment-remove" data-existing-attachment-id="{{ $att->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="board-file-preview" id="attachmentFilePreview"></div>
            </div>
        </div>
        <div id="bo-removed-attachments"></div>
        <small class="board-form-text d-block mt-1">※ PDF, PPT, HWP 파일 업로드 가능 (최대 20MB)</small>
        @error('attachment_files')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
        @error('attachment_files.*')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
        @error('delete_attachment_ids')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
        @error('delete_attachment_ids.*')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<h3 class="bo-section-title">노출</h3>

<div class="bo-edu-form-row">
    <div class="board-form-group bo-edu-form-row-full mb-0">
        <span class="board-form-label">공개 여부 <span class="required">*</span></span>
        <div class="board-radio-group">
            @foreach ($statusLabels as $code => $label)
                <label class="board-radio-item">
                    <input type="radio" name="status" value="{{ $code }}"
                        @checked(old('status', optional($eduTraining)->status ?? 'PUBLIC') === $code)>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('status')
            <span class="bo-inline-error">{{ $message }}</span>
        @enderror
    </div>
</div>
