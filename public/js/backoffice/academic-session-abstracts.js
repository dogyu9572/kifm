(function () {
    'use strict';

    const tbody = document.querySelector('#bo-session-items-tbody');
    const abstractTemplate = document.querySelector('#bo-session-abstract-row-template');
    const breakTemplate = document.querySelector('#bo-session-break-row-template');
    const abstractModal = document.querySelector('#bo-abstract-modal');
    const checkAll = document.querySelector('#bo-abstract-modal-check-all');
    const keywordInput = document.querySelector('#bo-abstract-modal-keyword');

    if (! tbody || ! abstractTemplate || ! breakTemplate) {
        return;
    }

    function rowFromTemplate(template) {
        return template.content.firstElementChild.cloneNode(true);
    }

    function reindexRows() {
        tbody.querySelectorAll('[data-session-item-row]').forEach(function (row, index) {
            row.querySelectorAll('[data-item-field]').forEach(function (field) {
                field.name = 'items[' + index + '][' + field.dataset.itemField + ']';
            });
        });
    }

    function sortRowsByTime() {
        const rows = Array.from(tbody.querySelectorAll('[data-session-item-row]'));
        rows.sort(function (a, b) {
            const aTime = a.querySelector('[data-item-field="start_time"]').value || '99:99';
            const bTime = b.querySelector('[data-item-field="start_time"]').value || '99:99';

            return aTime.localeCompare(bTime);
        });
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
        reindexRows();
    }

    function addRow(type, data) {
        const row = rowFromTemplate(type === 'break' ? breakTemplate : abstractTemplate);
        const values = data || {};

        Object.keys(values).forEach(function (key) {
            const field = row.querySelector('[data-item-field="' + key + '"]');
            if (field) {
                field.value = values[key] || '';
            }
        });

        tbody.appendChild(row);
        sortRowsByTime();
    }

    function openModal() {
        if (! abstractModal) {
            return;
        }
        abstractModal.style.display = 'block';
        if (keywordInput) {
            keywordInput.focus();
        }
    }

    function closeModal() {
        if (! abstractModal) {
            return;
        }
        abstractModal.style.display = 'none';
    }

    function filterModalRows() {
        const keyword = (keywordInput ? keywordInput.value : '').trim().toLowerCase();

        document.querySelectorAll('[data-abstract-modal-row]').forEach(function (row) {
            const target = (row.dataset.keyword || '').toLowerCase();
            row.classList.toggle('bo-abstract-modal-row-hidden', keyword !== '' && ! target.includes(keyword));
        });
    }

    function resetModalChecks() {
        document.querySelectorAll('.bo-abstract-modal-check').forEach(function (checkbox) {
            checkbox.checked = false;
        });
        if (checkAll) {
            checkAll.checked = false;
        }
    }

    function addSelectedAbstracts() {
        document.querySelectorAll('.bo-abstract-modal-check:checked').forEach(function (checkbox) {
            addRow('abstract', {
                academic_event_abstract_id: checkbox.value,
                start_time: '10:00',
                end_time: '10:20',
                title: checkbox.dataset.title || '',
                presenter: checkbox.dataset.presenter || ''
            });
        });

        resetModalChecks();
        closeModal();
    }

    document.addEventListener('click', function (event) {
        const actionTarget = event.target.closest('[data-session-item-action]');
        if (! actionTarget) {
            return;
        }

        const action = actionTarget.dataset.sessionItemAction;
        if (action === 'add-abstract') {
            addRow('abstract');
        } else if (action === 'add-break') {
            addRow('break', {
                title: 'Coffee Break'
            });
        } else if (action === 'remove') {
            actionTarget.closest('[data-session-item-row]').remove();
            reindexRows();
        } else if (action === 'open-abstract-modal') {
            openModal();
        } else if (action === 'close-abstract-modal') {
            closeModal();
        } else if (action === 'filter-abstract-modal') {
            filterModalRows();
        } else if (action === 'add-selected-abstracts') {
            addSelectedAbstracts();
        }
    });

    document.addEventListener('change', function (event) {
        if (event.target.matches('[data-item-field="start_time"]')) {
            sortRowsByTime();
        }
        if (event.target === checkAll) {
            document.querySelectorAll('.bo-abstract-modal-check').forEach(function (checkbox) {
                const row = checkbox.closest('[data-abstract-modal-row]');
                if (! row || ! row.classList.contains('bo-abstract-modal-row-hidden')) {
                    checkbox.checked = checkAll.checked;
                }
            });
        }
    });

    if (keywordInput) {
        keywordInput.addEventListener('input', filterModalRows);
    }

    reindexRows();
})();
