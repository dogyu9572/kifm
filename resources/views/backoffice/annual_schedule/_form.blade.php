<div class="board-form-group">
    <label for="title" class="board-form-label">일정명 <span class="required">*</span></label>
    <input
        type="text"
        class="board-form-control"
        id="title"
        name="title"
        value="{{ old('title', $annualSchedule->title ?? '') }}"
        maxlength="200"
        required
    >
</div>

<div class="board-form-group">
    <label class="board-form-label">일정 기간 <span class="required">*</span></label>
    <div class="board-options-list board-options-horizontal">
        <input
            type="date"
            class="board-form-control"
            id="start_date"
            name="start_date"
            value="{{ old('start_date', optional($annualSchedule->start_date ?? null)->format('Y-m-d')) }}"
            required
        >
        <span id="schedule-date-separator">~</span>
        <input
            type="date"
            class="board-form-control"
            id="end_date"
            name="end_date"
            value="{{ old('end_date', optional($annualSchedule->end_date ?? null)->format('Y-m-d')) }}"
            required
        >
        <div class="board-option-item">
            <input
                type="checkbox"
                id="is_single_day"
                name="is_single_day"
                value="1"
                @checked((bool) old('is_single_day', $annualSchedule->is_single_day ?? false))
            >
            <label for="is_single_day">하루 일정</label>
        </div>
    </div>
</div>

<div class="board-form-group">
    <label for="content" class="board-form-label">일정 내용</label>
    <textarea
        class="board-form-control board-form-textarea"
        id="content"
        name="content"
        rows="8"
        maxlength="1000"
    >{{ old('content', $annualSchedule->content ?? '') }}</textarea>
    <small class="board-form-text"><span id="schedule-content-count">0</span> / 1000자</small>
</div>

<div class="board-form-group">
    <label class="board-form-label">노출 여부 <span class="required">*</span></label>
    <div class="board-options-list board-options-horizontal">
        <div class="board-option-item">
            <input
                type="radio"
                id="is_visible_1"
                name="is_visible"
                value="1"
                @checked((string) old('is_visible', (int) ($annualSchedule->is_visible ?? true)) === '1')
            >
            <label for="is_visible_1">노출</label>
        </div>
        <div class="board-option-item">
            <input
                type="radio"
                id="is_visible_0"
                name="is_visible"
                value="0"
                @checked((string) old('is_visible', (int) ($annualSchedule->is_visible ?? true)) === '0')
            >
            <label for="is_visible_0">미노출</label>
        </div>
    </div>
</div>
