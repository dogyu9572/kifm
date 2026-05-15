/**
 * 마이페이지 병원 정보: 시도·시군구 연동, 주소 검색, 사진 미리보기
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('mypage-hospital-information-form');
    if (!form) {
        return;
    }

    initSigunguCascade(form);
    initPhotoPreview(form);
    initAddressSearch(form);
});

function initSigunguCascade(form) {
    const sidoSelect = form.querySelector('#mypage-sido');
    const sigunguSelect = form.querySelector('#mypage-sigungu');
    let sigunguMap = {};

    try {
        sigunguMap = JSON.parse(form.dataset.sigunguJson || '{}');
    } catch {
        sigunguMap = {};
    }

    const rebuildSigungu = (resetSelection) => {
        if (!sigunguSelect) {
            return;
        }
        const sido = (sidoSelect?.value || '').trim();
        let list = sido && Array.isArray(sigunguMap[sido]) ? sigunguMap[sido] : [];
        if (sido && list.length === 0) {
            const hit = Object.keys(sigunguMap).find((key) => key === sido);
            list = hit && Array.isArray(sigunguMap[hit]) ? sigunguMap[hit] : [];
        }
        const previous = resetSelection ? '' : sigunguSelect.value;
        sigunguSelect.innerHTML = '<option value="">선택</option>';
        list.forEach((name) => {
            const opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            if (!resetSelection && previous === name) {
                opt.selected = true;
            }
            sigunguSelect.appendChild(opt);
        });
        if (!resetSelection && previous && !list.includes(previous)) {
            const optExtra = document.createElement('option');
            optExtra.value = previous;
            optExtra.textContent = previous;
            optExtra.selected = true;
            sigunguSelect.appendChild(optExtra);
        }
    };

    sidoSelect?.addEventListener('change', () => rebuildSigungu(true));
    rebuildSigungu(false);
}

function initPhotoPreview(form) {
    const fileInput = form.querySelector('#doctor_photo_file');
    const preview = form.querySelector('#mypage-doctor-photo-preview');
    const label = form.querySelector('label[for="doctor_photo_file"]');

    label?.addEventListener('click', (event) => {
        if (event.target === fileInput) {
            return;
        }
        event.preventDefault();
        fileInput?.click();
    });

    fileInput?.addEventListener('change', () => {
        const file = fileInput.files?.[0];
        if (!file || !preview) {
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target?.result || preview.src;
        };
        reader.readAsDataURL(file);
    });
}

function initAddressSearch(form) {
    const searchBtn = form.querySelector('#mypage-hospital-address-search');
    const addressInput = form.querySelector('#address');

    searchBtn?.addEventListener('click', () => {
        if (typeof daum === 'undefined' || !daum.Postcode) {
            window.alert('주소 검색 서비스를 불러오지 못했습니다.');
            return;
        }
        new daum.Postcode({
            oncomplete(data) {
                if (addressInput) {
                    addressInput.value = data.roadAddress || data.jibunAddress || '';
                }
                form.querySelector('#address_detail')?.focus();
            },
        }).open();
    });
}
