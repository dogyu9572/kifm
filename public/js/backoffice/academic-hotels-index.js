/**
 * 숙박 목록: 전체 선택, 선택 삭제, 단건 삭제, 페이지 크기
 */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('bo-academic-hotels-index');
    const selectAll = document.getElementById('select-all');
    const rowChecks = document.querySelectorAll('.bo-hotel-checkbox');
    const bulkBtn = document.getElementById('bulk-delete-btn');
    const deleteForms = document.querySelectorAll('#bo-academic-hotels-index .js-delete-confirm-form');
    const perPageSelect = document.querySelector('.bo-hotel-per-page');
    const perPageForm = document.getElementById('bo-hotel-per-page-form');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const updateBulkBtn = () => {
        if (!bulkBtn) {
            return;
        }
        const checkedCount = Array.from(rowChecks).filter((c) => c.checked).length;
        bulkBtn.disabled = checkedCount === 0;
        bulkBtn.innerHTML = `<i class="fas fa-trash"></i> 선택 삭제 (${checkedCount})`;
    };

    const syncSelectAllState = () => {
        if (!selectAll || rowChecks.length === 0) {
            return;
        }
        const checkedCount = Array.from(rowChecks).filter((c) => c.checked).length;
        selectAll.checked = checkedCount === rowChecks.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length;
    };

    if (selectAll && rowChecks.length > 0) {
        selectAll.addEventListener('change', () => {
            rowChecks.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
            selectAll.indeterminate = false;
            updateBulkBtn();
        });
        rowChecks.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                syncSelectAllState();
                updateBulkBtn();
            });
        });
        syncSelectAllState();
        updateBulkBtn();
    } else if (bulkBtn) {
        bulkBtn.disabled = true;
    }

    deleteForms.forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            const ok = window.confirm('정말 이 숙박 정보를 삭제하시겠습니까?');
            if (!ok) {
                event.preventDefault();
            }
        });
    });

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
        if (!bulkUrl || !csrfToken) {
            window.alert('삭제 요청 설정이 올바르지 않습니다.');
            return;
        }

        const formData = new FormData();
        selectedIds.forEach((id) => formData.append('hotel_ids[]', id));

        const response = await fetch(bulkUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: formData,
        });

        let data = {};
        try {
            data = await response.json();
        } catch {
            window.alert('응답 처리에 실패했습니다.');
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
