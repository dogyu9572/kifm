(function () {
    const form = document.getElementById('edu-training-payment-form');
    if (!form) {
        return;
    }

    const cardGuideEl = document.getElementById('bo-edu-payment-card-guide');
    const bankBlockEl = document.getElementById('bo-edu-payment-bank-block');
    const receiptDetailEl = document.getElementById('bo-edu-receipt-detail');

    const syncPaymentMethodPanels = () => {
        const checked = form.querySelector('input[name="payment_method"]:checked');
        const method = checked instanceof HTMLInputElement ? checked.value : 'card';
        const showBank = method === 'bank_transfer';
        if (cardGuideEl) {
            cardGuideEl.classList.toggle('bo-hidden', showBank);
        }
        if (bankBlockEl) {
            bankBlockEl.classList.toggle('bo-hidden', !showBank);
        }
    };

    const syncReceiptDetailPanel = () => {
        const checked = form.querySelector('input[name="receipt_issue"]:checked');
        const issueYes = checked instanceof HTMLInputElement && checked.value === 'YES';
        if (receiptDetailEl) {
            receiptDetailEl.classList.toggle('bo-hidden', !issueYes);
        }
    };

    form.addEventListener('change', (event) => {
        const target = event.target;
        if (target instanceof HTMLInputElement && target.name === 'payment_method') {
            syncPaymentMethodPanels();
        }
        if (target instanceof HTMLInputElement && target.name === 'receipt_issue') {
            syncReceiptDetailPanel();
        }
    });

    syncPaymentMethodPanels();
    syncReceiptDetailPanel();

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const planModal = form.querySelector('.js-plan-modal');
    const planKeywordInput = form.querySelector('.js-plan-keyword');
    const planSearchField = form.querySelector('.js-plan-search-field');
    const planSearchResults = form.querySelector('.js-plan-results');
    const planPaginationRoot = form.querySelector('.js-plan-pagination');
    const planCloseButton = form.querySelector('.js-close-plan-modal');
    const planSearchButton = form.querySelector('.js-search-plan');

    const nameInput = document.getElementById('name');
    const licenseInput = document.getElementById('license_no');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');

    const payloadInput = document.getElementById('payment_items_payload');
    const selectedItemsTableBody = document.querySelector('#selected-items-table tbody');
    const totalAmountDisplay = document.getElementById('total-amount-display');
    const selectedItemsTotal = document.getElementById('selected-items-total');

    let currentPlanPage = 1;

    const selectedItems = parsePayload(payloadInput?.value);
    renderSelectedItems();

    const memberSelectorRoot = form.querySelector('.js-member-selector');
    memberSelectorRoot?.addEventListener('bo-member-selected', (event) => {
        const detail = event.detail;
        if (!detail) {
            return;
        }
        if (nameInput) {
            nameInput.value = detail.label || '';
        }
        if (licenseInput) {
            licenseInput.value = detail.license_number || '';
        }
        if (phoneInput) {
            phoneInput.value = detail.phone_number || '';
        }
        if (emailInput) {
            emailInput.value = detail.email || '';
        }
    });

    const resetPlanSearch = () => {
        if (planSearchField) {
            planSearchField.selectedIndex = 0;
        }
        if (planKeywordInput) {
            planKeywordInput.value = '';
        }
    };

    const closePlanModal = () => {
        if (planModal) {
            planModal.style.display = 'none';
        }
    };

    const openPlanModal = () => {
        if (planModal) {
            planModal.style.display = 'block';
        }
        resetPlanSearch();
        if (planKeywordInput) {
            planKeywordInput.focus();
        }
        fetchPlans(1);
    };

    document.getElementById('open-plan-modal')?.addEventListener('click', openPlanModal);
    planCloseButton?.addEventListener('click', closePlanModal);
    planModal?.addEventListener('click', (event) => {
        if (event.target === planModal) {
            closePlanModal();
        }
    });

    planSearchButton?.addEventListener('click', () => fetchPlans(1));
    planKeywordInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            fetchPlans(1);
        }
    });

    planSearchResults?.addEventListener('click', (event) => {
        const button = event.target.closest('.js-select-plan');
        if (!button) {
            return;
        }

        selectedItems.push({
            payment_plan_id: Number(button.dataset.planId),
            item_name: button.dataset.planName || '',
            category: button.dataset.planCategory || '',
            member_scope: button.dataset.planScope || '',
            price: Number(button.dataset.planPrice || 0),
        });
        renderSelectedItems();
        closePlanModal();
    });

    planPaginationRoot?.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('js-plan-page')) {
            return;
        }
        event.preventDefault();
        if (target.closest('.disabled')) {
            return;
        }

        const page = Number(target.dataset.page ?? '1');
        if (Number.isNaN(page) || page < 1 || page === currentPlanPage) {
            return;
        }

        fetchPlans(page);
    });

    document.getElementById('confirm-deposit-btn')?.addEventListener('click', async () => {
        const url = form.dataset.confirmDepositUrl;
        const depositor = document.getElementById('bank_depositor')?.value || '';
        const depositDate = document.getElementById('bank_deposit_date')?.value || '';
        if (!url || !depositor || !depositDate) {
            alert('입금자명과 입금일을 입력해주세요.');
            return;
        }
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    bank_depositor: depositor,
                    bank_deposit_date: depositDate,
                }),
            });
            if (!response.ok) {
                throw new Error('failed');
            }
            alert('입금 확인이 완료되었습니다.');
        } catch (error) {
            alert('입금 확인 처리에 실패했습니다.');
        }
    });

    async function fetchPlans(page) {
        const baseUrl = form.dataset.searchPlansUrl;
        if (!baseUrl || !planSearchResults) {
            return;
        }

        currentPlanPage = page;
        planSearchResults.innerHTML = '<tr><td colspan="6" class="text-center">조회 중입니다...</td></tr>';
        if (planPaginationRoot) {
            planPaginationRoot.innerHTML = '';
        }

        const params = new URLSearchParams();
        params.set('page', String(page));
        params.set('keyword', planKeywordInput?.value.trim() || '');

        try {
            const response = await fetch(`${baseUrl}?${params.toString()}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) {
                throw new Error('failed');
            }
            const data = await response.json();
            renderPlanRows(data.data || [], data.meta ?? null);
            renderPlanPagination(data.meta ?? null);
        } catch (error) {
            planSearchResults.innerHTML = '<tr><td colspan="6" class="text-center">조회 중 오류가 발생했습니다.</td></tr>';
            if (planPaginationRoot) {
                planPaginationRoot.innerHTML = '';
            }
        }
    }

    function renderPlanRows(rows, meta) {
        if (!planSearchResults) {
            return;
        }
        if (rows.length === 0) {
            planSearchResults.innerHTML = '<tr><td colspan="6" class="text-center">검색 결과가 없습니다.</td></tr>';
            return;
        }

        const startNo = ((Number(meta?.current_page ?? 1) - 1) * Number(meta?.per_page ?? 10)) + 1;
        planSearchResults.innerHTML = rows.map((plan, idx) => {
            const indexNo = startNo + idx;
            return `
                <tr>
                    <td>${indexNo}</td>
                    <td>${escapeHtml(plan.category_label || '-')}</td>
                    <td>${escapeHtml(plan.name || '-')}</td>
                    <td>${escapeHtml(plan.member_scope_label || '-')}</td>
                    <td>${escapeHtml(plan.price_display || '-')}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary js-select-plan"
                            data-plan-id="${plan.id}"
                            data-plan-name="${escapeAttr(plan.name || '')}"
                            data-plan-category="${escapeAttr(plan.category || '')}"
                            data-plan-scope="${escapeAttr(plan.member_scope || '')}"
                            data-plan-price="${Number(plan.price || 0)}"
                        >선택</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    /**
     * 조회 성공 시 항상 페이지네이션 UI를 표시한다(1페이지만 있어도 이전/다음·번호 영역 노출).
     */
    function renderPlanPagination(meta) {
        if (!planPaginationRoot) {
            return;
        }

        if (!meta) {
            planPaginationRoot.innerHTML = '';
            return;
        }

        const current = Number(meta.current_page ?? 1);
        const last = Math.max(1, Number(meta.last_page ?? 1));
        const start = Math.max(1, current - 2);
        const end = Math.min(last, start + 4);

        const numberButtons = [];
        for (let p = start; p <= end; p += 1) {
            const activeClass = p === current ? 'active' : '';
            numberButtons.push(`<li class="page-item ${activeClass}"><a class="page-link js-plan-page" data-page="${p}" href="#">${p}</a></li>`);
        }

        planPaginationRoot.innerHTML = `
            <ul class="pagination">
                <li class="page-item ${current <= 1 ? 'disabled' : ''}">
                    <a class="page-link js-plan-page" data-page="${Math.max(1, current - 1)}" href="#" aria-label="이전 페이지">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                ${numberButtons.join('')}
                <li class="page-item ${current >= last ? 'disabled' : ''}">
                    <a class="page-link js-plan-page" data-page="${Math.min(last, current + 1)}" href="#" aria-label="다음 페이지">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        `;
    }

    function renderSelectedItems() {
        if (!selectedItemsTableBody || !payloadInput) {
            return;
        }

        if (selectedItems.length === 0) {
            selectedItemsTableBody.innerHTML = '<tr><td colspan="3" class="text-center">선택된 결제 항목이 없습니다.</td></tr>';
        } else {
            selectedItemsTableBody.innerHTML = selectedItems.map((item) => {
                return `
                    <tr>
                        <td>${escapeHtml(item.item_name || '-')}</td>
                        <td>${escapeHtml(memberScopeLabel(item.member_scope))}</td>
                        <td>${Number(item.price || 0).toLocaleString()} 원</td>
                    </tr>
                `;
            }).join('');
        }

        payloadInput.value = JSON.stringify(selectedItems);
        const sum = selectedItems.reduce((acc, item) => acc + Number(item.price || 0), 0);
        if (totalAmountDisplay) {
            totalAmountDisplay.value = `${sum.toLocaleString()} 원`;
        }
        if (selectedItemsTotal) {
            selectedItemsTotal.textContent = `${sum.toLocaleString()}원`;
        }
    }

    function memberScopeLabel(code) {
        if (code === 'member') {
            return '회원';
        }
        if (code === 'non-member') {
            return '비회원';
        }
        return code ? String(code) : '-';
    }

    function parsePayload(raw) {
        if (!raw) {
            return [];
        }
        try {
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function escapeAttr(value) {
        return escapeHtml(value).replaceAll('`', '&#096;');
    }
})();
