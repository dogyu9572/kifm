@php
    $isEdit = $course->exists;
    $activeTab = old('active_tab', 'basic');
    $selectedProfessorLabel = old('professor_member_label');
    if ($selectedProfessorLabel === null && $course->professor_member_id) {
        $selectedProfessorLabel = ($course->professorMember->name ?? $course->professor_name ?? '') . ' (' . ($course->professorMember->workplace_name ?? $course->professor_org ?? '-') . ')';
    }
    $professorDisplayName = old('professor_name', $course->professorMember->name ?? $course->professor_name);
    $professorDisplayOrg = old('professor_org', $course->professorMember->workplace_name ?? $course->professor_org);
    $link = old('link');
    if (! is_array($link)) {
        $link = [];
    }
    $annualFeeTargetValue = old('annual_fee_target', $course->annual_fee_target ?? 'all');
    $freeYnValue = $annualFeeTargetValue === 'paid' ? 'N' : old('free_yn', $course->free_yn ?? 'N');
@endphp

<div class="bo-round-toolbar mb-3">
    <div class="bo-round-tabs" role="tablist">
        <button type="button" class="btn btn-sm btn-outline-primary js-course-tab-btn @if($activeTab === 'basic') active @endif" data-tab="basic">기본 정보</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-course-tab-btn @if($activeTab === 'pricing') active @endif" data-tab="pricing">과금 및 수강 설정</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-course-tab-btn @if($activeTab === 'exam') active @endif" data-tab="exam">시험 관리</button>
        <button type="button" class="btn btn-sm btn-outline-primary js-course-tab-btn @if($activeTab === 'link') active @endif" data-tab="link">연동 설정</button>
    </div>
</div>
<input type="hidden" name="active_tab" id="bo-edu-course-active-tab" value="{{ $activeTab }}">

