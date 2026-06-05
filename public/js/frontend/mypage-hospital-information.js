/**
 * 마이페이지 병원 정보: 시도·시군구 연동, 주소 검색, 사진 미리보기
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('mypage-hospital-information-form');
    if (!form) {
        return;
    }

    initSigunguCascade(form);
    initPhotoPreview(form);
    initAddressSearch(form);
});

function initSigunguCascade(form) {
    var sidoSelect = form.querySelector('#mypage-sido');
    var sigunguSelect = form.querySelector('#mypage-sigungu');
    var sigunguMap = {};

    try {
        sigunguMap = JSON.parse(form.dataset.sigunguJson || '{}');
    } catch (error) {
        sigunguMap = {};
    }

    function rebuildSigungu(resetSelection) {
        if (!sigunguSelect) {
            return;
        }
        var sido = (sidoSelect ? sidoSelect.value : '').trim();
        var list = sido && Array.isArray(sigunguMap[sido]) ? sigunguMap[sido] : [];
        if (sido && list.length === 0) {
            var hit = Object.keys(sigunguMap).find(function (key) {
                return key === sido;
            });
            list = hit && Array.isArray(sigunguMap[hit]) ? sigunguMap[hit] : [];
        }
        var previous = resetSelection ? '' : sigunguSelect.value;
        sigunguSelect.innerHTML = '<option value="">선택</option>';
        list.forEach(function (name) {
            var opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            if (!resetSelection && previous === name) {
                opt.selected = true;
            }
            sigunguSelect.appendChild(opt);
        });
        if (!resetSelection && previous && list.indexOf(previous) === -1) {
            var optExtra = document.createElement('option');
            optExtra.value = previous;
            optExtra.textContent = previous;
            optExtra.selected = true;
            sigunguSelect.appendChild(optExtra);
        }
    }

    if (sidoSelect) {
        sidoSelect.addEventListener('change', function () {
            rebuildSigungu(true);
        });
    }
    rebuildSigungu(false);
}

function initPhotoPreview(form) {
    var fileInput = form.querySelector('#doctor_photo_file');
    var preview = form.querySelector('#mypage-doctor-photo-preview');
    var label = form.querySelector('label[for="doctor_photo_file"]');

    if (label) {
        label.addEventListener('click', function (event) {
            if (event.target === fileInput) {
                return;
            }
            event.preventDefault();
            if (fileInput) {
                fileInput.click();
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var file = fileInput.files && fileInput.files.length > 0 ? fileInput.files[0] : null;
            if (!file || !preview) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target && event.target.result ? event.target.result : preview.src;
            };
            reader.readAsDataURL(file);
        });
    }
}

function initAddressSearch(form) {
    var searchBtn = form.querySelector('#mypage-hospital-address-search');
    var addressInput = form.querySelector('#address');

    if (!searchBtn) {
        return;
    }

    searchBtn.addEventListener('click', function () {
        if (typeof daum === 'undefined' || !daum.Postcode) {
            window.alert('주소 검색 서비스를 불러오지 못했습니다.');
            return;
        }
        new daum.Postcode({
            oncomplete: function (data) {
                if (addressInput) {
                    addressInput.value = data.roadAddress || data.jibunAddress || '';
                }
                var addressDetail = form.querySelector('#address_detail');
                if (addressDetail) {
                    addressDetail.focus();
                }
            },
        }).open();
    });
}
