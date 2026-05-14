/**
 * 이미지 파일 선택 시 썸네일 형태 미리보기(공통).
 * 위원회 배너, 강좌 썸네일, 주치의 사진 등에서 사용.
 */
(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /**
     * @param {object} options
     * @param {string} options.inputId file input id
     * @param {string} options.previewId 미리보기 컨테이너 id
     * @param {number} [options.maxFileSizeMb] 기본: input data-max-file-size-mb 또는 5
     * @param {string} [options.removeExistingSelector] 기존 첨부 제거 버튼
     * @param {string} [options.deleteCheckboxId] 기존 파일 삭제용 hidden checkbox id
     * @param {string} [options.existingItemId] 기존 파일 행 id
     */
    window.initBoardImageFilePreview = function initBoardImageFilePreview(options) {
        const input = document.getElementById(options.inputId);
        const preview = document.getElementById(options.previewId);
        if (!input || !preview) {
            return;
        }

        const maxFileSizeMb = Number(options.maxFileSizeMb ?? input.dataset.maxFileSizeMb ?? 5);
        const maxBytes = maxFileSizeMb * 1024 * 1024;

        const removeExistingBtn = options.removeExistingSelector
            ? document.querySelector(options.removeExistingSelector)
            : null;
        const deleteCheckbox = options.deleteCheckboxId
            ? document.getElementById(options.deleteCheckboxId)
            : null;
        const existingItem = options.existingItemId
            ? document.getElementById(options.existingItemId)
            : null;

        const uploadRoot = input.closest('.board-file-upload');
        const wrapper = input.closest('.board-file-input-wrapper');

        const hideExisting = () => {
            if (deleteCheckbox) {
                deleteCheckbox.checked = true;
            }
            if (existingItem) {
                existingItem.classList.add('bo-hidden');
            }
        };

        const showExisting = () => {
            if (deleteCheckbox) {
                deleteCheckbox.checked = false;
            }
            if (existingItem) {
                existingItem.classList.remove('bo-hidden');
            }
        };

        const render = (file, dataUrl) => {
            preview.innerHTML = `
                <div class="board-file-preview-item">
                    <img src="${dataUrl}" alt="" class="board-file-preview-img">
                    <div class="board-file-preview-info">
                        <span class="board-file-preview-name">${escapeHtml(file.name)}</span>
                        <span class="board-file-preview-size">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                    </div>
                    <button type="button" class="board-file-preview-remove js-board-image-file-preview-remove" aria-label="미리보기 제거">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
        };

        const clear = () => {
            preview.innerHTML = '';
        };

        const processFile = (file) => {
            if (!file) {
                input.value = '';
                clear();
                showExisting();
                return;
            }
            if (!file.type.startsWith('image/')) {
                window.alert(options.alertNotImage ?? '이미지 파일만 업로드할 수 있습니다.');
                input.value = '';
                return;
            }
            if (file.size > maxBytes) {
                window.alert(`파일은 ${maxFileSizeMb}MB 이하만 가능합니다.`);
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = (ev) => {
                const result = ev.target?.result;
                if (typeof result === 'string') {
                    render(file, result);
                    hideExisting();
                }
            };
            reader.readAsDataURL(file);
        };

        input.addEventListener('change', (e) => {
            processFile(e.target.files?.[0] ?? null);
        });

        preview.addEventListener('click', (e) => {
            if (!e.target.closest('.js-board-image-file-preview-remove')) {
                return;
            }
            input.value = '';
            clear();
            showExisting();
        });

        removeExistingBtn?.addEventListener('click', () => {
            hideExisting();
        });

        if (wrapper && uploadRoot) {
            wrapper.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadRoot.classList.add('board-file-drag-over');
            });
            wrapper.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadRoot.classList.remove('board-file-drag-over');
            });
            wrapper.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadRoot.classList.remove('board-file-drag-over');
                const file = e.dataTransfer.files?.[0];
                if (!file) {
                    return;
                }
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                processFile(file);
            });
        }
    };
})();
