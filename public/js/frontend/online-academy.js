(function () {
    function initSlides() {
        if (typeof Swiper === 'undefined' || !document.querySelector('.online_academy_slide_wrap')) {
            return;
        }

        const academyImgSlide = new Swiper('.slide_img', {
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 800,
        });

        const academyTxtSlide = new Swiper('.slide_txt', {
            loop: true,
            speed: 800,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.online_academy_head .next',
                prevEl: '.online_academy_head .prev',
            },
            on: {
                slideChangeTransitionStart: function () {
                    $('.online_academy_head .line span').stop().css('width', '0');
                },
                slideChangeTransitionEnd: function () {
                    $('.online_academy_head .line span').animate({ width: '100%' }, 5000, 'linear');
                },
                init: function () {
                    $('.online_academy_head .line span').animate({ width: '100%' }, 5000, 'linear');
                },
            },
        });

        academyTxtSlide.controller.control = academyImgSlide;
    }

    function initKeywordFilter() {
        const form = document.getElementById('online-academy-filter-form');
        const customBox = document.querySelector('[data-keyword-select]');
        if (!form || !customBox) {
            return;
        }

        const maxSelect = 5;
        const selectPanel = customBox.querySelector('.select_check');
        const userSelect = customBox.querySelector('.user_select');
        const hiddenWrap = customBox.querySelector('.js-keyword-hidden-inputs');

        customBox.querySelector('.select_type')?.addEventListener('click', function () {
            $(selectPanel).stop().slideToggle(300);
        });

        customBox.querySelectorAll('.select_list button').forEach((button) => {
            button.addEventListener('click', function () {
                const isTotal = button.dataset.keywordValue === '전체';
                const isOn = button.classList.contains('on');

                if (isTotal) {
                    customBox.querySelectorAll('.select_list button').forEach((other) => {
                        if (other !== button) {
                            other.classList.remove('on');
                        }
                    });
                    button.classList.toggle('on');
                    return;
                }

                customBox.querySelectorAll('[data-keyword-value="전체"]').forEach((totalButton) => {
                    totalButton.classList.remove('on');
                });

                const checkedCount = customBox.querySelectorAll('.select_list button.on:not([data-keyword-value="전체"])').length;
                if (!isOn && checkedCount >= maxSelect) {
                    window.alert('최대 ' + maxSelect + '개까지 선택 가능합니다.');
                    return;
                }
                button.classList.toggle('on');
            });
        });

        customBox.querySelector('.select_check .btn_reset')?.addEventListener('click', function () {
            customBox.querySelectorAll('.select_list button').forEach((button) => button.classList.remove('on'));
        });

        customBox.querySelector('.select_check .btn_check')?.addEventListener('click', function () {
            const selected = Array.from(customBox.querySelectorAll('.select_list button.on'))
                .map((button) => button.dataset.keywordValue || button.textContent.trim())
                .filter((value) => value && value !== '전체');

            userSelect.innerHTML = '';
            hiddenWrap.innerHTML = '';

            selected.forEach((value) => {
                const li = document.createElement('li');
                li.textContent = value;
                userSelect.appendChild(li);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'keywords[]';
                input.value = value;
                hiddenWrap.appendChild(input);
            });

            customBox.classList.toggle('on', selected.length > 0);
            $(selectPanel).stop().slideUp(300);
            form.submit();
        });
    }

    function initListAnchorFocus() {
        const listSection = document.querySelector('.online_academy_list');
        if (!listSection) {
            return;
        }
        const form = document.getElementById('online-academy-filter-form');
        const courseTypeInput = form?.querySelector('[data-course-type-input]');

        document.querySelectorAll('[data-online-academy-tabs] a').forEach((link) => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const url = link.getAttribute('href');
                const courseType = link.dataset.courseType || '';
                link.closest('li')?.classList.add('on');
                link.closest('li')?.parentElement?.querySelectorAll('li').forEach((li) => {
                    if (li !== link.closest('li')) {
                        li.classList.remove('on');
                    }
                });
                if (courseTypeInput) {
                    courseTypeInput.value = courseType;
                }

                const headerHeight = $('.header').outerHeight() || 0;
                const targetPos = $(listSection).offset().top - headerHeight;
                $('html, body').stop().animate({
                    scrollTop: Math.max(0, targetPos),
                }, 500);

                if (url) {
                    loadCourseList(url);
                }
            });
        });

        const hasFilter = new URLSearchParams(window.location.search).toString() !== '';
        if (hasFilter) {
            window.requestAnimationFrame(function () {
                const headerHeight = $('.header').outerHeight() || 0;
                const targetPos = $(listSection).offset().top - headerHeight;
                window.scrollTo(0, Math.max(0, targetPos));
            });
        }
    }

    function loadCourseList(url) {
        const list = document.querySelector('.gallery_academy');
        const pagination = document.querySelector('.board-pagination');
        if (!list) {
            window.location.href = url;
            return;
        }

        list.classList.add('is-loading');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to load course list.');
                }
                return response.text();
            })
            .then((html) => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextList = doc.querySelector('.gallery_academy');
                const nextPagination = doc.querySelector('.board-pagination');
                if (!nextList) {
                    throw new Error('Course list not found.');
                }

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
            .catch(() => {
                window.location.href = url;
            })
            .finally(() => {
                document.querySelector('.gallery_academy')?.classList.remove('is-loading');
            });
    }

    function initStateBars() {
        document.querySelectorAll('.state_line').forEach((line) => {
            const percentText = line.querySelector('.left strong')?.textContent || '';
            const percentValue = percentText.replace(/[^0-9]/g, '') || '0';
            const bar = line.querySelector('.bar');
            if (bar) {
                bar.style.width = percentValue + '%';
            }
        });

        document.querySelectorAll('.test_state_line').forEach((line) => {
            const stepText = line.querySelector('.step')?.textContent || '';
            const parts = stepText.split('/');
            const currentStep = parseFloat(parts[0]);
            const totalSteps = parseFloat(parts[1]);
            const bar = line.querySelector('.bar');
            if (bar && totalSteps > 0) {
                bar.style.width = (currentStep / totalSteps) * 100 + '%';
            }
        });
    }

    function initTestButton() {
        const percentText = document.querySelector('.state_line .left strong');
        const testButton = document.querySelector('.btn_test');
        const buttonArea = document.querySelector('.btn_area');
        const container = document.querySelector('.online_academy_view');
        const textBox = document.querySelector('.txtbox');
        if (!percentText || !testButton || !buttonArea || !container) {
            return;
        }

        if (percentText.textContent.replace(/\s/g, '') === '100%') {
            container.classList.add('percent100');
            buttonArea.classList.add('end');
            textBox?.removeAttribute('aria-hidden');
            testButton.classList.remove('disabled');
            testButton.setAttribute('aria-disabled', 'false');
        }

        testButton.addEventListener('click', function (event) {
            if (testButton.getAttribute('aria-disabled') === 'true') {
                event.preventDefault();
            }
        });
    }

    function initTestEnd() {
        const end = document.querySelector('.test_end');
        if (!end) {
            return;
        }
        const score = parseInt((end.querySelector('.gbox h2')?.textContent || '').replace(/[^0-9]/g, ''), 10);
        if (score >= 100) {
            end.classList.add('pass');
            end.closest('.test_page')?.querySelectorAll('.btn_kwg, .btn_wkk').forEach((button) => button.remove());
        } else {
            end.classList.add('fail');
            end.closest('.test_page')?.querySelectorAll('.btn_woo2').forEach((button) => button.remove());
            const title = end.querySelector('.tit');
            const message = end.querySelector('.gbox p');
            if (title) {
                title.innerHTML = '아쉽지만 <strong class="c_red">불합격</strong> 하셨습니다.';
            }
            if (message) {
                message.textContent = '다시 한번 도전해 보세요!';
            }
        }
    }

    function initHistoryBack() {
        document.querySelectorAll('[data-history-back]').forEach((button) => {
            button.addEventListener('click', function () {
                window.history.back();
            });
        });
    }

    function initPaymentStickyBox() {
        const detail = document.querySelector('.academic_event_view_detail');
        const app = document.querySelector('.abso_application');
        const header = document.querySelector('header');
        if (!detail || !app) {
            return;
        }

        const onScroll = function () {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const headerHeight = header ? header.offsetHeight : 0;
            const detailTop = detail.getBoundingClientRect().top + scrollTop;
            const detailHeight = detail.offsetHeight;
            const appHeight = app.offsetHeight;
            const fixStartPoint = detailTop - headerHeight - 100;
            const unfixPoint = detailTop + detailHeight - appHeight - 190;

            if (scrollTop >= fixStartPoint) {
                if (scrollTop >= unfixPoint) {
                    detail.classList.add('unfixed');
                    detail.classList.remove('fixed');
                } else {
                    detail.classList.add('fixed');
                    detail.classList.remove('unfixed');
                }
            } else {
                detail.classList.remove('fixed', 'unfixed');
            }
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        onScroll();
    }

    function initCheckoutForm() {
        const form = document.getElementById('online-academy-checkout-form');
        if (!form) {
            return;
        }

        const courseInput = form.querySelector('input[name="course_id"]');
        const itemInput = form.querySelector('input[name="course_item"]');
        const couponInput = document.getElementById('coupon_num');
        const couponButton = document.getElementById('online-coupon-apply-btn');
        const couponResult = document.querySelector('#online-coupon-result dd');
        const summarySubtotal = document.getElementById('online-summary-subtotal');
        const summaryDiscount = document.getElementById('online-summary-discount');
        const summaryTotal = document.getElementById('online-summary-total');
        const submitAmount = document.getElementById('online-submit-amount');
        const bankElements = Array.from(document.querySelectorAll('.type_bank_hide'));
        const cardMessages = Array.from(document.querySelectorAll('.type_card'));
        const paymentMethodInput = document.getElementById('online-payment-method');
        const paymentRadios = Array.from(form.querySelectorAll('input[name="payment_method_display"]'));
        const receiptRadios = Array.from(form.querySelectorAll('input[name="receipt_issue"]'));
        const cashReceiptArea = document.querySelector('.cash_receipt_area');
        let appliedDiscount = 0;
        let appliedCouponCode = '';

        function numberFormat(value) {
            return new Intl.NumberFormat('ko-KR').format(Math.max(0, Number(value) || 0));
        }

        function subtotal() {
            return Number(itemInput?.dataset.price) || 0;
        }

        function csrfToken() {
            return form.querySelector('input[name="_token"]')?.value || '';
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
            updateSummary();
        }

        function updateSummary() {
            const currentSubtotal = subtotal();
            const discount = Math.min(appliedDiscount, currentSubtotal);
            const total = Math.max(0, currentSubtotal - discount);

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

        async function applyCoupon() {
            const couponCode = couponInput ? couponInput.value.trim() : '';
            if (!couponCode) {
                window.alert('쿠폰번호를 입력해주세요.');
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
                        course_id: courseInput?.value || '',
                        coupon_code: couponCode,
                    }),
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    resetCoupon();
                    window.alert(data.message || '사용 가능한 쿠폰이 아닙니다.');
                    return;
                }

                appliedCouponCode = couponCode.toUpperCase();
                appliedDiscount = Number(data.discount) || 0;
                if (couponInput) {
                    couponInput.value = appliedCouponCode;
                }
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

        function toggleReceiptArea() {
            const isReceipt = form.querySelector('input[name="receipt_issue"]:checked')?.value === 'YES';
            const isBank = selectedPaymentMethod() === 'bank';
            if (cashReceiptArea) {
                cashReceiptArea.style.display = isBank && isReceipt ? 'block' : 'none';
            }
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

        if (couponButton) {
            couponButton.addEventListener('click', applyCoupon);
        }
        if (couponInput) {
            couponInput.addEventListener('input', function () {
                if (appliedCouponCode) {
                    resetCoupon();
                }
            });
        }
        paymentRadios.forEach((input) => input.addEventListener('change', togglePaymentMethod));
        receiptRadios.forEach((input) => input.addEventListener('change', toggleReceiptArea));
        togglePaymentMethod();
        updateSummary();
    }

    function initOnlineAcademy() {
        initSlides();
        initKeywordFilter();
        initListAnchorFocus();
        initStateBars();
        initTestButton();
        initTestEnd();
        initHistoryBack();
        initPaymentStickyBox();
        initCheckoutForm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnlineAcademy);
    } else {
        initOnlineAcademy();
    }
})();