<div class="js-course-tab-panel @if($activeTab !== 'basic') bo-hidden @endif" data-tab-panel="basic">
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">과정 유형 <span class="required">*</span></label>
            <select name="course_type" id="course_type" class="board-form-control board-form-control--max-xs js-course-type">
                <option value="" @selected(old('course_type', $course->course_type) === null || old('course_type', $course->course_type) === '')>선택</option>
                @foreach ($courseTypeLabels as $code => $label)
                    <option value="{{ $code }}" @selected(old('course_type', $course->course_type) === $code)>{{ $label }}</option>
                @endforeach
            </select>
            @error('course_type')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">개설연도 <span class="required">*</span></label>
            <select name="open_year" class="board-form-control board-form-control--max-xs">
                @foreach ($yearOptions as $year)
                    <option value="{{ $year }}" @selected((int) old('open_year', $course->open_year ?: $yearOptions[0]) === (int) $year)>{{ $year }}년</option>
                @endforeach
            </select>
            @error('open_year')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
    </div>

    <div id="bo-course-linked-event-wrap" class="board-form-group @if(old('course_type', $course->course_type) !== 'conference') bo-hidden @endif">
        <label class="board-form-label">연계 학술대회 <span class="required">*</span></label>
        <select name="linked_event_id" class="board-form-control board-form-control--max-md">
            <option value="">연계할 학술대회를 선택하세요</option>
            @foreach ($linkedEvents as $event)
                <option value="{{ $event['id'] }}" @selected((string) old('linked_event_id', $course->linked_event_id) === (string) $event['id'])>{{ $event['title'] }}</option>
            @endforeach
        </select>
        <small class="board-form-text">※ 학술대회 신청자에게 이 강좌가 자동으로 노출됩니다.</small>
        @error('linked_event_id')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>

    <div class="board-form-group">
        <label class="board-form-label">강의 키워드</label>
        <input type="text" name="keywords" class="board-form-control board-form-control--max-lg" value="{{ old('keywords', $course->keywords) }}">
        <small class="board-form-text">※ 쉼표(,)를 구분자로 직접 입력하세요.</small>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">강의 주제</label>
        <input type="text" name="topics" class="board-form-control board-form-control--max-lg" value="{{ old('topics', $course->topics) }}">
        <small class="board-form-text">※ 쉼표(,)를 구분자로 직접 입력하세요.</small>
    </div>

    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">상단 노출여부 <span class="required">*</span></label>
            <div class="board-radio-group">
                <label class="board-radio-item"><input type="radio" name="expose_yn" value="Y" @checked(old('expose_yn', $course->expose_yn ?? 'Y') === 'Y')> <span>노출</span></label>
                <label class="board-radio-item"><input type="radio" name="expose_yn" value="N" @checked(old('expose_yn', $course->expose_yn) === 'N')> <span>미노출</span></label>
            </div>
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">사용여부 <span class="required">*</span></label>
            <div class="board-radio-group">
                <label class="board-radio-item"><input type="radio" name="use_yn" value="Y" @checked(old('use_yn', $course->use_yn ?? 'Y') === 'Y')> <span>사용</span></label>
                <label class="board-radio-item"><input type="radio" name="use_yn" value="N" @checked(old('use_yn', $course->use_yn) === 'N')> <span>미사용</span></label>
            </div>
        </div>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">썸네일</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="thumbnail" id="thumbnail_file" class="board-file-input" accept=".jpg,.jpeg,.png,.gif" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-image"></i>
                    <span class="board-file-input-text">썸네일 파일을 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">JPG, PNG, GIF 파일만 가능 (최대 20MB)</span>
                </div>
            </div>
            @if ($isEdit && $course->thumbnail_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-thumbnail-existing-item">
                            <i class="fas fa-file-image"></i>
                            <a href="{{ asset('storage/' . $course->thumbnail_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ \App\Support\BackofficeFile::displayName($course->thumbnail_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="thumbnail">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_thumbnail" id="delete_thumbnail" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="thumbnailFilePreview"></div>
        </div>
        @if ($isEdit && $course->thumbnail_path)
            <small class="board-form-text d-block mt-1">※ 기존 썸네일은 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>

    <div class="board-form-group">
        <label class="board-form-label">강의 제목 <span class="required">*</span></label>
        <input type="text" name="title" class="board-form-control board-form-control--max-lg" value="{{ old('title', $course->title) }}" required>
    </div>

    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">교수명 <span class="required">*</span></label>
            <input type="text" id="professor_name" class="board-form-control" value="{{ $professorDisplayName }}" readonly>
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">교수 소속</label>
            <input type="text" id="professor_org" class="board-form-control" value="{{ $professorDisplayOrg }}" readonly>
        </div>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">회원 연동 <span class="required">*</span></label>
        <div class="input-with-button bo-member-select-inline js-member-selector js-professor-selector"
            data-search-url="{{ route('backoffice.edu-courses.search-members') }}">
            <input type="hidden" name="professor_member_id" id="professor_member_id" class="js-member-id" value="{{ old('professor_member_id', $course->professor_member_id) }}">
            <input type="hidden" name="professor_member_label" class="js-member-label" value="{{ $selectedProfessorLabel }}">
            <input type="text" class="board-form-control js-member-display" id="professor_member_display" value="{{ $selectedProfessorLabel }}" placeholder="회원 DB 검색으로 교수를 선택하세요." readonly>
            <button type="button" class="btn btn-secondary btn-sm js-open-member-modal">회원 검색</button>
        </div>
        <small class="board-form-text">회원 선택 시 교수명·소속이 자동 입력됩니다.</small>
        @error('professor_member_id')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>

    <div class="board-form-group">
        <label class="board-form-label">강의주제 상세</label>
        <textarea name="topic_detail" class="board-form-control board-form-textarea" rows="4">{{ old('topic_detail', $course->topic_detail) }}</textarea>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">강의 내용</label>
        <textarea name="content" class="board-form-control board-form-textarea" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('content', $course->content) }}</textarea>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">강의록</label>
        <div class="board-file-upload">
            <div class="board-file-input-wrapper">
                <input type="file" name="lecture_file" id="lecture_file" class="board-file-input" accept=".pdf,.ppt,.pptx,.doc,.docx,.hwp" data-max-file-size-mb="20">
                <div class="board-file-input-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="board-file-input-text">강의록 파일을 선택하거나 여기로 드래그하세요</span>
                    <span class="board-file-input-subtext">PDF, PPT, DOC, HWP 파일만 가능 (최대 20MB)</span>
                </div>
            </div>
            @if ($isEdit && $course->lecture_file_path)
                <div class="board-existing-files mt-2">
                    <div class="board-attachment-list">
                        <div class="board-attachment-item existing-file" id="bo-lecture-existing-item">
                            <i class="fas fa-file"></i>
                            <a href="{{ asset('storage/' . $course->lecture_file_path) }}" target="_blank" rel="noopener">
                                <span class="board-attachment-name">{{ \App\Support\BackofficeFile::displayName($course->lecture_file_path) }}</span>
                            </a>
                            <button type="button" class="board-attachment-remove" data-remove-existing-target="lecture">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" name="delete_lecture_file" id="delete_lecture_file" value="1" class="bo-hidden">
                        </div>
                    </div>
                </div>
            @endif
            <div class="board-file-preview" id="lectureFilePreview"></div>
        </div>
        @if ($isEdit && $course->lecture_file_path)
            <small class="board-form-text d-block mt-1">※ 기존 강의록은 x 버튼 클릭 시 삭제됩니다.</small>
        @endif
    </div>

    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">강의 URL</label>
            <input type="url" name="video_url" class="board-form-control" value="{{ old('video_url', $course->video_url) }}">
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">강의시간 <span class="required">*</span></label>
            @php
                $durationTotalSec = (int) old('duration_sec', $course->duration_sec ?? ((int) ($course->duration_min ?? 0) * 60));
                $durationMin = old('duration_min', intdiv($durationTotalSec, 60));
                $durationSeconds = old('duration_seconds', $durationTotalSec % 60);
            @endphp
            <div class="board-inline-form bo-inline-form d-inline-flex align-items-center">
                <input type="number" name="duration_min" class="board-form-control board-form-control--max-xs" value="{{ $durationMin }}" min="0">
                <span class="ml-2">분</span>
                <input type="number" name="duration_seconds" class="board-form-control board-form-control--max-xs ml-2" value="{{ $durationSeconds }}" min="0" max="59">
                <span class="ml-2">초</span>
            </div>
            @error('duration_min')<span class="bo-inline-error">{{ $message }}</span>@enderror
            @error('duration_seconds')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">수강 완료 평점 <span class="required">*</span></label>
        <div class="board-inline-form bo-inline-form d-inline-flex align-items-center">
            <input type="number" name="completion_score" class="board-form-control board-form-control--max-xs" value="{{ old('completion_score', $course->completion_score ?? 0) }}" min="0">
            <span class="ml-2">점</span>
        </div>
    </div>
