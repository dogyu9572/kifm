/**
 * 결제 항목 목록: 전체 선택, 선택 삭제, 단건 삭제 확인, 페이지 크기 변경
 */
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('select-all');
    const rowChecks = document.querySelectorAll('.bo-plan-checkbox');
    const bulkBtn = document.getElementById('bulk-delete-btn');
    const deleteForms = document.querySelectorAll('#bo-payment-plans-index .js-delete-confirm-form');
    const perPageSelect = document.querySelector('.bo-per-page-select');
    const perPageForm = document.getElementById('payment-plan-per-page-form');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const updateBulkBtn = () => {
        if (!bulkBtn) {
            return;
        }
        const n = Array.from(rowChecks).filter((c) => c.checked).length;
        bulkBtn.disabled = n === 0;
        bulkBtn.innerHTML = `<i class="fas fa-trash"></i> 선택 삭제 (${n})`;
    };

    if (selectAll && rowChecks.length > 0) {
        selectAll.addEventListener('change', () => {
            rowChecks.forEach((c) => {
                c.checked = selectAll.checked;
            });
            updateBulkBtn();
        });

        rowChecks.forEach((c) => {
            c.addEventListener('change', () => {
                const checked = Array.from(rowChecks).filter((x) => x.checked).length;
                selectAll.checked = checked === rowChecks.length;
                updateBulkBtn();
            });
        });
        updateBulkBtn();
    } else if (bulkBtn) {
        bulkBtn.disabled = true;
    }

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const ok = window.confirm('정말 이 결제 항목을 삭제하시겠습니까?');
            if (!ok) {
                event.preventDefault();
            }
        });
    });

    if (bulkBtn && rowChecks.length > 0) {
        bulkBtn.addEventListener('click', async () => {
            const ids = Array.from(rowChecks)
                .filter((c) => c.checked)
                .map((c) => c.value);

            if (ids.length === 0) {
                window.alert('삭제할 항목을 선택해주세요.');
                return;
            }

            if (!window.confirm(`선택한 ${ids.length}건을 삭제하시겠습니까?`)) {
                return;
            }

            const formData = new FormData();
            ids.forEach((id) => formData.append('plan_ids[]', id));

            const root = document.getElementById('bo-payment-plans-index');
            const bulkUrl = root?.dataset?.bulkDestroyUrl;
            if (!bulkUrl) {
                window.alert('삭제 URL이 설정되지 않았습니다.');
                return;
            }

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
    }

    if (perPageSelect && perPageForm) {
        perPageSelect.addEventListener('change', () => {
            perPageForm.submit();
        });
    }
});
