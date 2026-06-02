(function () {
    function numberFormat(value) {
        return new Intl.NumberFormat('ko-KR').format(Math.max(0, Number(value) || 0));
    }
    function initTabs() {
        const tabWrap = document.querySelector('[data-training-tabs]');
        if (!tabWrap) { return; }
        const tabs = Array.from(tabWrap.querySelectorAll('[role="tab"]'));
        tabs.forEach((tab) => {
            tab.addEventListener('click', function (event) {
                event.preventDefault();
                const targetId = tab.getAttribute('aria-controls') || tab.getAttribute('href')?.replace('#', '');
                if (!targetId) { return; }
                tabs.forEach((item) => {
                    item.setAttribute('aria-selected', item === tab ? 'true' : 'false');
                    item.closest('li')?.classList.toggle('on', item === tab);
                });
                document.querySelectorAll('.tab_wrap [role="tabpanel"]').forEach((panel) => {
                    panel.hidden = panel.id !== targetId;
                });
            });
        });
    }
    function initStickySummary() {
        if (!window.jQuery || !document.querySelector('.training_course_view_wrap .abso_application')) { return; }
        const $window = window.jQuery(window);
        const $detail = window.jQuery('.training_course_view_wrap');
        const $inbox = $detail.find('.inbox');
        const $absoApp = window.jQuery('.abso_application');
        
        let touchStartY = 0;
        let startBottom = 0;
        let isDragging = false;
        let isUserOpened = true; 

        function handleStickyLayout() {
            if (!$detail.length || !$absoApp.length) { return; }
            const scrollTop = $window.scrollTop();
            const windowHeight = $window.height();
            const windowWidth = $window.width();
            const detailOffsetTop = $detail.offset().top;
            const detailHeight = $detail.outerHeight();
            const appHeight = $absoApp.outerHeight();

            if (windowWidth <= 767) {
                $inbox.css('padding-bottom', (appHeight + 20) + 'px');
                const mobileUnfixPoint = (detailOffsetTop + detailHeight) - windowHeight;
                
                if (scrollTop >= mobileUnfixPoint) {
                    $detail.addClass('unfixed').removeClass('fixed');
                    if (!isDragging) {
                        $absoApp.css('transition', 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)');
                        $absoApp.addClass('open').css('bottom', '0px');
                    }
                } else {
                    $detail.removeClass('unfixed').addClass('fixed');
                    if (!isDragging) {
                        if (isUserOpened) {
                            $absoApp.addClass('open').css('bottom', '0px');
                        } else {
                            $absoApp.removeClass('open');
                            const closedBottom = `calc(-100% + (100vh - ${appHeight}px) + 33px)`;
                            $absoApp.css('bottom', closedBottom);
                        }
                    }
                }
            } else {
                $inbox.css('padding-bottom', '');
                $absoApp.removeClass('open').css({ 'bottom': '', 'transition': '' });
                const fixStartPoint = detailOffsetTop - 190;
                const unfixPoint = detailOffsetTop + detailHeight - appHeight - 190;
                if (scrollTop >= fixStartPoint) {
                    if (scrollTop >= unfixPoint) {
                        $detail.addClass('unfixed').removeClass('fixed');
                    } else {
                        $detail.addClass('fixed').removeClass('unfixed');
                    }
                } else {
                    $detail.removeClass('fixed unfixed');
                }
            }
        }

        $absoApp.on('touchstart.stickyApp', function (e) {
            if ($window.width() > 767 || $detail.hasClass('unfixed')) { return; }
            const touch = e.originalEvent.touches[0];
            touchStartY = touch.clientY;
            isDragging = true;
            $absoApp.css('transition', 'none');
            const windowHeight = window.innerHeight;
            const appRect = $absoApp[0].getBoundingClientRect();
            startBottom = windowHeight - appRect.bottom;
        });

        $absoApp.on('touchmove.stickyApp', function (e) {
            if (!isDragging || $window.width() > 767 || $detail.hasClass('unfixed')) { return; }
            const touch = e.originalEvent.touches[0];
            const diffY = touchStartY - touch.clientY;
            if (Math.abs(diffY) > 5) {
                if (e.cancelable) { e.preventDefault(); }
            }
            let currentBottom = startBottom + diffY;
            if (currentBottom > 0) { currentBottom = 0; }
            $absoApp.css('bottom', currentBottom + 'px');
        });

        $absoApp.on('touchend.stickyApp touchcancel.stickyApp', function (e) {
            if (!isDragging || $window.width() > 767) { return; }
            isDragging = false;
            const touch = e.originalEvent.changedTouches[0];
            const diffY = touchStartY - touch.clientY;
            const appHeight = $absoApp.outerHeight();
            const isUnfixed = $detail.hasClass('unfixed');

            $absoApp.css('transition', 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)');
            const closedBottom = isUnfixed 
                ? `-${appHeight - 33}px` 
                : `calc(-100% + (100vh - ${appHeight}px) + 33px)`;

            if (diffY > 30) {
                $absoApp.addClass('open').css('bottom', '0px');
                isUserOpened = true;
            } else if (diffY < -30) {
                $absoApp.removeClass('open').css('bottom', closedBottom);
                isUserOpened = false;
            } else {
                if ($absoApp.hasClass('open')) {
                    $absoApp.css('bottom', '0px');
                    isUserOpened = true;
                } else {
                    $absoApp.css('bottom', closedBottom);
                    isUserOpened = false;
                }
            }
        });

        $window.on('scroll.trainingCourseSticky resize.trainingCourseSticky', handleStickyLayout);
        $window.trigger('scroll.trainingCourseSticky');
    }
    function initListFilters() {
        const listSection = document.querySelector('.academic_event_body');
        const form = document.getElementById('training-course-filter-form');
        if (!listSection || !form) { return; }
        form.querySelectorAll('[data-auto-submit-form]').forEach((select) => {
            select.addEventListener('change', function () {
                form.submit();
            });
        });
        const statusInput = form.querySelector('[data-training-status-input]');
        document.querySelectorAll('[data-training-course-tabs] a').forEach((link) => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const url = link.getAttribute('href');
                const status = link.dataset.trainingStatus || '';
                link.closest('li')?.classList.add('on');
                link.closest('li')?.parentElement?.querySelectorAll('li').forEach((li) => {
                    if (li !== link.closest('li')) { li.classList.remove('on'); }
                });
                if (statusInput) { statusInput.value = status === 'all' ? '' : status; }
                if (window.jQuery) {
                    const headerHeight = window.jQuery('.header').outerHeight() || 0;
                    const targetPos = window.jQuery(listSection).offset().top - headerHeight;
                    window.jQuery('html, body').stop().animate({ scrollTop: Math.max(0, targetPos) }, 500);
                }
                if (url) { loadTrainingList(url); }
            });
        });
    }
    function loadTrainingList(url) {
        const list = document.querySelector('.training-course-list');
        const pagination = document.querySelector('.board-pagination');
        if (!list) { window.location.href = url; return; }
        list.classList.add('is-loading');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => {
                if (!response.ok) { throw new Error('Failed to load training course list.'); }
                return response.text();
            })
            .then((html) => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextList = doc.querySelector('.training-course-list');
                const nextPagination = doc.querySelector('.board-pagination');
                if (!nextList) { throw new Error('Training course list not found.'); }
                list.replaceWith(nextList);
                if (pagination && nextPagination) {
                    pagination.replaceWith(nextPagination);
                } else if (pagination && !nextPagination) {
                    pagination.remove();
                } else if (!pagination && nextPagination) {
                    nextList.insertAdjacentElement('afterend', nextPagination);
                }
                window.history.pushState({}, '', url);
            })
            .catch(() => { window.location.href = url; })
            .finally(() => { document.querySelector('.training-course-list')?.classList.remove('is-loading'); });
    }
    function initPaymentForm() {
        const form = document.getElementById('training-course-payment-form');
        if (!form) { return; }
        const roundInputs = Array.from(form.querySelectorAll('input[name="round_ids[]"]'));
        const couponInput = document.getElementById('training-coupon-num');
        const couponHidden = document.getElementById('training-coupon-code-hidden');
        const couponButton = document.getElementById('training-coupon-apply-btn');
        const couponResult = document.getElementById('training-coupon-result');
        const summaryItems = document.getElementById('training-summary-items');
        const summarySubtotal = document.getElementById('training-summary-subtotal');
        const summaryDiscount = document.getElementById('training-summary-discount');
        const summaryTotal = document.getElementById('training-summary-total');
        const submitAmount = document.getElementById('training-submit-amount');
        const bankElements = Array.from(document.querySelectorAll('.type_bank_hide'));
        const cardElements = Array.from(document.querySelectorAll('.type_card'));
        const receiptArea = document.querySelector('.cash_receipt_area');
        const submitButton = form.querySelector('button[type="submit"]');
        const termsCheckbox = document.getElementById('training-terms-agree');
        let appliedDiscount = 0;
        let appliedCouponCode = '';
        let tossSdkPromise = null;
        function selectedRounds() {
            return roundInputs.filter((input) => input.checked && !input.disabled);
        }
        function subtotal() {
            return selectedRounds().reduce((sum, input) => sum + (Number(input.dataset.price) || 0), 0);
        }
        function resetCoupon() {
            appliedDiscount = 0;
            appliedCouponCode = '';
            if (couponHidden) { couponHidden.value = ''; }
            if (couponResult) { couponResult.textContent = '적용된 쿠폰이 없습니다.'; }
        }
        function updateSubmitButton() {
            if (!submitButton) { return; }
            const total = Math.max(0, subtotal() - Math.min(appliedDiscount, subtotal()));
            if (termsCheckbox && termsCheckbox.checked) {
                submitButton.className = 'btn_submit btn_wbb';
                submitButton.innerHTML = `<span class="sound_only" id="training-submit-amount">${numberFormat(total)}원 </span>결제하기`;
            } else {
                submitButton.className = 'btn_submit btn_wgg';
                submitButton.innerHTML = '결제 약관에 동의해주세요.';
            }
        }
        function updateSummary() {
            const selected = selectedRounds();
            const currentSubtotal = subtotal();
            const discount = Math.min(appliedDiscount, currentSubtotal);
            const total = Math.max(0, currentSubtotal - discount);
            const itemLabel = selected.length > 0
                ? selected.map((input) => input.dataset.label || input.value).join(', ')
                : '결제 항목을 선택해주세요.';
            if (summaryItems) { summaryItems.textContent = itemLabel; }
            if (summarySubtotal) { summarySubtotal.textContent = numberFormat(currentSubtotal); }
            if (summaryDiscount) { summaryDiscount.textContent = discount > 0 ? '-' + numberFormat(discount) : '0'; }
            if (summaryTotal) { summaryTotal.textContent = numberFormat(total); }
            updateSubmitButton();
            if (window.jQuery) { window.jQuery(window).trigger('scroll.trainingCourseSticky'); }
        }
        function currentTotal() {
            return Number((summaryTotal?.textContent || '0').replace(/\D/g, '')) || 0;
        }
        function csrfToken() {
            return form.querySelector('input[name="_token"]')?.value || '';
        }
        async function applyCoupon() {
            const code = couponInput ? couponInput.value.trim() : '';
            const ids = selectedRounds().map((input) => input.value);
            if (!code) { window.alert('쿠폰번호를 입력해주세요.'); return; }
            if (ids.length === 0) { window.alert('결제 항목을 선택해주세요.'); return; }
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
                        training_id: form.querySelector('input[name="training_id"]')?.value || '',
                        round_ids: ids,
                        coupon_code: code,
                    }),
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    resetCoupon();
                    updateSummary();
                    window.alert(data.message || '사용 가능한 쿠폰이 아닙니다.');
                    return;
                }
                appliedCouponCode = code.toUpperCase();
                appliedDiscount = Number(data.discount) || 0;
                if (couponHidden) { couponHidden.value = appliedCouponCode; }
                if (couponResult) { couponResult.textContent = appliedCouponCode + ' / ' + numberFormat(appliedDiscount) + '원 할인'; }
                updateSummary();
            } catch (error) {
                window.alert('쿠폰 확인 중 오류가 발생했습니다.');
            } finally {
                couponButton.disabled = false;
            }
        }
        function selectedPaymentMethod() {
            return form.querySelector('input[name="payment_method"]:checked')?.value || 'card';
        }
        function togglePaymentMethod() {
            const isBank = selectedPaymentMethod() === 'bank_transfer';
            bankElements.forEach((element) => { element.style.display = isBank ? 'block' : 'none'; });
            cardElements.forEach((element) => { element.style.display = isBank ? 'none' : 'block'; });
            toggleReceiptArea();
        }
        function toggleReceiptArea() {
            const issue = form.querySelector('input[name="receipt_issue"]:checked')?.value === 'YES';
            if (receiptArea) { receiptArea.style.display = selectedPaymentMethod() === 'bank_transfer' && issue ? 'block' : 'none'; }
            if (window.jQuery) { window.jQuery(window).trigger('scroll.trainingCourseSticky'); }
        }
        function loadTossSdk() {
            if (window.TossPayments) { return Promise.resolve(window.TossPayments); }
            if (tossSdkPromise) { return tossSdkPromise; }
            tossSdkPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://js.tosspayments.com/v2/standard';
                script.async = true;
                script.onload = function () {
                    if (window.TossPayments) { resolve(window.TossPayments); return; }
                    reject(new Error('TossPayments SDK is not available.'));
                };
                script.onerror = function () { reject(new Error('Failed to load TossPayments SDK.')); };
                document.head.appendChild(script);
            });
            return tossSdkPromise;
        }
        async function requestTossPayment() {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                const errors = data.errors || {};
                const firstError = Object.keys(errors).length > 0 ? errors[Object.keys(errors)[0]][0] : null;
                window.alert(firstError || data.message || '입력 내용을 확인해주세요.');
                return;
            }
            const TossPayments = await loadTossSdk();
            const tossPayments = TossPayments(data.clientKey);
            const payment = tossPayments.payment({ customerKey: data.customerKey });
            await payment.requestPayment({
                method: 'CARD',
                amount: { value: Number(data.amount) || 0, currency: 'KRW' },
                orderId: data.orderId,
                orderName: data.orderName,
                customerName: data.customerName,
                customerEmail: data.customerEmail,
                customerMobilePhone: String(data.customerMobilePhone || '').replace(/\D/g, ''),
                successUrl: data.successUrl,
                failUrl: data.failUrl,
            });
        }
        roundInputs.forEach((input) => {
            input.addEventListener('change', function () {
                resetCoupon();
                updateSummary();
            });
        });
        couponButton?.addEventListener('click', applyCoupon);
        form.querySelectorAll('input[name="payment_method"]').forEach((input) => { input.addEventListener('change', togglePaymentMethod); });
        form.querySelectorAll('input[name="receipt_issue"]').forEach((input) => { input.addEventListener('change', toggleReceiptArea); });
        termsCheckbox?.addEventListener('change', updateSubmitButton);
        form.addEventListener('submit', async function (event) {
            if (selectedRounds().length === 0) {
                event.preventDefault();
                window.alert('결제 항목을 선택해주세요.');
                return;
            }
            if (termsCheckbox && !termsCheckbox.checked) {
                event.preventDefault();
                window.alert('결제 이용 약관 및 개인정보 처리 동의에 체크해주세요.');
                return;
            }
            if (selectedPaymentMethod() === 'bank_transfer' || currentTotal() <= 0) { return; }
            event.preventDefault();
            if (submitButton) { submitButton.disabled = true; }
            try {
                await requestTossPayment();
            } catch (error) {
                window.alert('토스페이먼츠 결제창을 여는 중 오류가 발생했습니다.');
            } finally {
                if (submitButton) { submitButton.disabled = false; }
            }
        });
        togglePaymentMethod();
        toggleReceiptArea();
        updateSummary();
    }
    document.addEventListener('DOMContentLoaded', function () {
        initTabs();
        initListFilters();
        initStickySummary();
        initPaymentForm();
    });
})();