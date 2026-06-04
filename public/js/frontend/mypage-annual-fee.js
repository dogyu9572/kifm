(function () {
    'use strict';

    // 1. 세자리 콤마 포맷 함수
    function numberFormat(value) {
        return new Intl.NumberFormat('ko-KR').format(Math.max(0, Number(value) || 0));
    }

    // 2. 모바일/PC 스티키 레이아웃 및 터치 드래그 제어
    function initStickySummary() {
        if (typeof window.jQuery === 'undefined') { return; }

        var $ = window.jQuery;
        var $window = $(window);
        var $detail = $('.training_course_view_wrap'); 
        var $absoApp = $('.abso_application');
        var $inbox = $detail.find('.inbox');

        if (!$detail.length || !$absoApp.length) { return; }

        var touchStartY = 0;
        var startBottom = 0;
        var isDragging = false;
        var isUserOpened = false; 

        function handleStickyLayout() {
            var scrollTop = $window.scrollTop();
            var windowHeight = $window.height();
            var windowWidth = $window.width();
            var detailOffsetTop = $detail.offset().top;
            var detailHeight = $detail.outerHeight();
            var appHeight = $absoApp.outerHeight();

            if (windowWidth <= 767) {
                $inbox.css('padding-bottom', (appHeight + 20) + 'px');
                var mobileUnfixPoint = (detailOffsetTop + detailHeight) - windowHeight;
                var closedBottom = 'calc(-100% + (100vh - ' + appHeight + 'px) + 33px)';
                
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
                            $absoApp.removeClass('open').css('bottom', closedBottom);
                        }
                    }
                }
            } else {
                $inbox.css('padding-bottom', '');
                $absoApp.removeClass('open').css({ 'bottom': '', 'transition': '' });
                
                var fixStartPoint = detailOffsetTop - 190;
                var unfixPoint = detailOffsetTop + detailHeight - appHeight - 190;

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
            var touch = e.originalEvent.touches[0];
            touchStartY = touch.clientY;
            isDragging = true;
            $absoApp.css('transition', 'none');
            var windowHeight = window.innerHeight;
            var appRect = $absoApp[0].getBoundingClientRect();
            startBottom = windowHeight - appRect.bottom;
        });

        $absoApp.on('touchmove.stickyApp', function (e) {
            if (!isDragging || $window.width() > 767 || $detail.hasClass('unfixed')) { return; }
            var touch = e.originalEvent.touches[0];
            var diffY = touchStartY - touch.clientY;
            if (Math.abs(diffY) > 5) { if (e.cancelable) { e.preventDefault(); } }
            var currentBottom = startBottom + diffY;
            if (currentBottom > 0) { currentBottom = 0; }
            $absoApp.css('bottom', currentBottom + 'px');
        });

        $absoApp.on('touchend.stickyApp touchcancel.stickyApp', function (e) {
            if (!isDragging || $window.width() > 767) { return; }
            isDragging = false;
            var touch = e.originalEvent.changedTouches[0];
            var diffY = touchStartY - touch.clientY;
            var appHeight = $absoApp.outerHeight();
            var isUnfixed = $detail.hasClass('unfixed');

            $absoApp.css('transition', 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)');
            var closedBottom = isUnfixed ? '-' + (appHeight - 33) + 'px' : 'calc(-100% + (100vh - ' + appHeight + 'px) + 33px)';

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

        $window.on('scroll.stickyApp resize.stickyApp load.stickyApp', handleStickyLayout);
        handleStickyLayout();
    }
    function initPaymentForm() {
        var container = document.querySelector('.abso_application');
        if (!container) { return; }

        var termsCheckbox = document.getElementById('terms_agree'); 
        var submitButton = container.querySelector('button[type="submit"]');
        var totalAmount = 250000; 
        var paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        var cashReceiptRadios = document.querySelectorAll('input[name="cash_receipt"]');
        var bankElements = document.querySelectorAll('.type_bank_hide');
        var cardElements = document.querySelectorAll('.type_card');
        var cashReceiptArea = document.querySelector('.cash_receipt_area');
        function handlePaymentLayoutToggle() {
            var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            var isBank = selectedPayment && selectedPayment.value === 'bank_transfer';
            bankElements.forEach(function (el) {
                el.style.display = isBank ? 'block' : 'none';
            });
            cardElements.forEach(function (el) {
                el.style.display = isBank ? 'none' : 'block';
            });
            if (isBank) {
                var selectedReceipt = document.querySelector('input[name="cash_receipt"]:checked');
                var isReceiptApply = selectedReceipt && selectedReceipt.value === '발행';
                
                if (cashReceiptArea) {
                    cashReceiptArea.style.display = isReceiptApply ? 'block' : 'none';
                }
            } else {
                if (cashReceiptArea) { cashReceiptArea.style.display = 'none'; }
            }
            if (typeof window.jQuery !== 'undefined') {
                window.jQuery(window).trigger('scroll.stickyApp');
            }
        }
        function updateSubmitButton() {
            if (!submitButton || !termsCheckbox) { return; }
            var formattedPrice = numberFormat(totalAmount) + '원 ';
            if (termsCheckbox.checked) {
                submitButton.className = 'btn_submit btn_wbb';
                submitButton.innerHTML = '<span class="sound_only" id="training-submit-amount">' + formattedPrice + '</span>결제하기';
            } else {
                submitButton.className = 'btn_submit btn_wgg';
                submitButton.innerHTML = '<span class="sound_only" id="training-submit-amount">' + formattedPrice + '</span>결제 약관에 동의해주세요.';
            }
        }
        if (termsCheckbox) {
            termsCheckbox.addEventListener('change', updateSubmitButton);
        }
        paymentRadios.forEach(function (radio) {
            radio.addEventListener('change', handlePaymentLayoutToggle);
        });
        cashReceiptRadios.forEach(function (radio) {
            radio.addEventListener('change', handlePaymentLayoutToggle);
        });
        if (submitButton) {
            submitButton.addEventListener('click', function (event) {
                var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
                if (selectedPayment && selectedPayment.value === 'bank_transfer') {
                    var nameInput = document.getElementById('name'); // 입금자명
                    var dateInput = document.getElementById('date'); // 입금예정일

                    if (nameInput && !nameInput.value.trim()) {
                        event.preventDefault();
                        window.alert('입금자명을 입력해 주세요.');
                        nameInput.focus();
                        return;
                    }
                    if (dateInput && !dateInput.value.trim()) {
                        event.preventDefault();
                        window.alert('입금 예정일을 입력해 주세요.');
                        dateInput.focus();
                        return;
                    }
                    var selectedReceipt = document.querySelector('input[name="cash_receipt"]:checked');
                    if (selectedReceipt && selectedReceipt.value === '발행' && cashReceiptArea) {
                        var requiredInputs = cashReceiptArea.querySelectorAll('input[required]');
                        for (var i = 0; i < requiredInputs.length; i++) {
                            if (!requiredInputs[i].value.trim()) {
                                event.preventDefault();
                                var labelText = requiredInputs[i].closest('li').querySelector('label').textContent.replace('*', '').trim();
                                window.alert(labelText + ' 항목을 입력해 주세요.');
                                requiredInputs[i].focus();
                                return;
                            }
                        }
                    }
                }
                if (termsCheckbox && !termsCheckbox.checked) {
                    event.preventDefault();
                    window.alert('결제 이용 약관 및 개인정보 처리 동의에 체크해주세요.');
                    termsCheckbox.focus();
                }
            });
        }
        handlePaymentLayoutToggle();
        updateSubmitButton();
    }

    // 4. 뒤로가기 버튼 기능 제어
    function initHistoryBack() {
        document.querySelectorAll('[data-history-back]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (window.history.length > 1) {
                    window.history.back();
                    return;
                }
                window.location.href = '/mypage/profile_edit';
            });
        });
    }

    // DOM 로드 완료 후 순서대로 기동
    document.addEventListener('DOMContentLoaded', function () {
        initStickySummary();
        initPaymentForm();
        initHistoryBack();
    });
})();