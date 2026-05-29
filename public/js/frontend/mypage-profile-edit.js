/**
 * 마이페이지 개인정보: 인증의 게이지, 중복확인, 팝업, 휴대폰 하이픈
 */
(function () {
    'use strict';

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function formatPhoneKoreaDisplay(raw) {
        var d = String(raw || '').replace(/\D/g, '').slice(0, 11);
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
            return d.slice(0, 3) + '-' + d.slice(3, 6) + '-' + d.slice(6);
        }
        return d;
    }

    function initProgressBar() {
		document.querySelectorAll('.participation_area .box').forEach(function(box) {
			var countEl = box.querySelector('.count');
			if (!countEl) return;

			var nums = countEl.textContent.match(/\d+/g);
			if (!nums || nums.length < 2) return;

			var current = parseInt(nums[0], 10);
			var total   = parseInt(nums[1], 10);
			var barEl   = box.querySelector('.state_line .bar');

			if (barEl && total > 0) {
				barEl.style.width = (current / total) * 100 + '%';
			}
		});
	}

    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        }).then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, data: data };
            });
        });
    }

    function initDuplicateChecks(root) {
        var emailBtn = root.querySelector('[data-check-email]');
        var phoneBtn = root.querySelector('[data-check-phone]');
        var licenseBtn = root.querySelector('[data-check-license]');

        if (emailBtn) {
            emailBtn.addEventListener('click', function () {
                var input = root.querySelector('#register-email');
                var email = input ? input.value.trim() : '';
                if (!email) {
                    window.alert('이메일을 입력해주세요.');
                    return;
                }
                postJson(emailBtn.getAttribute('data-check-url'), { email: email }).then(function (r) {
                    window.alert(r.data.message || (r.ok ? '사용 가능합니다.' : '확인에 실패했습니다.'));
                });
            });
        }

        if (phoneBtn) {
            phoneBtn.addEventListener('click', function () {
                var input = root.querySelector('#register-phone');
                var phone = input ? input.value.replace(/\D/g, '') : '';
                if (!phone) {
                    window.alert('휴대폰 번호를 입력해주세요.');
                    return;
                }
                postJson(phoneBtn.getAttribute('data-check-url'), { phone_number: phone }).then(function (r) {
                    window.alert(r.data.message || (r.ok ? '사용 가능합니다.' : '확인에 실패했습니다.'));
                });
            });
        }

        if (licenseBtn) {
            licenseBtn.addEventListener('click', function () {
                var input = root.querySelector('#register-doctor-num');
                var license = input ? input.value.trim() : '';
                if (!license) {
                    window.alert('의사면허번호를 입력해주세요.');
                    return;
                }
                postJson(licenseBtn.getAttribute('data-check-url'), { license_number: license }).then(function (r) {
                    window.alert(r.data.message || (r.ok ? '사용 가능합니다.' : '확인에 실패했습니다.'));
                });
            });
        }
    }

    function initPhoneInput(root) {
        var phoneInput = root.querySelector('#register-phone');
        if (!phoneInput) {
            return;
        }
        phoneInput.setAttribute('inputmode', 'numeric');
        phoneInput.addEventListener('input', function () {
            phoneInput.value = formatPhoneKoreaDisplay(phoneInput.value);
        });
    }

    function initWorkplaceAddressSearch() {
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

    function initLayerTriggers() {
        document.querySelectorAll('[data-layer-open]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-layer-open');
                if (id && typeof window.layerShow === 'function') {
                    window.layerShow(id);
                }
            });
        });
        document.querySelectorAll('[data-layer-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-layer-close');
                if (id && typeof window.layerHide === 'function') {
                    window.layerHide(id);
                }
            });
        });
        document.querySelectorAll('.popup .dm[data-layer-close]').forEach(function (dm) {
            dm.addEventListener('click', function () {
                var id = dm.getAttribute('data-layer-close');
                if (id && typeof window.layerHide === 'function') {
                    window.layerHide(id);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('[data-mypage-profile-edit]');
        if (!root) {
            return;
        }
        initProgressBar();
        initDuplicateChecks(root);
        initPhoneInput(root);
        initLayerTriggers();
        initWorkplaceAddressSearch();
    });
})();
