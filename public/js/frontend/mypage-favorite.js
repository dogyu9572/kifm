/**
 * 마이페이지 즐겨찾기 메뉴 (DB 저장)
 */
(function () {
    'use strict';

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var wrap = document.querySelector('[data-mypage-favorite]');
        if (!wrap) {
            return;
        }

        var max = parseInt(wrap.getAttribute('data-max-favorites') || '6', 10);
        var saveUrl = wrap.getAttribute('data-save-url');
        var checkboxes = wrap.querySelectorAll('input[name="favorite"]');
        var initial = [];

        checkboxes.forEach(function (cb) {
            if (cb.checked) {
                initial.push(cb.id);
            }
            cb.addEventListener('change', function () {
                var checked = wrap.querySelectorAll('input[name="favorite"]:checked');
                if (checked.length > max) {
                    cb.checked = false;
                    window.alert('즐겨찾는 메뉴는 최대 ' + max + '개까지 저장할 수 있습니다.');
                }
            });
        });

        var resetBtn = wrap.querySelector('.btn_reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                checkboxes.forEach(function (cb) {
                    cb.checked = initial.indexOf(cb.id) !== -1;
                });
            });
        }

        var saveBtn = wrap.querySelector('.btn_save');
        if (saveBtn && saveUrl) {
            saveBtn.addEventListener('click', function () {
                var codes = [];
                wrap.querySelectorAll('input[name="favorite"]:checked').forEach(function (cb) {
                    codes.push(cb.id);
                });
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ menu_codes: codes }),
                })
                    .then(function (res) {
                        return res.json().then(function (data) {
                            return { ok: res.ok, data: data };
                        });
                    })
                    .then(function (r) {
                        if (!r.ok) {
                            window.alert(r.data.message || '저장에 실패했습니다.');
                            return;
                        }
                        initial = codes.slice();
                        window.alert(r.data.message || '저장되었습니다.');
                    });
            });
        }
    });
})();
