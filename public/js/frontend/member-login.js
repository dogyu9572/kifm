/**
 * 회원 로그인: 아이디 저장(localStorage), 승인대기/휴면 팝업(layerShow), 팝업 닫기(인라인 제거)
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'member_saved_login_id';

    function bindPopupClose() {
        document.querySelectorAll('.js-login-popup-close').forEach(function (el) {
            el.addEventListener('click', function () {
                var id = el.getAttribute('data-popup');
                if (id && typeof window.layerHide === 'function') {
                    window.layerHide(id);
                }
            });
        });
    }

    function showServerPopup() {
        var root = document.getElementById('member-login-page');
        if (!root) {
            return;
        }
        var kind = root.getAttribute('data-login-popup');
        if (!kind || typeof window.layerShow !== 'function') {
            return;
        }
        if (kind === 'pending') {
            window.layerShow('pop_awaiting');
        } else if (kind === 'dormant') {
            window.layerShow('pop_sleep');
        }
    }

    function bindSaveId() {
        var form = document.getElementById('member-login-form');
        var loginInput = document.querySelector('#member-login-form input[name="login_id"]');
        var saveCb = document.getElementById('save-id');
        if (!form || !loginInput) {
            return;
        }
        try {
            var saved = window.localStorage.getItem(STORAGE_KEY);
            if (saved && saveCb) {
                loginInput.value = saved;
                saveCb.checked = true;
            }
        } catch (e) {
            /* ignore */
        }
        form.addEventListener('submit', function () {
            if (!saveCb) {
                return;
            }
            try {
                if (saveCb.checked && loginInput.value.trim()) {
                    window.localStorage.setItem(STORAGE_KEY, loginInput.value.trim());
                } else {
                    window.localStorage.removeItem(STORAGE_KEY);
                }
            } catch (err) {
                /* ignore */
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindPopupClose();
        bindSaveId();
        showServerPopup();
    });
})();
