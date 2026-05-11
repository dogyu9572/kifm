document.addEventListener('DOMContentLoaded', () => {
    const deleteForms = document.querySelectorAll('.js-delete-confirm-form');
    const selectAllCheckbox = document.getElementById('select-all');
    const scheduleCheckboxes = document.querySelectorAll('.schedule-checkbox');
    const bulkDeleteButton = document.getElementById('bulk-delete-btn');
    const singleDayCheckbox = document.getElementById('is_single_day');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const dateSeparator = document.getElementById('schedule-date-separator');
    const contentInput = document.getElementById('content');
    const contentCounter = document.getElementById('schedule-content-count');

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const confirmed = window.confirm('정말 이 일정을 삭제하시겠습니까?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    const updateBulkDeleteButton = () => {
        if (!bulkDeleteButton) {
            return;
        }

        const checkedCount = Array.from(scheduleCheckboxes).filter((checkbox) => checkbox.checked).length;
        bulkDeleteButton.disabled = checkedCount === 0;
        bulkDeleteButton.innerHTML = `<i class="fas fa-trash"></i> 선택 삭제 (${checkedCount})`;
    };

    if (selectAllCheckbox && scheduleCheckboxes.length > 0) {
        selectAllCheckbox.addEventListener('change', () => {
            scheduleCheckboxes.forEach((checkbox) => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButton();
        });

        scheduleCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const checkedCount = Array.from(scheduleCheckboxes).filter((item) => item.checked).length;
                selectAllCheckbox.checked = checkedCount === scheduleCheckboxes.length;
                updateBulkDeleteButton();
            });
        });

        updateBulkDeleteButton();
    } else if (bulkDeleteButton) {
        bulkDeleteButton.style.display = 'none';
    }

    if (bulkDeleteButton) {
        bulkDeleteButton.addEventListener('click', async () => {
            const selectedIds = Array.from(scheduleCheckboxes)
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value);

            if (selectedIds.length === 0) {
                window.alert('삭제할 일정을 선택해주세요.');
                return;
            }

            if (!window.confirm(`선택한 ${selectedIds.length}개의 일정을 삭제하시겠습니까?`)) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const formData = new FormData();
            selectedIds.forEach((id) => formData.append('schedule_ids[]', id));

            const response = await fetch('/backoffice/annual_schedule/bulk-destroy', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: formData,
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                window.alert(data.message ?? '선택 삭제 중 오류가 발생했습니다.');
                return;
            }

            window.alert(data.message ?? '선택한 일정이 삭제되었습니다.');
            window.location.reload();
        });
    }

    const updateSingleDayState = () => {
        if (!singleDayCheckbox || !startDateInput || !endDateInput || !dateSeparator) {
            return;
        }

        if (singleDayCheckbox.checked) {
            endDateInput.value = startDateInput.value;
            endDateInput.setAttribute('readonly', 'readonly');
            endDateInput.classList.add('board-field-disabled');
            dateSeparator.style.display = 'none';
            return;
        }

        endDateInput.removeAttribute('readonly');
        endDateInput.classList.remove('board-field-disabled');
        dateSeparator.style.display = '';
    };

    const updateContentCounter = () => {
        if (!contentInput || !contentCounter) {
            return;
        }
        contentCounter.textContent = String(contentInput.value.length);
    };

    if (singleDayCheckbox && startDateInput) {
        singleDayCheckbox.addEventListener('change', updateSingleDayState);
        startDateInput.addEventListener('change', () => {
            if (singleDayCheckbox.checked) {
                endDateInput.value = startDateInput.value;
            }
        });
        updateSingleDayState();
    }

    if (contentInput) {
        contentInput.addEventListener('input', updateContentCounter);
        updateContentCounter();
    }
});
