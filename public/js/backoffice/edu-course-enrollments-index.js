document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('bo-edu-course-enrollments-index');
    const selectAll = document.getElementById('select-all');
    const rowChecks = document.querySelectorAll('.bo-edu-course-enrollment-checkbox');
    const bulkBtn = document.getElementById('bulk-delete-btn');
    const perPageSelect = document.querySelector('.bo-edu-course-enrollment-per-page');
    const perPageForm = document.getElementById('edu-course-enrollment-per-page-form');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const updateBulkBtn = () => {
        if (!bulkBtn) {
            return;
        }
        const checkedCount = Array.from(rowChecks).filter((checkbox) => checkbox.checked).length;
        bulkBtn.disabled = checkedCount === 0;
        bulkBtn.innerHTML = `<i class="fas fa-trash"></i> 선택 삭제 (${checkedCount})`;
    };

    if (selectAll && rowChecks.length > 0) {
        selectAll.addEventListener('change', () => {
            rowChecks.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkBtn();
        });

        rowChecks.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const checkedCount = Array.from(rowChecks).filter((item) => item.checked).length;
                selectAll.checked = checkedCount === rowChecks.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length;
                updateBulkBtn();
            });
        });
    }

    updateBulkBtn();

    bulkBtn?.addEventListener('click', async () => {
        const selectedIds = Array.from(rowChecks)
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.value);

        if (selectedIds.length === 0) {
            window.alert('삭제할 항목을 선택해주세요.');
            return;
        }

        if (!window.confirm(`선택한 ${selectedIds.length}건을 삭제하시겠습니까?`)) {
            return;
        }

        const bulkUrl = root?.dataset?.bulkDestroyUrl;
        if (!bulkUrl) {
            window.alert('삭제 URL이 설정되지 않았습니다.');
            return;
        }

        const formData = new FormData();
        selectedIds.forEach((id) => formData.append('enrollment_ids[]', id));

        const response = await fetch(bulkUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken ?? '',
                Accept: 'application/json',
            },
            body: formData,
        });

        let data = {};
        try {
            data = await response.json();
        } catch {
            window.alert('삭제 응답을 처리할 수 없습니다.');
            return;
        }

        if (!response.ok || !data.success) {
            window.alert(data.message ?? '선택 삭제 중 오류가 발생했습니다.');
            return;
        }

        window.alert(data.message ?? '삭제되었습니다.');
        window.location.reload();
    });

    perPageSelect?.addEventListener('change', () => {
        perPageForm?.submit();
    });
});

