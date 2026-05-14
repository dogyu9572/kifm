/**
 * 1:1 문의 답변 작성: 답변 완료 라디오 클릭 시 답변 일시 자동 채움, 답변 첨부 드래그앤드롭/미리보기/삭제
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-one-on-one-inquiry-form');
    if (!form) {
        return;
    }

    const statusInputs = form.querySelectorAll('.js-answer-status');
    const answeredAtInput = form.querySelector('.js-answered-at');
    const MAX_FILES = 5;

    const formatFileSize = (bytes) => `(${(bytes / 1024 / 1024).toFixed(2)}MB)`;

    const formatLocalDatetime = (date) => {
        const pad = (n) => String(n).padStart(2, '0');
        const yyyy = date.getFullYear();
        const mm = pad(date.getMonth() + 1);
        const dd = pad(date.getDate());
        const hh = pad(date.getHours());
        const mi = pad(date.getMinutes());
        return `${yyyy}-${mm}-${dd}T${hh}:${mi}`;
    };

    statusInputs.forEach((radio) => {
        radio.addEventListener('change', () => {
            if (!answeredAtInput) {
                return;
            }
            if (radio.value === 'DONE' && radio.checked) {
                if (!answeredAtInput.value) {
                    answeredAtInput.value = formatLocalDatetime(new Date());
                }
            }
            if (radio.value === 'PENDING' && radio.checked) {
                answeredAtInput.value = '';
            }
        });
    });

    class AnswerAttachmentFileManager {
        constructor() {
            this.fileInput = document.getElementById('answer_attachments');
            this.filePreview = document.getElementById('answerAttachmentFilePreview');
            this.fileUpload = this.fileInput?.closest('.board-file-upload');
            this.maxFileSize = Number(this.fileInput?.dataset.maxFileSizeMb || 10) * 1024 * 1024;
            this.removedContainer = document.getElementById('bo-removed-answer-attachments');
            this.existingList = this.fileUpload?.querySelector('.board-existing-files');
            if (this.fileInput && this.filePreview && this.fileUpload) {
                this.init();
            }
        }

        init() {
            this.fileInput.addEventListener('change', (e) => {
                const files = Array.from(e.target.files || []);
                if (files.length === 0) {
                    this.filePreview.innerHTML = '';
                    return;
                }
                if (this.hasOversized(files)) {
                    window.alert('답변 첨부는 각 파일당 10MB 이하만 업로드할 수 있습니다.');
                    this.fileInput.value = '';
                    this.filePreview.innerHTML = '';
                    return;
                }
                if (this.exceedsMaxCount(files.length)) {
                    return;
                }
                this.replaceFiles(files);
            });

            this.fileUpload.addEventListener('dragover', (e) => {
                e.preventDefault();
                this.fileUpload.classList.add('board-file-drag-over');
            });

            this.fileUpload.addEventListener('dragleave', (e) => {
                e.preventDefault();
                this.fileUpload.classList.remove('board-file-drag-over');
            });

            this.fileUpload.addEventListener('drop', (e) => {
                e.preventDefault();
                this.fileUpload.classList.remove('board-file-drag-over');
                const files = Array.from(e.dataTransfer?.files || []);
                if (files.length === 0) {
                    return;
                }
                if (this.hasOversized(files)) {
                    window.alert('답변 첨부는 각 파일당 10MB 이하만 업로드할 수 있습니다.');
                    return;
                }
                this.mergeFiles(files);
            });

            this.filePreview.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-remove-answer-attachment-index]');
                if (!btn) {
                    return;
                }
                const index = Number(btn.dataset.removeAnswerAttachmentIndex);
                if (Number.isNaN(index)) {
                    return;
                }
                this.removeFile(index);
            });
        }

        currentExistingCount() {
            if (!this.existingList) {
                return 0;
            }
            return this.existingList.querySelectorAll('.existing-file').length;
        }

        exceedsMaxCount(adding) {
            const total = this.currentExistingCount() + adding;
            if (total > MAX_FILES) {
                window.alert(`답변 첨부는 최대 ${MAX_FILES}개까지 업로드할 수 있습니다.`);
                this.fileInput.value = '';
                this.filePreview.innerHTML = '';
                return true;
            }
            return false;
        }

        hasOversized(files) {
            return files.some((file) => file.size > this.maxFileSize);
        }

        replaceFiles(files) {
            const dt = new DataTransfer();
            files.forEach((file) => dt.items.add(file));
            this.fileInput.files = dt.files;
            this.render();
        }

        mergeFiles(files) {
            const existing = Array.from(this.fileInput.files || []);
            const merged = [...existing];
            files.forEach((file) => {
                const duplicated = merged.some(
                    (old) => old.name === file.name && old.size === file.size
                );
                if (!duplicated) {
                    merged.push(file);
                }
            });
            if (this.currentExistingCount() + merged.length > MAX_FILES) {
                window.alert(`답변 첨부는 최대 ${MAX_FILES}개까지 업로드할 수 있습니다.`);
                return;
            }
            this.replaceFiles(merged);
        }

        removeFile(index) {
            const files = Array.from(this.fileInput.files || []);
            files.splice(index, 1);
            this.replaceFiles(files);
        }

        render() {
            const files = Array.from(this.fileInput.files || []);
            this.filePreview.innerHTML = '';
            files.forEach((file, index) => {
                const el = document.createElement('div');
                el.className = 'board-file-item';
                el.innerHTML = `
                    <div class="board-file-info">
                        <i class="fas fa-file"></i>
                        <span class="board-file-name">${file.name}</span>
                        <span class="board-file-size">${formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="board-file-remove" data-remove-answer-attachment-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                this.filePreview.appendChild(el);
            });
        }
    }

    form.addEventListener('click', (event) => {
        const removeBtn = event.target.closest('.board-attachment-remove[data-existing-attachment-index]');
        if (!removeBtn) {
            return;
        }
        const idx = removeBtn.dataset.existingAttachmentIndex;
        if (idx === undefined) {
            return;
        }
        const removedContainer = document.getElementById('bo-removed-answer-attachments');
        if (removedContainer) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'delete_answer_attachment_indexes[]';
            hidden.value = String(idx);
            removedContainer.appendChild(hidden);
        }
        const row = removeBtn.closest('.existing-file');
        if (row) {
            row.remove();
        }
    });

    form.addEventListener('submit', (event) => {
        const checked = Array.from(statusInputs).find((r) => r.checked);
        if (!checked || checked.value !== 'DONE') {
            if (typeof window.syncBackofficeCKEditorFields === 'function') {
                window.syncBackofficeCKEditorFields(form);
            }
            return;
        }
        if (typeof window.syncBackofficeCKEditorFields === 'function') {
            window.syncBackofficeCKEditorFields(form);
        }
        const textarea = form.querySelector('#answer_content');
        const value = textarea?.value?.trim() ?? '';
        if (value === '') {
            event.preventDefault();
            window.alert('답변 완료 시 답변 내용을 입력해주세요.');
            textarea?.focus();
        }
    });

    new AnswerAttachmentFileManager();
});
