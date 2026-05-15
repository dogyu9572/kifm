/**
 * 회원가입: 위원회 최대 3개, 중복 확인(fetch), 뒤로가기
 */
(function () {
    'use strict';

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * 한국 휴대폰 표시용 하이픈 (저장값은 숫자만 — 서버에서 User::normalizePhone 처리)
     */
    function formatPhoneKoreaDisplay(raw) {
        var d = String(raw || '')
            .replace(/\D/g, '')
            .slice(0, 11);
        if (!d.length) {
            return '';
        }
        if (d.length <= 3) {
            return d;
        }
        // 10자리 완성(011 등 구형) → 3-3-4
        if (d.length === 10 && d[2] !== '0') {
            return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
        }
        // 010·016~019 또는 11자리 입력 중 → 3-4-4
        if (d.startsWith('010') || d.length === 11 || /^01[6789]/.test(d)) {
            if (d.length <= 7) {
                return d.slice(0, 3) + '-' + d.slice(3);
            }
            return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
        }
        // 011 입력 중(10자리 미만) → 3-3-4 형태로 진행
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

    function bindRegisterPhoneInput() {
        var input = document.querySelector('input.js-register-phone-input[name="phone_number"]');
        if (!input) {
            return;
        }
        var apply = function () {
            var formatted = formatPhoneKoreaDisplay(input.value);
            input.value = formatted;
            var end = formatted.length;
            input.setSelectionRange(end, end);
        };
        if (input.value) {
            input.value = formatPhoneKoreaDisplay(input.value);
        }
        input.addEventListener('input', apply);
        input.addEventListener('blur', function () {
            input.value = formatPhoneKoreaDisplay(input.value);
        });
        input.addEventListener('paste', function () {
            window.requestAnimationFrame(apply);
        });
    }

    function bindWorkplaceAddressSearch() {
        var btn = document.querySelector('.js-register-search-workplace-address');
        if (!btn) {
            return;
        }
        btn.addEventListener('click', function () {
            if (typeof daum === 'undefined' || !daum.Postcode) {
                window.alert('주소 검색을 불러오지 못했습니다. 페이지를 새로고침 후 다시 시도해주세요.');
                return;
            }
            new daum.Postcode({
                oncomplete: function (data) {
                    var zip = document.getElementById('register-workplace-zipcode');
                    var base = document.getElementById('register-company-address');
                    var detail = document.getElementById('register-workplace-address-detail');
                    if (zip) {
                        zip.value = data.zonecode;
                    }
                    if (base) {
                        base.value = data.address;
                    }
                    if (detail) {
                        detail.focus();
                    }
                },
            }).open();
        });
    }

    function postForm(url, bodyObj) {
        var body = new URLSearchParams();
        body.append('_token', getCsrfToken());
        Object.keys(bodyObj).forEach(function (key) {
            if (bodyObj[key] !== undefined && bodyObj[key] !== null) {
                body.append(key, bodyObj[key]);
            }
        });
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body.toString(),
            credentials: 'same-origin',
        }).then(function (r) {
            return r.json().then(function (data) {
                if (!r.ok) {
                    var msg = data.message;
                    if (!msg && data.errors) {
                        msg = Object.values(data.errors).flat().join('\n');
                    }
                    throw new Error(msg || '요청에 실패했습니다.');
                }
                return data;
            });
        });
    }

    function bindCommitteeLimit() {
        var checks = document.querySelectorAll('input[name="committee_codes[]"]');
        var max = 3;
        checks.forEach(function (cb) {
            cb.addEventListener('change', function () {
                var n = document.querySelectorAll('input[name="committee_codes[]"]:checked').length;
                if (n > max) {
                    cb.checked = false;
                    window.alert('위원회 참가 신청은 최대 ' + max + '개까지만 선택 가능합니다.');
                }
            });
        });
    }

    function bindDuplicateButton(selector, url, fieldName, getValue) {
        var btn = document.querySelector(selector);
        if (!btn || !url) {
            return;
        }
        btn.addEventListener('click', function () {
            var val = getValue();
            if (!val) {
                window.alert('값을 입력한 뒤 중복 확인을 눌러주세요.');
                return;
            }
            var payload = {};
            payload[fieldName] = val;
            postForm(url, payload)
                .then(function (data) {
                    window.alert(data.message || '');
                })
                .catch(function (e) {
                    window.alert(e.message || '중복 확인 중 오류가 발생했습니다.');
                });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('member-register-page');
        if (!root) {
            return;
        }

        bindRegisterPhoneInput();
        bindWorkplaceAddressSearch();

        bindCommitteeLimit();

        bindDuplicateButton('.js-register-check-login-id', root.dataset.checkLoginId, 'login_id', function () {
            var el = document.querySelector('input[name="login_id"]');
            return el ? el.value.trim() : '';
        });

        bindDuplicateButton('.js-register-check-email', root.dataset.checkEmail, 'email', function () {
            var el = document.querySelector('input[name="email"]');
            return el ? el.value.trim() : '';
        });

        bindDuplicateButton('.js-register-check-phone', root.dataset.checkPhone, 'phone_number', function () {
            var el = document.querySelector('input[name="phone_number"]');
            return el ? el.value.replace(/\D/g, '') : '';
        });

        bindDuplicateButton('.js-register-check-license', root.dataset.checkLicense, 'license_number', function () {
            var el = document.querySelector('input[name="license_number"]');
            return el ? el.value.trim() : '';
        });

        document.querySelectorAll('.js-register-back').forEach(function (btn) {
            btn.addEventListener('click', function () {
                window.history.back();
            });
        });
    });
})();
