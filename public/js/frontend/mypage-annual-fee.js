(function () {
    'use strict';

    function initStickyApplication() {
        if (typeof window.jQuery === 'undefined') {
            return;
        }

        var $ = window.jQuery;
        var $window = $(window);
        var $detail = $('.academic_event_view_detail');
        var $absoApp = $('.abso_application');
        var $inbox = $detail.find('.inbox');

        if (!$detail.length || !$absoApp.length) {
            return;
        }

        var touchStartY = 0;
        var startBottom = 0;
        var isDragging = false;
        // ★ 중요: 처음 진입 시 0px이 되지 않고 접힌 상태로 시작하도록 false로 변경
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
                        // 콘텐츠 맨 아래에 도달했을 때는 자동으로 펼쳐짐
                        $absoApp.addClass('open').css('bottom', '0px');
                    }
                } else {
                    $detail.removeClass('unfixed').addClass('fixed');
                    if (!isDragging) {
                        // 사용자가 직접 열기 전(isUserOpened === false)에는 처음부터 closedBottom 유지
                        if (isUserOpened) {
                            $absoApp.addClass('open').css('bottom', '0px');
                        } else {
                            $absoApp.removeClass('open').css('bottom', closedBottom);
                        }
                    }
                }
            } 
            else {
                // PC 환경 초기화
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

        // 터치 시작
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

        // 터치 이동 (끌어당기는 중)
        $absoApp.on('touchmove.stickyApp', function (e) {
            if (!isDragging || $window.width() > 767 || $detail.hasClass('unfixed')) { return; }
            var touch = e.originalEvent.touches[0];
            var diffY = touchStartY - touch.clientY;
            if (Math.abs(diffY) > 5) {
                if (e.cancelable) { e.preventDefault(); }
            }
            var currentBottom = startBottom + diffY;
            if (currentBottom > 0) { currentBottom = 0; }
            $absoApp.css('bottom', currentBottom + 'px');
        });

        // 터치 종료 (손을 뗐을 때 튕김 처리)
        $absoApp.on('touchend.stickyApp touchcancel.stickyApp', function (e) {
            if (!isDragging || $window.width() > 767) { return; }
            isDragging = false;
            var touch = e.originalEvent.changedTouches[0];
            var diffY = touchStartY - touch.clientY;
            var appHeight = $absoApp.outerHeight();
            var isUnfixed = $detail.hasClass('unfixed');

            $absoApp.css('transition', 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)');
            var closedBottom = isUnfixed 
                ? '-' + (appHeight - 33) + 'px' 
                : 'calc(-100% + (100vh - ' + appHeight + 'px) + 33px)';

            // 위로 30px 이상 당기면 오픈(0px)
            if (diffY > 30) {
                $absoApp.addClass('open').css('bottom', '0px');
                isUserOpened = true;
            } 
            // 아래로 30px 이상 내리면 닫힘(closedBottom)
            else if (diffY < -30) {
                $absoApp.removeClass('open').css('bottom', closedBottom);
                isUserOpened = false;
            } 
            // 미세하게 움직인 경우 기존 상태 유지
            else {
                if ($absoApp.hasClass('open')) {
                    $absoApp.css('bottom', '0px');
                    isUserOpened = true;
                } else {
                    $absoApp.css('bottom', closedBottom);
                    isUserOpened = false;
                }
            }
        });

        // 이미지 로드 등으로 높이가 바뀔 수 있으므로 load 이벤트 추가 바인딩
        $window.on('scroll.stickyApp resize.stickyApp load.stickyApp', handleStickyLayout);
        
        // DOM 로드 직후 바로 실행하여 닫힌 상태 포지션 잡기
        handleStickyLayout();
    }

    function initPaymentMethodToggle() {
        var paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        var cashRadios = document.querySelectorAll('input[name="cash_receipt"]');
        var bankElements = document.querySelectorAll('.type_bank_hide');
        var cardElements = document.querySelectorAll('.type_card');
        var cashArea = document.querySelector('.cash_receipt_area');
        var bankRadio = document.getElementById('payment_type_bank');
        var cashReceiptRadio = document.getElementById('cash_receipt');
        var cashReceiptNoneRadio = document.getElementById('cash_receipt_non');

        if (!paymentRadios.length || !bankRadio) {
            return;
        }

        function refreshSticky() {
            if (typeof window.jQuery !== 'undefined') {
                window.jQuery(window).trigger('scroll.stickyApp');
            }
        }

        function handleCashReceiptChange() {
            if (!cashArea || !cashReceiptRadio) {
                return;
            }

            cashArea.style.display = cashReceiptRadio.checked ? 'block' : 'none';
            refreshSticky();
        }

        function handlePaymentChange() {
            var isBank = bankRadio.checked;

            bankElements.forEach(function (el) {
                el.style.display = isBank ? 'block' : 'none';
            });
            cardElements.forEach(function (el) {
                el.style.display = isBank ? 'none' : 'block';
            });

            if (!isBank && cashReceiptNoneRadio) {
                cashReceiptNoneRadio.checked = true;
                handleCashReceiptChange();
            }

            refreshSticky();
        }

        paymentRadios.forEach(function (radio) {
            radio.addEventListener('change', handlePaymentChange);
        });
        cashRadios.forEach(function (radio) {
            radio.addEventListener('change', handleCashReceiptChange);
        });

        handlePaymentChange();
        handleCashReceiptChange();
    }

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

    document.addEventListener('DOMContentLoaded', function () {
        initStickyApplication();
        initPaymentMethodToggle();
        initHistoryBack();
    });
})();