/**
 * 쿠폰 등록/수정: 할인 방식 전환, 코드 자동 생성, 유효기간 검증, 삭제
 */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('bo-coupon-form-root');
    const generateUrl = root?.dataset?.generateCodeUrl;
    const generateBtn = document.getElementById('bo-coupon-generate-btn');
    const codeInput = document.getElementById('coupon_code');
    const typeRadios = document.querySelectorAll('.bo-discount-type-radio');
    const fixedPanel = document.getElementById('bo-discount-fixed-panel');
    const ratePanel = document.getElementById('bo-discount-rate-panel');
    const discountAmount = document.getElementById('discount_amount');
    const discountRate = document.getElementById('discount_rate');
    const validFrom = document.getElementById('valid_from');
    const validTo = document.getElementById('valid_to');
    const deleteBtn = document.getElementById('bo-coupon-delete-btn');
    const deleteForm = document.getElementById('bo-coupon-delete-form');

    const setPanelDisabled = (panel, disabled) => {
        if (!panel) {
            return;
        }
        panel.querySelectorAll('input, select, textarea').forEach((el) => {
            el.disabled = disabled;
        });
    };

    const syncDiscountPanels = () => {
        const checked = document.querySelector('.bo-discount-type-radio:checked');
        const type = checked ? checked.value : 'FIXED';

        if (type === 'FIXED') {
            fixedPanel?.classList.remove('bo-hidden');
            ratePanel?.classList.add('bo-hidden');
            setPanelDisabled(fixedPanel, false);
            setPanelDisabled(ratePanel, true);
        } else {
            fixedPanel?.classList.add('bo-hidden');
            ratePanel?.classList.remove('bo-hidden');
            setPanelDisabled(fixedPanel, true);
            setPanelDisabled(ratePanel, false);
        }
    };

    const validateDateRange = () => {
        if (!validFrom || !validTo || !validFrom.value || !validTo.value) {
            return;
        }
        if (validFrom.value > validTo.value) {
            window.alert('종료일은 시작일 이후여야 합니다.');
            validTo.value = '';
        }
    };

    typeRadios.forEach((r) => {
        r.addEventListener('change', syncDiscountPanels);
    });

    if (validFrom) {
        validFrom.addEventListener('change', validateDateRange);
    }
    if (validTo) {
        validTo.addEventListener('change', validateDateRange);
    }

    syncDiscountPanels();

    if (generateBtn && generateUrl && codeInput) {
        generateBtn.addEventListener('click', async () => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(generateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: '{}',
            });
            if (!response.ok) {
                window.alert('코드 생성에 실패했습니다.');
                return;
            }
            const data = await response.json();
            if (data.coupon_code) {
                codeInput.value = data.coupon_code;
            }
        });
    }

    if (deleteBtn && deleteForm) {
        deleteBtn.addEventListener('click', () => {
            const ok = window.confirm('정말 이 쿠폰을 삭제하시겠습니까?');
            if (ok) {
                deleteForm.submit();
            }
        });
    }
});
