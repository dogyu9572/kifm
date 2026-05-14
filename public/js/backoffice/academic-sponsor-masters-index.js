/**
 * 스폰서 마스터 목록: 전체 선택, 선택 삭제, 단건 삭제 확인, 페이지 크기, 드래그 정렬
 */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('bo-academic-sponsor-masters-index');
    const selectAll = document.getElementById('select-all');
    const rowChecks = document.querySelectorAll('.bo-sponsor-master-checkbox');
    const bulkBtn = document.getElementById('bulk-delete-btn');
    const deleteForms = document.querySelectorAll('#bo-academic-sponsor-masters-index .js-delete-confirm-form');
    const perPageSelect = document.querySelector('.bo-sponsor-master-per-page');
    const perPageForm = document.getElementById('bo-sponsor-master-per-page-form');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const sortableTbody = document.getElementById('sortable-tbody');

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
            const ok = window.confirm('정말 이 스폰서를 삭제하시겠습니까?');
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
        selectedIds.forEach((id) => formData.append('sponsor_ids[]', id));

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

    const updateSortOrder = async () => {
        if (!sortableTbody || !root?.dataset?.sortOrderUrl) {
            return;
        }

        const rows = Array.from(sortableTbody.querySelectorAll('tr[data-post-id]'));
        if (rows.length === 0) {
            return;
        }
        const updates = rows.map((row, index) => ({
            post_id: Number(row.dataset.postId),
            sort_order: index + 1,
        }));

        const response = await fetch(root.dataset.sortOrderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                Accept: 'application/json',
            },
            body: JSON.stringify({ updates }),
        });

        let data = {};
        try {
            data = await response.json();
        } catch {
            window.alert('응답 처리에 실패했습니다.');
            return;
        }

        if (!response.ok || !data.success) {
            window.alert(data.message ?? '정렬 순서 저장 중 오류가 발생했습니다.');
            return;
        }
    };

    if (sortableTbody && typeof Sortable !== 'undefined') {
        const rows = sortableTbody.querySelectorAll('tr[data-post-id]');
        if (rows.length === 0) {
            return;
        }
        new Sortable(sortableTbody, {
            handle: '.sort-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: async (event) => {
                event.item.classList.remove('dragging');
                await updateSortOrder();
            },
            onStart: (event) => {
                event.item.classList.add('dragging');
            },
        });
    }
});