</div>

<div class="js-course-tab-panel @if($activeTab !== 'pricing') bo-hidden @endif" data-tab-panel="pricing">
    <div class="board-form-group">
        <label class="board-form-label">연회비 납부 대상 강의 <span class="required">*</span></label>
        <div class="board-radio-group">
            <label class="board-radio-item"><input type="radio" name="annual_fee_target" value="all" class="js-annual-fee-target" @checked($annualFeeTargetValue === 'all')> <span>전체</span></label>
            <label class="board-radio-item"><input type="radio" name="annual_fee_target" value="paid" class="js-annual-fee-target" @checked($annualFeeTargetValue === 'paid')> <span>납부대상</span></label>
        </div>
        @error('annual_fee_target')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>

    <div id="bo-grade-price-wrap" class="board-form-group @if($freeYnValue === 'Y') bo-hidden @endif">
        <label class="board-form-label">수강 가능 회원 <span class="required">*</span></label>
        <div class="bo-grade-row-list">
            @foreach ($gradeLabels as $gradeCode => $gradeLabel)
                @php
                    $existingGrade = $course->gradePrices->firstWhere('grade_code', $gradeCode);
                    $enabled = old("grade_prices.$gradeCode.enabled", $existingGrade?->is_enabled ?? false);
                    $price = old("grade_prices.$gradeCode.price", $existingGrade?->price);
                @endphp
                <div class="bo-grade-row bo-grade-row-item">
                    <label class="board-radio-item mb-0">
                        <input type="hidden" name="grade_prices[{{ $gradeCode }}][enabled]" value="0">
                        <input type="checkbox" name="grade_prices[{{ $gradeCode }}][enabled]" value="1" class="js-grade-eligible" @checked($enabled)>
                        <span>{{ $gradeLabel }}</span>
                    </label>
                    <div class="bo-training-price-wrap d-flex align-items-center gap-2 js-grade-price-wrap @if(! $enabled) bo-hidden @endif">
                        <input type="number"
                            class="board-form-control board-form-control--max-xs js-grade-price"
                            name="grade_prices[{{ $gradeCode }}][price]"
                            value="{{ $price }}"
                            min="0"
                            placeholder="등록비 (원)"
                            autocomplete="off">
                        <span class="board-form-text mb-0">원</span>
                    </div>
                </div>
            @endforeach
        </div>
        @error('grade_prices')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>

    <div class="board-form-group">
        <label class="board-form-label">무료 제공 여부 <span class="required">*</span></label>
        <div class="board-radio-group">
            <label class="board-radio-item"><input type="radio" name="free_yn" value="Y" class="js-free-yn" @checked($freeYnValue === 'Y') @disabled($annualFeeTargetValue === 'paid')> <span>제공</span></label>
            <label class="board-radio-item"><input type="radio" name="free_yn" value="N" class="js-free-yn" @checked($freeYnValue === 'N')> <span>미제공</span></label>
        </div>
        @error('free_yn')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>

    <div id="bo-free-period-wrap" class="bo-edu-form-row @if($freeYnValue !== 'Y') bo-hidden @endif">
        <div class="board-form-group mb-0">
            <label class="board-form-label">무료 제공 시작일</label>
            <input type="date" name="free_start_date" class="board-form-control board-form-control--max-xs" value="{{ old('free_start_date', optional($course->free_start_date)->format('Y-m-d')) }}">
            @error('free_start_date')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">무료 제공 종료일</label>
            <input type="date" name="free_end_date" class="board-form-control board-form-control--max-xs" value="{{ old('free_end_date', optional($course->free_end_date)->format('Y-m-d')) }}">
            @error('free_end_date')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="board-form-group">
        <label class="board-form-label">유료 강좌 기간 <span class="required">*</span></label>
        <div class="board-radio-group">
            <label class="board-radio-item"><input type="radio" name="period_type" value="days" class="js-period-type" @checked(old('period_type', $course->period_type ?? 'days') === 'days')> <span>신청일부터</span></label>
            <label class="board-radio-item"><input type="radio" name="period_type" value="range" class="js-period-type" @checked(old('period_type', $course->period_type) === 'range')> <span>특정 기간 설정</span></label>
        </div>
        @error('period_type')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>
    <div id="bo-period-days-wrap" class="board-form-group @if(old('period_type', $course->period_type ?? 'days') !== 'days') bo-hidden @endif">
        <label class="board-form-label">수강 기간</label>
        <div class="board-inline-form bo-inline-form d-inline-flex align-items-center">
            <input type="number" name="duration_days" class="board-form-control board-form-control--max-xs" min="1" value="{{ old('duration_days', $course->duration_days ?? 30) }}">
            <span class="ml-2">일</span>
        </div>
        @error('duration_days')<span class="bo-inline-error">{{ $message }}</span>@enderror
    </div>
    <div id="bo-period-range-wrap" class="bo-edu-form-row @if(old('period_type', $course->period_type ?? 'days') !== 'range') bo-hidden @endif">
        <div class="board-form-group mb-0">
            <label class="board-form-label">수강 시작일</label>
            <input type="date" name="period_start" class="board-form-control board-form-control--max-xs" value="{{ old('period_start', optional($course->period_start)->format('Y-m-d')) }}">
            @error('period_start')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">수강 종료일</label>
            <input type="date" name="period_end" class="board-form-control board-form-control--max-xs" value="{{ old('period_end', optional($course->period_end)->format('Y-m-d')) }}">
            @error('period_end')<span class="bo-inline-error">{{ $message }}</span>@enderror
        </div>
    </div>
