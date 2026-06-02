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

    /**
     * 직장 전화 포매팅 (02-123-4567, 031-1234-5678, 010-1234-5678)
     */
	function formatWorkplacePhoneDisplay(raw) {
		var d = String(raw || '')
			.replace(/\D/g, '')
			.slice(0, 11);
		if (!d.length) {
			return '';
		}
		if (d[0] === '0') {
			// 휴대폰 번호 (010, 011 등) → 3-4-4
			if (/^01/.test(d)) {
				if (d.length <= 3) return d;
				if (d.length <= 7) return d.slice(0, 3) + '-' + d.slice(3);
				return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
			}
			// 02 (서울) → 2-3-4
			if (d.startsWith('02')) {
				if (d.length <= 2) return d;
				if (d.length <= 5) return d.slice(0, 2) + '-' + d.slice(2);
				return d.slice(0, 2) + '-' + d.slice(2, 5) + '-' + d.slice(5);
			}
			// 031 등 지역번호 → 3-3-4
			if (d.length <= 3) return d;
			if (d.length <= 6) return d.slice(0, 3) + '-' + d.slice(3);
			return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
		}
		if (d.length <= 3) return d;
		if (d.length <= 7) return d.slice(0, 3) + '-' + d.slice(3);
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

    function bindWorkplacePhoneInput() {
        var input = document.querySelector('input.js-register-workplace-phone-input[name="workplace_phone"]');
        if (!input) {
            return;
        }
        var apply = function () {
            var formatted = formatWorkplacePhoneDisplay(input.value);
            input.value = formatted;
            var end = formatted.length;
            input.setSelectionRange(end, end);
        };
        if (input.value) {
            input.value = formatWorkplacePhoneDisplay(input.value);
        }
        input.addEventListener('input', apply);
        input.addEventListener('blur', function () {
            input.value = formatWorkplacePhoneDisplay(input.value);
        });
        input.addEventListener('paste', function () {
            window.requestAnimationFrame(apply);
        });
    }

    function bindLoginIdInput() {
        var input = document.querySelector('input[name="login_id"]');
        if (!input) {
            return;
        }
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-z0-9]/g, '').slice(0, 12);
        });
        input.addEventListener('blur', function () {
            var val = this.value;
            if (val && (val.length < 4 || val.length > 12)) {
                window.alert('아이디는 4~12자의 영문 소문자와 숫자만 사용할 수 있습니다.');
                this.focus();
            }
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
                    if (zip) { zip.value = data.zonecode; }
                    if (base) { base.value = data.address; }
                    if (detail) { detail.focus(); }
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

    function validateRegisterForm() {
        var checks = [
            {
                el: document.querySelector('input[name="login_id"]'),
                test: function (v) { return !!v; },
                msg: '아이디를 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="login_id"]'),
                test: function (v) { return v.length >= 4 && v.length <= 12; },
                msg: '아이디는 4~12자여야 합니다.',
            },
            {
                el: document.querySelector('input[name="login_id"]'),
                test: function (v) { return /^[a-z0-9]+$/.test(v); },
                msg: '아이디는 영문 소문자와 숫자만 사용할 수 있습니다.',
            },
            {
                el: document.querySelector('input[name="password"]'),
                test: function (v) { return !!v; },
                msg: '비밀번호를 입력해주세요.',
            },
			{
				el: document.querySelector('input[name="password"]'),
				test: function (v) { return /^(?=.*[a-zA-Z])(?=.*\d).{8,10}$/.test(v); },
				msg: '비밀번호는 8~10자이며 영문과 숫자를 포함해야 합니다.',
			},
            {
                el: document.querySelector('input[name="password_confirmation"]'),
                test: function (v) {
                    var pw = document.querySelector('input[name="password"]');
                    return pw && v === pw.value;
                },
                msg: '비밀번호가 일치하지 않습니다.',
            },
            {
                el: document.querySelector('input[name="name"]'),
                test: function (v) { return !!v; },
                msg: '한글 이름을 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="name_en"]'),
                test: function (v) { return !!v; },
                msg: '영문 이름을 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="phone_number"]'),
                test: function (v) { return !!v; },
                msg: '휴대폰 번호를 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="phone_number"]'),
                test: function (v) { return v.replace(/\D/g, '').length >= 10; },
                msg: '휴대폰 번호를 정확히 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="email"]'),
                test: function (v) { return !!v; },
                msg: '이메일을 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="email"]'),
                test: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); },
                msg: '유효한 이메일 주소를 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="license_number"]'),
                test: function (v) { return !!v; },
                msg: '의사면허번호를 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="workplace_name"]'),
                test: function (v) { return !!v; },
                msg: '직장명을 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="workplace_phone"]'),
                test: function (v) { return !!v; },
                msg: '직장 전화를 입력해주세요.',
            },
            {
                el: document.querySelector('input[name="privacy_agreed"]'),
                test: function (v, el) { return el.checked; },
                msg: '개인정보 수집 및 이용에 동의해주세요.',
            },
            {
                el: document.querySelector('input[name="terms_agreed"]'),
                test: function (v, el) { return el.checked; },
                msg: '이용약관에 동의해주세요.',
            },
        ];

        for (var i = 0; i < checks.length; i++) {
            var c = checks[i];
            if (!c.el) { continue; }
            var val = c.el.value || '';
            if (!c.test(val, c.el)) {
                window.alert(c.msg);
                c.el.focus();
                return false;
            }
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('member-register-page');
        if (!root) {
            return;
        }

        bindRegisterPhoneInput();
        bindWorkplacePhoneInput();
        bindLoginIdInput();
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

        var form = document.querySelector('.register_form');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!validateRegisterForm()) {
                    e.preventDefault();
                }
            });
        }
    });
})();