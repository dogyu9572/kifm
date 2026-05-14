/**
 * 숙박 등록/수정: 다음 주소 검색, 임원진과 동일한 이미지 업로드, CKEditor 동기화
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-hotel-form');
    if (!form) {
        return;
    }

    document.getElementById('bo-hotel-address-search')?.addEventListener('click', () => {
        if (typeof daum === 'undefined' || !daum.Postcode) {
            window.alert('주소 검색 스크립트를 불러오지 못했습니다.');
            return;
        }
        new daum.Postcode({
            oncomplete(data) {
                const base = document.getElementById('bo-hotel-address');
                const detail = document.getElementById('bo-hotel-address-detail');
                if (base) {
                    base.value = data.roadAddress || data.address || '';
                }
                if (detail) {
                    detail.focus();
                }
            },
        }).open();
    });

    const imageInput = document.getElementById('hotel_image');
    const imagePreview = document.getElementById('hotelImagePreview');
    const existingImageRemoveButton = document.querySelector('[data-existing-hotel-image-remove]');

    const removeExistingImage = () => {
        const wrap = document.getElementById('existingHotelImageWrapper');
        if (wrap) {
            wrap.remove();
        }
        let hidden = document.querySelector('input[name="remove_image"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'remove_image';
            hidden.value = '1';
            form.appendChild(hidden);
        }
    };

    if (existingImageRemoveButton) {
        existingImageRemoveButton.addEventListener('click', removeExistingImage);
    }

    if (imageInput && imagePreview) {
        const maxFileSize = 5 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png'];
        const uploadWrapper = imageInput.closest('.board-file-upload');

        const warnIfDimensionsOdd = (file) => {
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = () => {
                URL.revokeObjectURL(url);
                const w = img.naturalWidth;
                const h = img.naturalHeight;
                if (w > 1200 || h > 800) {
                    window.alert(
                        `업로드한 이미지 크기는 ${w}×${h}px 입니다. 권장(600×400px)과 차이가 크면 화면에서 잘릴 수 있습니다.`
                    );
                }
            };
            img.onerror = () => {
                URL.revokeObjectURL(url);
            };
            img.src = url;
        };

        const renderPreview = (file) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                imagePreview.innerHTML = `
                    <div class="board-file-item">
                        <div class="board-file-info">
                            <img src="${event.target?.result}" alt="이미지 미리보기" class="thumbnail-preview">
                            <span class="board-file-name">${file.name}</span>
                            <span class="board-file-size">(${(file.size / 1024 / 1024).toFixed(2)}MB)</span>
                        </div>
                        <button type="button" class="board-file-remove" id="hotel-image-remove-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                document.getElementById('hotel-image-remove-btn')?.addEventListener('click', () => {
                    imageInput.value = '';
                    imagePreview.innerHTML = '';
                });
            };
            reader.readAsDataURL(file);
        };

        const handleImageFile = (file) => {
            if (!allowedTypes.includes(file.type)) {
                window.alert('이미지 파일만 업로드 가능합니다. (JPG, PNG)');
                imageInput.value = '';
                return;
            }
            if (file.size > maxFileSize) {
                window.alert('이미지 파일은 5MB 이하만 업로드 가능합니다.');
                imageInput.value = '';
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(file);
            imageInput.files = dt.files;
            removeExistingImage();
            warnIfDimensionsOdd(file);
            renderPreview(file);
        };

        imageInput.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (file) {
                handleImageFile(file);
            }
        });

        if (uploadWrapper) {
            uploadWrapper.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadWrapper.classList.add('board-file-drag-over');
            });
            uploadWrapper.addEventListener('dragleave', (e) => {
                e.preventDefault();
                uploadWrapper.classList.remove('board-file-drag-over');
            });
            uploadWrapper.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadWrapper.classList.remove('board-file-drag-over');
                const file = e.dataTransfer?.files?.[0];
                if (file) {
                    handleImageFile(file);
                }
            });
        }
    }

    form.addEventListener('submit', () => {
        if (typeof window.syncBackofficeCKEditorFields === 'function') {
            window.syncBackofficeCKEditorFields(form);
        }
    });
});
