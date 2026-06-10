(function () {
    const form = document.getElementById('academic-registration-form');
    if (!form) {
        return;
    }

    const paymentInputs = Array.from(form.querySelectorAll('input[name="payment_plan_ids[]"]'));
    const membershipInputs = Array.from(form.querySelectorAll('input[name="membership_plan_id"]'));
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
    const submitButton = form.querySelector('button[type="submit"]');
    let appliedDiscount = 0;
    let appliedCouponCode = '';
    let tossSdkPromise = null;
    let daumPostcodePromise = null;

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

    function hasSelectedConferencePlan() {
        return selectedPlans().length > 0;
    }

    function selectedMembershipPlan() {
        return membershipInputs.find((input) => input.checked) || null;
    }

    function effectiveGrade() {
        const membershipPlan = selectedMembershipPlan();
        return membershipPlan?.dataset.grade || form.dataset.currentGrade || '';
    }

    function subtotal() {
        const conferenceTotal = selectedPlans().reduce((total, input) => total + (Number(input.dataset.price) || 0), 0);
        const membershipTotal = Number(selectedMembershipPlan()?.dataset.price || 0) || 0;

        return conferenceTotal + membershipTotal;
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
        const itemLabels = plans.map((input) => input.dataset.label || input.value);
        const membershipPlan = selectedMembershipPlan();
        if (membershipPlan && membershipPlan.value && membershipPlan.dataset.label) {
            itemLabels.unshift(membershipPlan.dataset.label);
        }
        const itemLabel = itemLabels.length > 0 ? itemLabels.join(', ') : '-';

        if (summaryItems) {
            summaryItems.textContent = itemLabel;
        }
        if (summarySubtotal) {
            summarySubtotal.textContent = numberFormat(currentSubtotal);
        }
        if (summaryDiscount) {
            summaryDiscount.textContent = discount > 0 ? '-' + numberFormat(discount) : '0';
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
            window.alert('결제항목을 선택해 주세요.');
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

    function updateConferencePlanAvailability() {
        const grade = effectiveGrade();
        paymentInputs.forEach((input) => {
            const grades = String(input.dataset.grades || '').split(',').filter(Boolean);
            const enabled = grades.length === 0 || grades.includes(grade);
            input.disabled = !enabled;
            if (!enabled && input.checked) {
                input.checked = false;
            }
        });

        if (!hasSelectedConferencePlan()) {
            const firstEnabled = paymentInputs.find((input) => !input.disabled);
            if (firstEnabled) {
                firstEnabled.checked = true;
            }
        }

        updateSubmitButtonState();
    }

    function updateSubmitButtonState() {
        if (!submitButton) {
            return;
        }

        const isBlocked = !hasSelectedConferencePlan();
        submitButton.classList.toggle('is-disabled', isBlocked);
        submitButton.setAttribute('aria-disabled', isBlocked ? 'true' : 'false');
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

    function validateRequiredSelections() {
        if (membershipInputs.length > 0 && !selectedMembershipPlan()) {
            window.alert('연회비 결제 항목을 선택해주세요.');
            membershipInputs[0]?.focus();
            return false;
        }
        if (selectedPlans().length === 0) {
            window.alert('결제항목을 선택해 주세요.');
            paymentInputs[0]?.focus();
            return false;
        }

        const terms = form.querySelector('input[name="terms_agree"]');
        if (terms && !terms.checked) {
            window.alert('결제 이용 약관, 개인정보 처리 동의가 필요합니다.');
            terms.focus();
            return false;
        }

        return true;
    }

    function fieldLabel(field) {
        const id = field.getAttribute('id');
        const label = id
            ? Array.from(form.querySelectorAll('label')).find((candidate) => candidate.getAttribute('for') === id)
            : null;
        return (label?.textContent || field.getAttribute('title') || field.getAttribute('placeholder') || '필수 항목')
            .replace('*', '')
            .trim();
    }

    function isVisibleField(field) {
        return Boolean(field.offsetWidth || field.offsetHeight || field.getClientRects().length);
    }

    function validateRequiredFields() {
        const fields = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'));
        const invalid = fields.find((field) => {
            if (field.disabled || field.readOnly || field.type === 'hidden' || !isVisibleField(field)) {
                return false;
            }
            if (field.type === 'checkbox' || field.type === 'radio') {
                return !field.checked;
            }

            return !field.value.trim();
        });

        if (!invalid) {
            return true;
        }

        window.alert(fieldLabel(invalid) + '을(를) 입력해주세요.');
        invalid.focus();

        return false;
    }

    function validateBankTransferFields() {
        if (selectedPaymentMethod() !== 'bank') {
            return true;
        }

        const depositor = form.querySelector('input[name="bank_depositor"]');
        if (depositor && !depositor.value.trim()) {
            window.alert('입금자명을 입력해주세요.');
            depositor.focus();
            return false;
        }

        const depositDate = form.querySelector('input[name="bank_deposit_date"]');
        if (depositDate && !depositDate.value.trim()) {
            window.alert('입금 예정일을 선택해주세요.');
            depositDate.focus();
            return false;
        }

        const refundAccount = form.querySelector('input[name="refund_account"]');
        if (refundAccount && !refundAccount.value.trim()) {
            window.alert('환불 계좌번호를 입력해주세요.');
            refundAccount.focus();
            return false;
        }

        const refundHolder = form.querySelector('input[name="refund_holder"]');
        if (refundHolder && !refundHolder.value.trim()) {
            window.alert('예금주명을 입력해주세요.');
            refundHolder.focus();
            return false;
        }

        const isReceipt = form.querySelector('input[name="receipt_issue"]:checked')?.value === 'YES';
        const receiptNumber = form.querySelector('input[name="receipt_number"]');
        if (isReceipt && receiptNumber && !receiptNumber.value.trim()) {
            window.alert('현금영수증 번호를 입력해주세요.');
            receiptNumber.focus();
            return false;
        }

        return true;
    }

    function focusFirstInvalidField() {
        const firstError = form.querySelector('.c_red[role="alert"]:not(.type_card)');
        if (!firstError) {
            return;
        }

        const container = firstError.closest('li, fieldset, form') || firstError.parentElement;
        if (!container) {
            return;
        }

        const target = container.querySelector('input:not([type="hidden"]):not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled]), button:not([disabled])');
        window.setTimeout(function () {
            (target || firstError).scrollIntoView({ block: 'center' });
            if (target) {
                target.focus({ preventScroll: true });
            }
        }, 80);
    }

    function bindStickySummary() {
		if (!window.jQuery || !document.querySelector('.registration_form_wrap .abso_application')) { return; }
		const $window = window.jQuery(window);
		const $detail = window.jQuery('.registration_form_wrap');
		const $inbox = $detail.find('.inbox');
		const $absoApp = window.jQuery('.abso_application');
		let touchStartY = 0, startBottom = 0, isDragging = false, isUserOpened = false;

		function getClosedBottom(appHeight) {
			return `-${appHeight - 32}px`;
		}

		if ($window.width() <= 767) {
			const initialHeight = $absoApp.outerHeight() || 0;
			$absoApp.removeClass('open').css({
				'bottom': getClosedBottom(initialHeight),
				'transition': 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)'
			});
		}

		function handleStickyLayout() {
			if (!$detail.length || !$absoApp.length) { return; }
			const scrollTop = $window.scrollTop(), windowHeight = $window.height(), windowWidth = $window.width();
			const detailOffsetTop = $detail.offset().top, detailHeight = $detail.outerHeight(), appHeight = $absoApp.outerHeight();
			if (windowWidth <= 767) {
				$inbox.css('padding-bottom', (appHeight + 20) + 'px');
				const mobileUnfixPoint = (detailOffsetTop + detailHeight) - windowHeight;
				if (scrollTop >= mobileUnfixPoint) {
					$detail.addClass('unfixed').removeClass('fixed');
					if (!isDragging) {
						$absoApp.css('transition', 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)').addClass('open').css('bottom', '0px');
					}
				} else {
					$detail.removeClass('unfixed').addClass('fixed');
					if (!isDragging) {
						if (isUserOpened) {
							$absoApp.addClass('open').css('bottom', '0px');
						} else {
							$absoApp.removeClass('open');
							$absoApp.css('bottom', getClosedBottom(appHeight));
						}
					}
				}
			} else {
				$inbox.css('padding-bottom', '');
				$absoApp.removeClass('open').css({ 'bottom': '', 'transition': '' });
				const fixStartPoint = detailOffsetTop - 120;
				const unfixPoint = (detailOffsetTop + detailHeight) - appHeight - 120;
				if (scrollTop >= fixStartPoint) {
					if (scrollTop >= unfixPoint) { $detail.addClass('unfixed').removeClass('fixed'); }
					else { $detail.addClass('fixed').removeClass('unfixed'); }
				} else { $detail.removeClass('fixed unfixed'); }
			}
		}
		$absoApp.on('touchstart.stickyApp', function (e) {
			if ($window.width() > 767 || $detail.hasClass('unfixed')) { return; }
			touchStartY = e.originalEvent.touches[0].clientY;
			isDragging = true;
			$absoApp.css('transition', 'none');
			startBottom = window.innerHeight - $absoApp[0].getBoundingClientRect().bottom;
		});
		$absoApp.on('touchmove.stickyApp', function (e) {
			if (!isDragging || $window.width() > 767 || $detail.hasClass('unfixed')) { return; }
			const diffY = touchStartY - e.originalEvent.touches[0].clientY;
			if (Math.abs(diffY) > 5 && e.cancelable) { e.preventDefault(); }
			let currentBottom = startBottom + diffY;
			if (currentBottom > 0) { currentBottom = 0; }
			$absoApp.css('bottom', currentBottom + 'px');
		});
		$absoApp.on('touchend.stickyApp touchcancel.stickyApp', function (e) {
			if (!isDragging || $window.width() > 767) { return; }
			isDragging = false;
			const diffY = touchStartY - e.originalEvent.changedTouches[0].clientY, appHeight = $absoApp.outerHeight();
			$absoApp.css('transition', 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)');
			const closedBottom = getClosedBottom(appHeight);
			if (diffY > 30) {
				$absoApp.addClass('open').css('bottom', '0px');
				isUserOpened = true;
			} else if (diffY < -30) {
				$absoApp.removeClass('open').css('bottom', closedBottom);
				isUserOpened = false;
			} else {
				if ($absoApp.hasClass('open')) { $absoApp.css('bottom', '0px'); isUserOpened = true; }
				else { $absoApp.css('bottom', closedBottom); isUserOpened = false; }
			}
		});
		$window.on('scroll.stickyApp resize.stickyApp', handleStickyLayout);
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

    function loadDaumPostcode() {
        if (window.daum && window.daum.Postcode) {
            return Promise.resolve(window.daum.Postcode);
        }
        if (daumPostcodePromise) {
            return daumPostcodePromise;
        }

        daumPostcodePromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js';
            script.async = true;
            script.onload = function () {
                if (window.daum && window.daum.Postcode) {
                    resolve(window.daum.Postcode);
                    return;
                }
                reject(new Error('Daum postcode is not available.'));
            };
            script.onerror = function () {
                reject(new Error('Failed to load Daum postcode.'));
            };
            document.head.appendChild(script);
        });

        return daumPostcodePromise;
    }

    function bindAddressSearch() {
        if (!addressSearchButton) {
            return;
        }
        addressSearchButton.addEventListener('click', async function () {
            let Postcode = null;
            try {
                Postcode = await loadDaumPostcode();
            } catch (error) {
                window.alert('주소 검색을 불러오지 못했습니다. 페이지를 새로고침 후 다시 시도해주세요.');
                return;
            }
            new Postcode({
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

    function loadTossSdk() {
        if (window.TossPayments) {
            return Promise.resolve(window.TossPayments);
        }
        if (tossSdkPromise) {
            return tossSdkPromise;
        }

        tossSdkPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://js.tosspayments.com/v2/standard';
            script.async = true;
            script.onload = function () {
                if (window.TossPayments) {
                    resolve(window.TossPayments);
                    return;
                }
                reject(new Error('TossPayments SDK is not available.'));
            };
            script.onerror = function () {
                reject(new Error('Failed to load TossPayments SDK.'));
            };
            document.head.appendChild(script);
        });

        return tossSdkPromise;
    }

    async function requestTossPayment() {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: new FormData(form),
        });
        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data.success) {
            const errors = data.errors || {};
            const firstError = Object.keys(errors).length > 0 ? errors[Object.keys(errors)[0]][0] : null;
            window.alert(firstError || data.message || '입력 내용을 확인해주세요.');
            return;
        }
        if (!data.clientKey) {
            window.alert('토스페이먼츠 테스트 클라이언트 키가 설정되어 있지 않습니다.');
            return;
        }

        const TossPayments = await loadTossSdk();
        const tossPayments = TossPayments(data.clientKey);
        const payment = tossPayments.payment({ customerKey: data.customerKey });
        await payment.requestPayment({
            method: 'CARD',
            amount: {
                value: Number(data.amount) || 0,
                currency: 'KRW',
            },
            orderId: data.orderId,
            orderName: data.orderName,
            customerName: data.customerName,
            customerEmail: data.customerEmail,
            customerMobilePhone: String(data.customerMobilePhone || '').replace(/\D/g, ''),
            successUrl: data.successUrl,
            failUrl: data.failUrl,
        });
    }

    paymentInputs.forEach((input) => {
        input.addEventListener('change', function () {
            if (appliedCouponCode) {
                resetCoupon();
            }
            updateSummary();
            updateSubmitButtonState();
        });
    });
    membershipInputs.forEach((input) => {
        input.addEventListener('change', function () {
            if (appliedCouponCode) {
                resetCoupon();
            }
            updateConferencePlanAvailability();
            updateSummary();
            updateSubmitButtonState();
        });
    });
    receiptRadios.forEach((input) => input.addEventListener('change', toggleReceiptArea));
    paymentMethodRadios.forEach((input) => input.addEventListener('change', togglePaymentMethod));
    if (couponButton) {
        couponButton.addEventListener('click', applyCoupon);
    }
    form.addEventListener('submit', async function (event) {
        togglePaymentMethod();
        if (!hasSelectedConferencePlan()) {
            window.alert('결제항목을 선택해 주세요.');
            paymentInputs.find((input) => !input.disabled)?.focus();
            event.preventDefault();
            return;
        }
        if (!validateRequiredFields() || !validateRequiredSelections() || !validateBankTransferFields()) {
            event.preventDefault();
            return;
        }

        if (selectedPaymentMethod() === 'bank') {
            return;
        }

        event.preventDefault();
        if (submitButton) {
            submitButton.disabled = true;
        }
        try {
            await requestTossPayment();
        } catch (error) {
            window.alert('토스페이먼츠 결제창을 여는 중 오류가 발생했습니다.');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    });

    bindStickySummary();
    bindPopup();
    bindAddressSearch();
    bindPhoneInput();
    updateConferencePlanAvailability();
    togglePaymentMethod();
    updateSummary();
    updateSubmitButtonState();
    focusFirstInvalidField();
})();
