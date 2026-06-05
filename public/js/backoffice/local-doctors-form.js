/**
 * 주치의 등록/수정: 시·도→시군구, 다음 주소, 의사 사진(강좌 썸네일과 동일 UI), CKEditor 동기화
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-local-doctor-form');
    if (!form) {
        return;
    }

    let sigunguMap = {};
    try {
        const embed = document.getElementById('bo-local-doctor-sigungu-json');
        const raw = (embed?.value ?? '').trim() || '{}';
        const parsed = JSON.parse(raw);
        if (parsed !== null && typeof parsed === 'object' && !Array.isArray(parsed)) {
            sigunguMap = parsed;
        }
    } catch {
        sigunguMap = {};
    }

    const sidoSelect = form.querySelector('#sido');
    const sigunguSelect = form.querySelector('#sigungu');

    const rebuildSigungu = (resetSelection) => {
        if (!sidoSelect || !sigunguSelect) {
            return;
        }
        const sido = (sidoSelect.value || '').trim();
        let rawList = sido ? sigunguMap[sido] : null;
        if (sido && !Array.isArray(rawList)) {
            const hit = Object.keys(sigunguMap).find((k) => (k || '').trim() === sido);
            rawList = hit ? sigunguMap[hit] : null;
        }
        const list = Array.isArray(rawList) ? rawList : [];
        const previous = resetSelection ? '' : sigunguSelect.value;
        sigunguSelect.innerHTML = '';
        const opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = '선택';
        sigunguSelect.appendChild(opt0);
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

    const onSidoChanged = () => {
        rebuildSigungu(true);
    };
    sidoSelect?.addEventListener('change', onSidoChanged);
    sidoSelect?.addEventListener('input', onSidoChanged);

    rebuildSigungu(false);

    const memberSelector = form.querySelector('.js-member-selector');
    const doctorNameInput = document.getElementById('doctor_name');
    const licenseInput = document.getElementById('license_no');

    memberSelector?.addEventListener('bo-member-selected', (event) => {
        const member = event.detail || {};
        if (doctorNameInput) {
            doctorNameInput.value = member.name || '';
        }
        if (licenseInput) {
            licenseInput.value = member.license_number || '';
        }
    });

    if (typeof window.initBoardImageFilePreview === 'function') {
        window.initBoardImageFilePreview({
            inputId: 'doctor_photo_file',
            previewId: 'doctorPhotoFilePreview',
            removeExistingSelector: '[data-remove-existing-target="doctor_photo"]',
            deleteCheckboxId: 'delete_photo',
            existingItemId: 'bo-doctor-photo-existing-item',
        });
    }

    const addressSearchBtn = document.getElementById('bo-local-doctor-address-search');
    addressSearchBtn?.addEventListener('click', () => {
        if (typeof daum === 'undefined' || !daum.Postcode) {
            window.alert('주소 검색 스크립트를 불러오지 못했습니다.');
            return;
        }
        new daum.Postcode({
            oncomplete(data) {
                const base = document.getElementById('address');
                const detail = document.getElementById('address_detail');
                if (base) {
                    base.value = data.address || '';
                }
                if (detail) {
                    detail.focus();
                }
            },
        }).open();
    });

    form.addEventListener('submit', () => {
        if (typeof window.syncBackofficeCKEditorFields === 'function') {
            window.syncBackofficeCKEditorFields(form);
        }
    });
});
