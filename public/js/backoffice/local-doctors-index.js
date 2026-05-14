/**
 * 주치의 목록: 시·도→시군구, 전체 선택, 선택 삭제, 단건 삭제 확인, 표시 개수
 */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('bo-local-doctors-index');
    const perPageForm = document.getElementById('bo-local-doctors-per-page-form');
    const perPageSelect = document.getElementById('per_page');
    const selectAll = document.getElementById('select-all');
    const rowChecks = document.querySelectorAll('.bo-local-doctor-checkbox');
    const bulkBtn = document.getElementById('bulk-delete-btn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (perPageForm && perPageSelect) {
        perPageSelect.addEventListener('change', () => {
            perPageForm.submit();
        });
    }

    const sidoSelect = document.getElementById('filter_sido');
    const sigunguSelect = document.getElementById('filter_sigungu');
    let sigunguMap = {};
    const sigunguEmbed = document.getElementById('bo-local-doctors-sigungu-json');
    if (sigunguEmbed) {
        try {
            const raw = (sigunguEmbed.value || '').trim() || '{}';
            const parsed = JSON.parse(raw);
            sigunguMap = parsed !== null && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
        } catch {
            sigunguMap = {};
        }
    }
    if (sidoSelect && sigunguSelect) {
        const rebuildSigungu = () => {
            const sido = (sidoSelect.value || '').trim();
            let rawList = sido ? sigunguMap[sido] : null;
            if (sido && !Array.isArray(rawList)) {
                const hit = Object.keys(sigunguMap).find((k) => (k || '').trim() === sido);
                rawList = hit ? sigunguMap[hit] : null;
            }
            const list = Array.isArray(rawList) ? rawList : [];
            const current =
                new URLSearchParams(window.location.search).get('sigungu') ||
                sigunguSelect.value ||
                '';
            sigunguSelect.innerHTML = '';
            const opt0 = document.createElement('option');
            opt0.value = '';
            opt0.textContent = '전체 (시/군/구)';
            sigunguSelect.appendChild(opt0);
            list.forEach((name) => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                if (current === name) {
                    opt.selected = true;
                }
                sigunguSelect.appendChild(opt);
            });
            if (current && !list.includes(current)) {
                const optExtra = document.createElement('option');
                optExtra.value = current;
                optExtra.textContent = current;
                optExtra.selected = true;
                sigunguSelect.appendChild(optExtra);
            }
        };
        sidoSelect.addEventListener('change', () => {
            rebuildSigungu();
        });
        rebuildSigungu();
    }

    const updateBulkBtn = () => {
        if (!bulkBtn) {
            return;
        }
        const checkedCount = Array.from(rowChecks).filter((c) => c.checked).length;
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
                const checkedCount = Array.from(rowChecks).filter((c) => c.checked).length;
                selectAll.checked = checkedCount === rowChecks.length;
                updateBulkBtn();
            });
        });
        updateBulkBtn();
    } else if (bulkBtn) {
        bulkBtn.disabled = true;
    }

    document.querySelectorAll('#bo-local-doctors-index .js-delete-confirm-form').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            const ok = window.confirm('이 주치의 정보를 삭제하시겠습니까?');
            if (!ok) {
                event.preventDefault();
            }
        });
    });

    if (bulkBtn && rowChecks.length > 0) {
        bulkBtn.addEventListener('click', async () => {
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
            selectedIds.forEach((id) => formData.append('local_doctor_ids[]', id));

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
});