</div>

<div class="js-course-tab-panel @if($activeTab !== 'exam') bo-hidden @endif" data-tab-panel="exam">
    <div class="board-form-group">
        <label class="board-form-label">시험 여부 <span class="required">*</span></label>
        <div class="board-radio-group">
            <label class="board-radio-item"><input type="radio" name="exam_yn" value="Y" class="js-exam-yn" @checked(old('exam_yn', $course->exam_yn ?? 'Y') === 'Y')> <span>사용</span></label>
            <label class="board-radio-item"><input type="radio" name="exam_yn" value="N" class="js-exam-yn" @checked(old('exam_yn', $course->exam_yn ?? 'Y') === 'N')> <span>미사용</span></label>
        </div>
    </div>

    @php
        $oldQuestions = old('exam_questions');
        if (! is_array($oldQuestions)) {
            $oldQuestions = $course->examQuestions->map(function ($q) {
                return [
                    'question' => $q->question,
                    'choices' => $q->choices_json ?? [],
                    'answer_index' => $q->answer_index,
                ];
            })->toArray();
        }
    @endphp
    <div id="bo-exam-questions-wrap" @if(old('exam_yn', $course->exam_yn ?? 'Y') !== 'Y') class="bo-hidden" @endif>
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="bo-section-title mb-0">수강 완료 시험 문제</h4>
            <button type="button" class="btn btn-primary btn-sm" id="bo-add-exam-question">
                <i class="fas fa-plus"></i> 문제 추가
            </button>
        </div>
        <p class="board-form-text">시험 통과 기준: 모든 문제를 맞추어야(100%) 수강 완료 처리됩니다.</p>
        <div id="bo-exam-question-list">
            @foreach ($oldQuestions as $idx => $question)
                @php
                    $choices = is_array($question['choices'] ?? null) ? array_values($question['choices']) : [];
                    if (count($choices) < 4) {
                        $choices = array_pad($choices, 4, '');
                    }
                @endphp
                <div class="board-card mb-2 js-exam-question-card" data-index="{{ $idx }}">
                    <div class="board-card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="mb-0">문제 <span class="js-question-order-label">{{ $idx + 1 }}</span></h5>
                            <div class="board-btn-group">
                                <button type="button" class="btn btn-outline-secondary btn-sm js-move-exam-up" aria-label="위로 이동" title="위로 이동"><i class="fas fa-chevron-up"></i></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm js-move-exam-down" aria-label="아래로 이동" title="아래로 이동"><i class="fas fa-chevron-down"></i></button>
                                <button type="button" class="btn btn-outline-danger btn-sm js-remove-exam-question">삭제</button>
                            </div>
                        </div>
                        <div class="board-form-group">
                            <label class="board-form-label">문제</label>
                            <textarea class="board-form-control" rows="2" name="exam_questions[{{ $idx }}][question]">{{ $question['question'] ?? '' }}</textarea>
                        </div>
                        <div class="board-form-group mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="board-form-label mb-0">보기 (라디오 선택 = 정답)</label>
                                <button type="button" class="btn btn-outline-primary btn-sm js-add-exam-choice">+ 보기 추가</button>
                            </div>
                            <div class="js-choice-list">
                                @foreach ($choices as $choiceIdx => $choiceValue)
                                    <div class="d-flex align-items-center gap-2 mb-2 js-choice-row" data-choice-index="{{ $choiceIdx }}">
                                        <label class="board-radio-item mb-0">
                                            <input type="radio" class="js-answer-radio"
                                                name="exam_questions[{{ $idx }}][answer_index]"
                                                value="{{ $choiceIdx }}"
                                                @checked((int) ($question['answer_index'] ?? 0) === $choiceIdx)>
                                        </label>
                                        <input type="text"
                                            class="board-form-control"
                                            name="exam_questions[{{ $idx }}][choices][{{ $choiceIdx }}]"
                                            value="{{ $choiceValue }}"
                                            placeholder="보기 {{ $choiceIdx + 1 }}">
                                        <button type="button" class="btn btn-outline-danger btn-sm js-remove-exam-choice">삭제</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="js-course-tab-panel @if($activeTab !== 'link') bo-hidden @endif" data-tab-panel="link">
    @php
        $selectedLinkEventId = old('link_event_id', optional($course->linkSetting)->link_event_id);
        $selectedLinkTrainingId = old('link_training_id', optional($course->linkSetting)->link_training_id);
        $hasAnyLinkTarget = ! empty($selectedLinkEventId) || ! empty($selectedLinkTrainingId);
    @endphp
    <div class="bo-edu-form-row">
        <div class="board-form-group mb-0">
            <label class="board-form-label">연동 행사 (학술대회 연계 과정 전용)</label>
            <select name="link_event_id" class="board-form-control board-form-control--max-md js-link-event">
                <option value="">연동 없음</option>
                @foreach ($linkedEvents as $event)
                    <option value="{{ $event['id'] }}" @selected((string) old('link_event_id', optional($course->linkSetting)->link_event_id) === (string) $event['id'])>{{ $event['title'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="board-form-group mb-0">
            <label class="board-form-label">연동 연수 (연수교육 전용)</label>
            <select name="link_training_id" class="board-form-control board-form-control--max-md js-link-training">
                <option value="">연동 없음</option>
                @foreach ($linkedTrainings as $training)
                    <option value="{{ $training->id }}" @selected((string) old('link_training_id', optional($course->linkSetting)->link_training_id) === (string) $training->id)>{{ $training->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div id="bo-link-round-wrap" class="@if(! $selectedLinkTrainingId) bo-hidden @endif">
        <div class="board-form-group">
            <label class="board-form-label">차수별 연동 사용 여부</label>
            <div class="board-radio-group">
                <label class="board-radio-item"><input type="radio" name="link_round_use" value="N" class="js-link-round-use" @checked(old('link_round_use', optional($course->linkSetting)->link_round_use ?? 'N') === 'N')> <span>미사용(전체 연동)</span></label>
                <label class="board-radio-item"><input type="radio" name="link_round_use" value="Y" class="js-link-round-use" @checked(old('link_round_use', optional($course->linkSetting)->link_round_use) === 'Y')> <span>사용(차수 선택)</span></label>
            </div>
        </div>
        <div id="bo-link-round-choices" class="board-form-group @if(old('link_round_use', optional($course->linkSetting)->link_round_use ?? 'N') !== 'Y') bo-hidden @endif">
            @php
                $roundSelected = old('link_round_ids', optional($course->linkSetting)->link_round_ids ?? []);
            @endphp
            <div class="d-flex align-items-center flex-nowrap">
                @foreach (['1차', '2차', '3차', '4차', '5차'] as $roundLabel)
                    <label class="checkbox-label d-inline-flex align-items-center mb-0 mr-3">
                        <input type="checkbox" name="link_round_ids[]" value="{{ $roundLabel }}" @checked(in_array($roundLabel, $roundSelected, true))>
                        <span>{{ $roundLabel }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    <div id="bo-link-free-wrap" class="@if(! $hasAnyLinkTarget) bo-hidden @endif">
        <div class="board-form-group">
            <label class="board-form-label">신청자 무료 수강 기간</label>
            @php
                $linkType = old('link_period_type', optional($course->linkSetting)->link_period_type ?? 'days');
            @endphp
            <div class="board-radio-group">
                <label class="board-radio-item"><input type="radio" name="link_period_type" value="days" class="js-link-period-type" @checked($linkType === 'days')> <span>신청일부터</span></label>
                <label class="board-radio-item"><input type="radio" name="link_period_type" value="range" class="js-link-period-type" @checked($linkType === 'range')> <span>특정 기간 설정</span></label>
            </div>
        </div>
        <div id="bo-link-period-days-wrap" class="board-form-group @if($linkType !== 'days') bo-hidden @endif">
            <div class="board-inline-form bo-inline-form d-inline-flex align-items-center">
                <input type="number" name="link_duration_days" class="board-form-control board-form-control--max-xs" value="{{ old('link_duration_days', optional($course->linkSetting)->link_duration_days ?? 30) }}" min="1">
                <span class="ml-2">일</span>
            </div>
        </div>
        <div id="bo-link-period-range-wrap" class="bo-edu-form-row @if($linkType !== 'range') bo-hidden @endif">
            <div class="board-form-group mb-0">
                <label class="board-form-label">시작일</label>
                <input type="date" name="link_period_start" class="board-form-control board-form-control--max-xs" value="{{ old('link_period_start', optional(optional($course->linkSetting)->link_period_start)->format('Y-m-d')) }}">
            </div>
            <div class="board-form-group mb-0">
                <label class="board-form-label">종료일</label>
                <input type="date" name="link_period_end" class="board-form-control board-form-control--max-xs" value="{{ old('link_period_end', optional(optional($course->linkSetting)->link_period_end)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>

<div class="modal js-member-modal bo-member-search-modal">
    <div class="modal-content bo-member-search-modal-content">
        <div class="modal-header">
            <h5 class="modal-title">회원 검색</h5>
            <button type="button" class="close js-close-member-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                            <th>No</th><th>이름</th><th>아이디</th><th>연락처</th><th>이메일</th><th>선택</th>
                        </tr>
                    </thead>
                    <tbody class="js-member-results">
                        <tr><td colspan="6" class="text-center">검색 버튼을 눌러 회원을 조회하세요.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 js-member-pagination"></div>
        </div>
    </div>
</div>
