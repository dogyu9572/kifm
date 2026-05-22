(function () {
    const form = document.getElementById('academic-registration-form');
    if (!form) {
        return;
    }

    const paymentInputs = Array.from(form.querySelectorAll('input[name="payment_plan_ids[]"]'));
    const couponInput = document.getElementById('coupon_num');
    const couponButton = document.getElementById('academic-coupon-apply-btn');
    const couponResult = document.querySelector('#academic-coupon-result dd');
    const summaryItems = document.getElementById('academic-summary-items');
    const summarySubtotal = document.getElementById('academic-summary-subtotal');
    const summaryDiscount = document.getElementById('academic-summary-discount');
    const summaryTotal = document.getElementById('academic-summary-total');
    const submitAmount = document.getElementById('academic-submit-amount');
    const bankElements = Array.from(document.querySelectorAll('.type_bank_hide'));
    const cardMessages = Array.from(document.querySelectorAll('.type_card'));
    const paymentMethodInput = document.getElementById('academic-payment-method');
    const paymentMethodRadios = Array.from(form.querySelectorAll('input[name="payment_method_display"]'));
    const cashReceiptArea = document.querySelector('.cash_receipt_area');
    const receiptRadios = Array.from(form.querySelectorAll('input[name="receipt_issue"]'));
    const addressSearchButton = document.getElementById('academic-registration-address-search');
    const phoneInput = document.getElementById('user_tel');
    let appliedDiscount = 0;
    let appliedCouponCode = '';

    function formatPhoneKoreaDisplay(raw) {
        const d = String(raw || '').replace(/\D/g, '').slice(0, 11);
        if (!d.length) {
            return '';
        }
        if (d.length <= 3) {
            return d;
        }
        if (d.length === 10 && d[2] !== '0') {
            return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
        }
        if (d.startsWith('010') || d.length === 11 || /^01[6789]/.test(d)) {
            if (d.length <= 7) {
                return d.slice(0, 3) + '-' + d.slice(3);
            }
            return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
        }
        if (d.startsWith('011')) {
            if (d.length <= 6) {
                return d.slice(0, 3) + '-' + d.slice(3);
            }
            if (d.length === 10) {
                return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
            }
        }
        if (d.length <= 7) {
            return d.slice(0, 3) + '-' + d.slice(3);
        }
        return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
    }

    function numberFormat(value) {
        return new Intl.NumberFormat('ko-KR').format(Math.max(0, Number(value) || 0));
    }

    function selectedPlans() {
        return paymentInputs.filter((input) => input.checked);
    }

    function subtotal() {
        return selectedPlans().reduce((total, input) => total + (Number(input.dataset.price) || 0), 0);
    }

    function setCouponMessage(message) {
        if (couponResult) {
            couponResult.textContent = message;
        }
    }

    function resetCoupon() {
        appliedDiscount = 0;
        appliedCouponCode = '';
        setCouponMessage('적용된 쿠폰이 없습니다.');
    }

    function updateSummary() {
        const plans = selectedPlans();
        const currentSubtotal = subtotal();
        const discount = Math.min(appliedDiscount, currentSubtotal);
        const total = Math.max(0, currentSubtotal - discount);
        const itemLabel = plans.length > 0
            ? plans.map((input) => input.dataset.label || input.value).join(', ')
            : '-';

        if (summaryItems) {
            summaryItems.textContent = itemLabel;
        }
        if (summarySubtotal) {
            summarySubtotal.textContent = numberFormat(currentSubtotal);
        }
        if (summaryDiscount) {
            summaryDiscount.textContent = '-' + numberFormat(discount);
        }
        if (summaryTotal) {
            summaryTotal.textContent = numberFormat(total);
        }
        if (submitAmount) {
            submitAmount.textContent = numberFormat(total) + '원 ';
        }
    }

    function csrfToken() {
        const token = form.querySelector('input[name="_token"]');
        return token ? token.value : '';
    }

    async function applyCoupon() {
        const couponCode = couponInput ? couponInput.value.trim() : '';
        const ids = selectedPlans().map((input) => input.value);

        if (!couponCode) {
            window.alert('쿠폰번호를 입력해주세요.');
            return;
        }
        if (ids.length === 0) {
            window.alert('결제 항목을 선택해주세요.');
            return;
        }

        couponButton.disabled = true;
        try {
            const response = await fetch(form.dataset.couponUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({
                    coupon_code: couponCode,
                    payment_plan_ids: ids,
                }),
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                resetCoupon();
                updateSummary();
                window.alert(data.message || '사용 가능한 쿠폰이 아닙니다.');
                return;
            }

            appliedCouponCode = couponCode.toUpperCase();
            appliedDiscount = Number(data.discount) || 0;
            setCouponMessage(`${appliedCouponCode} / ${numberFormat(appliedDiscount)}원 할인`);
            updateSummary();
        } catch (error) {
            window.alert('쿠폰 확인 중 오류가 발생했습니다.');
        } finally {
            couponButton.disabled = false;
        }
    }

    function selectedPaymentMethod() {
        return form.querySelector('input[name="payment_method_display"]:checked')?.value || 'card';
    }

    function togglePaymentMethod() {
        const isBank = selectedPaymentMethod() === 'bank';
        if (paymentMethodInput) {
            paymentMethodInput.value = isBank ? 'bank_transfer' : 'card';
        }
        bankElements.forEach((element) => {
            element.style.display = isBank ? 'block' : 'none';
        });
        cardMessages.forEach((element) => {
            element.style.display = isBank ? 'none' : 'block';
        });
        toggleReceiptArea();
    }

    function toggleReceiptArea() {
        const isReceipt = form.querySelector('input[name="receipt_issue"]:checked')?.value === 'YES';
        const isBank = selectedPaymentMethod() === 'bank';
        if (cashReceiptArea) {
            cashReceiptArea.style.display = isBank && isReceipt ? 'block' : 'none';
        }
        if (window.jQuery) {
            window.jQuery(window).trigger('scroll.stickyApp');
        }
    }

    function bindStickySummary() {
        if (!window.jQuery) {
            return;
        }

        const $window = window.jQuery(window);
        const $detail = window.jQuery('.registration_form_wrap');
        const $absoApp = window.jQuery('.abso_application');
        $window.on('scroll.stickyApp', function () {
            const scrollTop = $window.scrollTop();
            const detailOffsetTop = $detail.offset().top;
            const detailHeight = $detail.outerHeight();
            const appHeight = $absoApp.outerHeight();
            const fixStartPoint = detailOffsetTop - 120;
            const unfixPoint = (detailOffsetTop + detailHeight) - appHeight - 120;

            if (scrollTop >= fixStartPoint) {
                if (scrollTop >= unfixPoint) {
                    $detail.addClass('unfixed').removeClass('fixed');
                } else {
                    $detail.addClass('fixed').removeClass('unfixed');
                }
            } else {
                $detail.removeClass('fixed unfixed');
            }
        });
        $window.trigger('scroll.stickyApp');
    }

    function bindPopup() {
        document.querySelectorAll('[data-popup-open]').forEach((button) => {
            button.addEventListener('click', function () {
                const popup = document.getElementById(button.dataset.popupOpen);
                if (popup) {
                    popup.classList.add('show');
                    popup.style.display = 'block';
                }
            });
        });
        document.querySelectorAll('[data-popup-close]').forEach((button) => {
            button.addEventListener('click', function () {
                const popup = document.getElementById(button.dataset.popupClose);
                if (popup) {
                    popup.classList.remove('show');
                    popup.style.display = 'none';
                }
            });
        });
    }

    function bindAddressSearch() {
        if (!addressSearchButton) {
            return;
        }
        addressSearchButton.addEventListener('click', function () {
            if (typeof daum === 'undefined' || !daum.Postcode) {
                window.alert('주소 검색을 불러오지 못했습니다. 페이지를 새로고침 후 다시 시도해주세요.');
                return;
            }
            new daum.Postcode({
                oncomplete(data) {
                    const postcode = document.getElementById('address_postcode');
                    const base = document.getElementById('address_base');
                    const detail = document.getElementById('address_detail');
                    if (postcode) {
                        postcode.value = data.zonecode || '';
                    }
                    if (base) {
                        base.value = data.roadAddress || data.address || '';
                    }
                    if (detail) {
                        detail.focus();
                    }
                },
            }).open();
        });
    }

    function bindPhoneInput() {
        if (!phoneInput) {
            return;
        }
        const apply = function () {
            const formatted = formatPhoneKoreaDisplay(phoneInput.value);
            phoneInput.value = formatted;
            const end = formatted.length;
            phoneInput.setSelectionRange(end, end);
        };
        if (phoneInput.value) {
            phoneInput.value = formatPhoneKoreaDisplay(phoneInput.value);
        }
        phoneInput.addEventListener('input', apply);
        phoneInput.addEventListener('blur', function () {
            phoneInput.value = formatPhoneKoreaDisplay(phoneInput.value);
        });
        phoneInput.addEventListener('paste', function () {
            window.requestAnimationFrame(apply);
        });
    }

    paymentInputs.forEach((input) => {
        input.addEventListener('change', function () {
            if (appliedCouponCode) {
                resetCoupon();
            }
            updateSummary();
        });
    });
    receiptRadios.forEach((input) => input.addEventListener('change', toggleReceiptArea));
    paymentMethodRadios.forEach((input) => input.addEventListener('change', togglePaymentMethod));
    if (couponButton) {
        couponButton.addEventListener('click', applyCoupon);
    }
    form.addEventListener('submit', function (event) {
        if (selectedPaymentMethod() !== 'bank') {
            event.preventDefault();
            window.alert('신용카드 결제는 테스트 모듈 설치 후 제공됩니다. 무통장입금을 선택해주세요.');
        }
    });

    bindStickySummary();
    bindPopup();
    bindAddressSearch();
    bindPhoneInput();
    togglePaymentMethod();
    updateSummary();
})();
