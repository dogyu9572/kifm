/**
 * 스폰서 등록/수정: 임원진(society-executives)과 동일한 로고 업로드(선택·드래그·미리보기·기존 삭제)
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-sponsor-master-form');
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    const existingLogoRemoveButton = document.querySelector('[data-existing-logo-remove]');

    if (!form || !logoInput || !logoPreview) {
        return;
    }

    const removeExistingLogo = () => {
        const existingLogoWrapper = document.getElementById('existingLogoWrapper');
        if (existingLogoWrapper) {
            existingLogoWrapper.remove();
        }

        let removeLogoInput = document.querySelector('input[name="remove_logo"]');
        if (!removeLogoInput) {
            removeLogoInput = document.createElement('input');
            removeLogoInput.type = 'hidden';
            removeLogoInput.name = 'remove_logo';
            removeLogoInput.value = '1';
            form.appendChild(removeLogoInput);
        }
    };

    if (existingLogoRemoveButton) {
        existingLogoRemoveButton.addEventListener('click', removeExistingLogo);
    }

    const maxFileSize = 5 * 1024 * 1024;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const uploadWrapper = logoInput.closest('.board-file-upload');

    const renderLogoPreview = (file) => {
        const reader = new FileReader();
        reader.onload = (event) => {
            logoPreview.innerHTML = `
                <div class="board-file-item">
                    <div class="board-file-info">
                        <img src="${event.target?.result}" alt="로고 미리보기" class="thumbnail-preview">
                        <span class="board-file-name">${file.name}</span>
                        <span class="board-file-size">(${(file.size / 1024 / 1024).toFixed(2)}MB)</span>
                    </div>
                    <button type="button" class="board-file-remove" id="logo-remove-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.getElementById('logo-remove-btn')?.addEventListener('click', () => {
                logoInput.value = '';
                logoPreview.innerHTML = '';
            });
        };
        reader.readAsDataURL(file);
    };

    const handleLogoFile = (file) => {
        if (!allowedTypes.includes(file.type)) {
            window.alert('이미지 파일만 업로드 가능합니다. (JPG, PNG, GIF)');
            logoInput.value = '';
            return;
        }

        if (file.size > maxFileSize) {
            window.alert('로고 파일은 5MB 이하만 업로드 가능합니다.');
            logoInput.value = '';
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        logoInput.files = dt.files;
        removeExistingLogo();
        renderLogoPreview(file);
    };

    logoInput.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (file) {
            handleLogoFile(file);
        }
    });

    if (uploadWrapper) {
        uploadWrapper.addEventListener('dragover', (event) => {
            event.preventDefault();
            uploadWrapper.classList.add('board-file-drag-over');
        });

        uploadWrapper.addEventListener('dragleave', (event) => {
            event.preventDefault();
            uploadWrapper.classList.remove('board-file-drag-over');
        });

        uploadWrapper.addEventListener('drop', (event) => {
            event.preventDefault();
            uploadWrapper.classList.remove('board-file-drag-over');
            const file = event.dataTransfer?.files?.[0];
            if (file) {
                handleLogoFile(file);
            }
        });
    }
});
