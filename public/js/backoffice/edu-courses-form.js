document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-edu-course-form');
    if (!form) {
        return;
    }

    const activeTabInput = document.getElementById('bo-edu-course-active-tab');
    const tabButtons = document.querySelectorAll('.js-course-tab-btn');
    const tabPanels = document.querySelectorAll('.js-course-tab-panel');
    const setActiveTab = (tabId) => {
        activeTabInput.value = tabId;
        tabButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.tab === tabId);
        });
        tabPanels.forEach((panel) => {
            panel.classList.toggle('bo-hidden', panel.dataset.tabPanel !== tabId);
        });
    };

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => setActiveTab(button.dataset.tab));
    });

    const courseTypeSelect = document.getElementById('course_type');
    const linkedEventWrap = document.getElementById('bo-course-linked-event-wrap');
    const syncCourseType = () => {
        const show = courseTypeSelect?.value === 'conference';
        linkedEventWrap?.classList.toggle('bo-hidden', !show);
    };
    courseTypeSelect?.addEventListener('change', syncCourseType);
    syncCourseType();

    const annualFeeRadios = document.querySelectorAll('.js-annual-fee-target');
    const freeRadios = document.querySelectorAll('.js-free-yn');
    const freeWrap = document.getElementById('bo-free-period-wrap');
    const gradePriceWrap = document.getElementById('bo-grade-price-wrap');
    const syncFreeWrap = () => {
        const value = form.querySelector('input[name="free_yn"]:checked')?.value;
        freeWrap?.classList.toggle('bo-hidden', value !== 'Y');
        gradePriceWrap?.classList.toggle('bo-hidden', value === 'Y');
    };
    const syncAnnualFeeTarget = () => {
        const annualFeeTarget = form.querySelector('input[name="annual_fee_target"]:checked')?.value || 'all';
        const freeYes = form.querySelector('input[name="free_yn"][value="Y"]');
        const freeNo = form.querySelector('input[name="free_yn"][value="N"]');
        if (annualFeeTarget === 'paid') {
            if (freeYes) {
                freeYes.checked = false;
                freeYes.disabled = true;
            }
            if (freeNo) {
                freeNo.checked = true;
            }
        } else if (freeYes) {
            freeYes.disabled = false;
        }
        syncFreeWrap();
    };
    annualFeeRadios.forEach((radio) => radio.addEventListener('change', syncAnnualFeeTarget));
    freeRadios.forEach((radio) => radio.addEventListener('change', syncFreeWrap));
    syncAnnualFeeTarget();

    const periodRadios = document.querySelectorAll('.js-period-type');
    const periodDaysWrap = document.getElementById('bo-period-days-wrap');
    const periodRangeWrap = document.getElementById('bo-period-range-wrap');
    const syncPeriodWrap = () => {
        const value = form.querySelector('input[name="period_type"]:checked')?.value || 'days';
        periodDaysWrap?.classList.toggle('bo-hidden', value !== 'days');
        periodRangeWrap?.classList.toggle('bo-hidden', value !== 'range');
    };
    periodRadios.forEach((radio) => radio.addEventListener('change', syncPeriodWrap));
    syncPeriodWrap();

    const examRadios = document.querySelectorAll('.js-exam-yn');
    const examWrap = document.getElementById('bo-exam-questions-wrap');
    const syncExamWrap = () => {
        const value = form.querySelector('input[name="exam_yn"]:checked')?.value || 'N';
        examWrap?.classList.toggle('bo-hidden', value !== 'Y');
    };
    examRadios.forEach((radio) => radio.addEventListener('change', syncExamWrap));
    syncExamWrap();

    const linkEventSelect = document.querySelector('.js-link-event');
    const linkTrainingSelect = document.querySelector('.js-link-training');
    const linkRoundWrap = document.getElementById('bo-link-round-wrap');
    const linkFreeWrap = document.getElementById('bo-link-free-wrap');
    const syncLinkBlocks = () => {
        const hasEvent = (linkEventSelect?.value || '') !== '';
        const hasTraining = (linkTrainingSelect?.value || '') !== '';
        linkRoundWrap?.classList.toggle('bo-hidden', !hasTraining);
        linkFreeWrap?.classList.toggle('bo-hidden', !(hasEvent || hasTraining));
    };
    const syncLinkTraining = () => {
        syncLinkBlocks();
    };
    linkEventSelect?.addEventListener('change', syncLinkBlocks);
    linkTrainingSelect?.addEventListener('change', syncLinkTraining);
    syncLinkBlocks();

    const linkRoundUseRadios = document.querySelectorAll('.js-link-round-use');
    const linkRoundChoices = document.getElementById('bo-link-round-choices');
    const syncLinkRoundChoices = () => {
        const value = form.querySelector('input[name="link_round_use"]:checked')?.value || 'N';
        linkRoundChoices?.classList.toggle('bo-hidden', value !== 'Y');
    };
    linkRoundUseRadios.forEach((radio) => radio.addEventListener('change', syncLinkRoundChoices));
    syncLinkRoundChoices();

    const linkPeriodRadios = document.querySelectorAll('.js-link-period-type');
    const linkDaysWrap = document.getElementById('bo-link-period-days-wrap');
    const linkRangeWrap = document.getElementById('bo-link-period-range-wrap');
    const syncLinkPeriod = () => {
        const value = form.querySelector('input[name="link_period_type"]:checked')?.value || 'days';
        linkDaysWrap?.classList.toggle('bo-hidden', value !== 'days');
        linkRangeWrap?.classList.toggle('bo-hidden', value !== 'range');
    };
    linkPeriodRadios.forEach((radio) => radio.addEventListener('change', syncLinkPeriod));
    syncLinkPeriod();

    const syncGradePriceRow = (checkbox) => {
        const row = checkbox.closest('.bo-grade-row');
        const wrap = row?.querySelector('.js-grade-price-wrap');
        const price = row?.querySelector('.js-grade-price');
        if (!wrap || !price) {
            return;
        }
        if (checkbox.checked) {
            wrap.classList.remove('bo-hidden');
        } else {
            wrap.classList.add('bo-hidden');
            price.value = '';
        }
    };
    form.querySelectorAll('.js-grade-eligible').forEach((checkbox) => {
        checkbox.addEventListener('change', () => syncGradePriceRow(checkbox));
        syncGradePriceRow(checkbox);
    });

    const examList = document.getElementById('bo-exam-question-list');
    const addExamBtn = document.getElementById('bo-add-exam-question');
    const reindexExamCards = () => {
        const cards = examList?.querySelectorAll('.js-exam-question-card') || [];
        cards.forEach((card, cardIndex) => {
            card.dataset.index = String(cardIndex);
            const orderLabel = card.querySelector('.js-question-order-label');
            if (orderLabel) {
                orderLabel.textContent = String(cardIndex + 1);
            }
            card.querySelectorAll('[name^="exam_questions["]').forEach((field) => {
                field.name = field.name.replace(/exam_questions\[\d+\]/, `exam_questions[${cardIndex}]`);
            });
            reindexChoiceRows(card);
        });
    };

    const reindexChoiceRows = (card) => {
        const cardIndex = Number(card.dataset.index || '0');
        const rows = card.querySelectorAll('.js-choice-row');
        rows.forEach((row, choiceIndex) => {
            row.dataset.choiceIndex = String(choiceIndex);
            const radio = row.querySelector('.js-answer-radio');
            const input = row.querySelector('input[type="text"]');
            if (radio) {
                radio.name = `exam_questions[${cardIndex}][answer_index]`;
                radio.value = String(choiceIndex);
            }
            if (input) {
                input.name = `exam_questions[${cardIndex}][choices][${choiceIndex}]`;
                input.placeholder = `보기 ${choiceIndex + 1}`;
            }
        });
    };

    const createChoiceRow = (cardIndex, choiceIndex, value = '', checked = false) => `
        <div class="d-flex align-items-center gap-2 mb-2 js-choice-row" data-choice-index="${choiceIndex}">
            <label class="board-radio-item mb-0">
                <input type="radio" class="js-answer-radio"
                    name="exam_questions[${cardIndex}][answer_index]"
                    value="${choiceIndex}" ${checked ? 'checked' : ''}>
            </label>
            <input type="text"
                class="board-form-control"
                name="exam_questions[${cardIndex}][choices][${choiceIndex}]"
                value="${escapeAttr(value)}"
                placeholder="보기 ${choiceIndex + 1}">
            <button type="button" class="btn btn-outline-danger btn-sm js-remove-exam-choice">삭제</button>
        </div>
    `;

    const renderExamCard = (index) => {
        const el = document.createElement('div');
        el.className = 'board-card mb-2 js-exam-question-card';
        el.dataset.index = String(index);
        el.innerHTML = `
            <div class="board-card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-0">문제 <span class="js-question-order-label">${index + 1}</span></h5>
                    <div class="board-btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm js-move-exam-up" aria-label="위로 이동" title="위로 이동"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="btn btn-outline-secondary btn-sm js-move-exam-down" aria-label="아래로 이동" title="아래로 이동"><i class="fas fa-chevron-down"></i></button>
                        <button type="button" class="btn btn-outline-danger btn-sm js-remove-exam-question">삭제</button>
                    </div>
                </div>
                <div class="board-form-group">
                    <label class="board-form-label">문제</label>
                    <textarea class="board-form-control" rows="2" name="exam_questions[${index}][question]"></textarea>
                </div>
                <div class="board-form-group mb-0">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="board-form-label mb-0">보기 (라디오 선택 = 정답)</label>
                        <button type="button" class="btn btn-outline-primary btn-sm js-add-exam-choice">+ 보기 추가</button>
                    </div>
                    <div class="js-choice-list">
                        ${createChoiceRow(index, 0, '', true)}
                        ${createChoiceRow(index, 1)}
                        ${createChoiceRow(index, 2)}
                        ${createChoiceRow(index, 3)}
                    </div>
                </div>
            </div>
        `;
        return el;
    };

    const nextExamIndex = () => {
        const cards = examList?.querySelectorAll('.js-exam-question-card') || [];
        return cards.length;
    };

    addExamBtn?.addEventListener('click', () => {
        if (!examList) {
            return;
        }
        examList.appendChild(renderExamCard(nextExamIndex()));
        reindexExamCards();
    });

    examList?.addEventListener('click', (event) => {
        const moveUpButton = event.target.closest('.js-move-exam-up');
        if (moveUpButton) {
            const card = moveUpButton.closest('.js-exam-question-card');
            const prev = card?.previousElementSibling;
            if (card && prev) {
                examList.insertBefore(card, prev);
                reindexExamCards();
            }
            return;
        }

        const moveDownButton = event.target.closest('.js-move-exam-down');
        if (moveDownButton) {
            const card = moveDownButton.closest('.js-exam-question-card');
            const next = card?.nextElementSibling;
            if (card && next) {
                examList.insertBefore(next, card);
                reindexExamCards();
            }
            return;
        }

        const addChoiceButton = event.target.closest('.js-add-exam-choice');
        if (addChoiceButton) {
            const card = addChoiceButton.closest('.js-exam-question-card');
            const choiceList = card?.querySelector('.js-choice-list');
            if (!card || !choiceList) {
                return;
            }
            const cardIndex = Number(card.dataset.index || '0');
            const nextIndex = choiceList.querySelectorAll('.js-choice-row').length;
            choiceList.insertAdjacentHTML('beforeend', createChoiceRow(cardIndex, nextIndex));
            return;
        }

        const removeChoiceButton = event.target.closest('.js-remove-exam-choice');
        if (removeChoiceButton) {
            const card = removeChoiceButton.closest('.js-exam-question-card');
            const choiceList = card?.querySelector('.js-choice-list');
            const row = removeChoiceButton.closest('.js-choice-row');
            if (!card || !choiceList || !row) {
                return;
            }
            row.remove();
            const rows = choiceList.querySelectorAll('.js-choice-row');
            if (rows.length === 0) {
                const cardIndex = Number(card.dataset.index || '0');
                choiceList.insertAdjacentHTML('beforeend', createChoiceRow(cardIndex, 0, '', true));
            }
            reindexChoiceRows(card);
            if (!choiceList.querySelector('.js-answer-radio:checked')) {
                const first = choiceList.querySelector('.js-answer-radio');
                if (first) {
                    first.checked = true;
                }
            }
            return;
        }

        const button = event.target.closest('.js-remove-exam-question');
        if (!button) {
            return;
        }
        const card = button.closest('.js-exam-question-card');
        card?.remove();
        reindexExamCards();
    });

    const memberIdInput = document.getElementById('professor_member_id');
    const memberDisplayInput = document.getElementById('professor_member_display');
    const professorNameInput = document.getElementById('professor_name');
    const professorOrgInput = document.getElementById('professor_org');
    const professorSelectorRoot = document.querySelector('.js-professor-selector');
    professorSelectorRoot?.addEventListener('bo-member-selected', (event) => {
        const detail = event.detail || {};
        const selectedName = detail.name || detail.label || '';
        const selectedOrg = detail.organization || '';
        if (memberIdInput) {
            memberIdInput.value = detail.id || '';
        }
        if (memberDisplayInput) {
            memberDisplayInput.value = selectedName
                ? `${selectedName} (${selectedOrg || '-'})`
                : '';
        }
        if (professorNameInput) {
            professorNameInput.value = selectedName;
        }
        if (professorOrgInput) {
            professorOrgInput.value = selectedOrg;
        }
    });

    const formatFileSize = (bytes) => `(${(bytes / 1024 / 1024).toFixed(2)}MB)`;

    const bindSingleFileInput = ({ inputId, previewId, existingKey, acceptText }) => {
        const fileInput = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const removeExistingButton = document.querySelector(`[data-remove-existing-target="${existingKey}"]`);
        const removeExistingCheckbox = document.getElementById(`delete_${existingKey === 'lecture' ? 'lecture_file' : 'thumbnail'}`);
        const existingItem = document.getElementById(`bo-${existingKey}-existing-item`);
        const maxFileSize = Number(fileInput?.dataset.maxFileSizeMb || 20) * 1024 * 1024;
        if (!fileInput || !preview) {
            return;
        }

        const renderPreview = (file) => {
            if (!file) {
                preview.innerHTML = '';
                return;
            }
            preview.innerHTML = `
                <div class="board-file-item">
                    <div class="board-file-info">
                        <i class="fas fa-file"></i>
                        <span class="board-file-name">${escapeHtml(file.name)}</span>
                        <span class="board-file-size">${formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="board-file-remove" data-remove-selected-file="${inputId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        };

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) {
                renderPreview(null);
                return;
            }
            if (file.size > maxFileSize) {
                window.alert(`${acceptText} 파일은 20MB 이하만 업로드할 수 있습니다.`);
                fileInput.value = '';
                renderPreview(null);
                return;
            }
            renderPreview(file);
            if (removeExistingCheckbox) {
                removeExistingCheckbox.checked = true;
            }
            if (existingItem) {
                existingItem.classList.add('bo-hidden');
            }
        });

        preview.addEventListener('click', (event) => {
            const removeBtn = event.target.closest(`[data-remove-selected-file="${inputId}"]`);
            if (!removeBtn) {
                return;
            }
            fileInput.value = '';
            renderPreview(null);
            if (removeExistingCheckbox) {
                removeExistingCheckbox.checked = false;
            }
            if (existingItem) {
                existingItem.classList.remove('bo-hidden');
            }
        });

        removeExistingButton?.addEventListener('click', () => {
            if (removeExistingCheckbox) {
                removeExistingCheckbox.checked = true;
            }
            if (existingItem) {
                existingItem.classList.add('bo-hidden');
            }
        });
    };

    if (typeof window.initBoardImageFilePreview === 'function') {
        window.initBoardImageFilePreview({
            inputId: 'thumbnail_file',
            previewId: 'thumbnailFilePreview',
            removeExistingSelector: '[data-remove-existing-target="thumbnail"]',
            deleteCheckboxId: 'delete_thumbnail',
            existingItemId: 'bo-thumbnail-existing-item',
        });
    }

    bindSingleFileInput({
        inputId: 'lecture_file',
        previewId: 'lectureFilePreview',
        existingKey: 'lecture',
        acceptText: '강의록',
    });

    form.addEventListener('submit', () => {
        reindexExamCards();
    });

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const escapeAttr = (value) => escapeHtml(value).replaceAll('`', '&#096;');
});
