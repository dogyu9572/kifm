document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-academic-event-abstract-form');
    if (!form) {
        return;
    }

    const maxFiles = 5;
    const formatFileSize = (bytes) => `(${(bytes / 1024 / 1024).toFixed(2)}MB)`;

    let eventsFields = {};
    try {
        eventsFields = JSON.parse(form.dataset.eventsFields || '{}');
    } catch {
        eventsFields = {};
    }

    const eventSelect = form.querySelector('.bo-ae-abs-event-select');
    const fieldSelect = document.getElementById('academic_event_field_id');
    const registeredRadios = form.querySelectorAll('.bo-ae-abs-registered-by');
    const memberWrap = form.querySelector('.js-ae-abstract-member-wrap');
    const dbBtn = document.getElementById('bo-ae-abs-db-btn');
    const guide = document.getElementById('bo-ae-abs-reg-guide');

    const memberSelectorRoot = form.querySelector('.js-member-selector');
    const authorNameInput = document.getElementById('author_name');
    const authorEmailInput = form.querySelector('input[name="author_email"]');
    const authorMobileInput = form.querySelector('input[name="author_mobile"]');
    memberSelectorRoot?.addEventListener('bo-member-selected', (event) => {
        const detail = event.detail;
        if (!detail) {
            return;
        }
        if (authorNameInput) {
            authorNameInput.value = detail.name || detail.label || '';
        }
        if (authorEmailInput) {
            authorEmailInput.value = detail.email || '';
        }
        if (authorMobileInput) {
            authorMobileInput.value = detail.phone_number || '';
        }
    });

    const rebuildFieldOptions = () => {
        if (!eventSelect || !fieldSelect) {
            return;
        }
        const eventId = String(eventSelect.value || '');
        const rows = eventsFields[eventId] || [];
        const current = fieldSelect.value;
        fieldSelect.innerHTML = '';
        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = '-- 선택 --';
        fieldSelect.appendChild(emptyOpt);
        rows.forEach((row) => {
            const opt = document.createElement('option');
            opt.value = String(row.id);
            opt.textContent = row.name;
            fieldSelect.appendChild(opt);
        });
        const still = rows.some((r) => String(r.id) === String(current));
        if (still) {
            fieldSelect.value = current;
        }
    };

    eventSelect?.addEventListener('change', () => {
        rebuildFieldOptions();
    });
    rebuildFieldOptions();

    const syncRegisteredByUi = () => {
        let val = 'user';
        registeredRadios.forEach((r) => {
            if (r.checked) {
                val = r.value;
            }
        });
        if (memberWrap) {
            if (val === 'admin') {
                memberWrap.classList.remove('d-none');
            } else {
                memberWrap.classList.add('d-none');
            }
        }
        if (dbBtn) {
            if (val === 'admin') {
                dbBtn.classList.remove('d-none');
            } else {
                dbBtn.classList.add('d-none');
            }
        }
        if (guide) {
            guide.textContent =
                val === 'admin'
                    ? "관리자 등록 시 '회원 DB 연동' 또는 '회원 조회' 버튼을 클릭하여 회원 계정을 선택하세요."
                    : '사용자(본인) 정보가 입력됩니다.';
        }
    };

    registeredRadios.forEach((r) => r.addEventListener('change', syncRegisteredByUi));
    syncRegisteredByUi();

    dbBtn?.addEventListener('click', () => {
        const opener = memberSelectorRoot?.querySelector('.js-open-member-modal');
        opener?.click();
    });

    class AbstractAttachmentFileManager {
        constructor() {
            this.fileInput = document.getElementById('bo-ae-abs-attachments');
            this.filePreview = document.getElementById('bo-ae-abs-attachment-preview');
            this.fileUpload = this.fileInput?.closest('.board-file-upload');
            this.maxFileSize = Number(this.fileInput?.dataset.maxFileSizeMb || 10) * 1024 * 1024;
            this.maxFiles = maxFiles;
            if (this.fileInput && this.filePreview && this.fileUpload) {
                this.init();
            }
        }

        countExistingRows() {
            return this.fileUpload.querySelectorAll('.board-attachment-item.existing-file').length;
        }

        init() {
            this.fileInput.addEventListener('change', (e) => {
                const files = Array.from(e.target.files || []);
                if (files.length === 0) {
                    this.filePreview.innerHTML = '';
                    return;
                }
                if (this.hasOversized(files)) {
                    window.alert('각 파일은 10MB 이하여야 합니다.');
                    this.fileInput.value = '';
                    this.filePreview.innerHTML = '';
                    return;
                }
                const cap = this.maxFiles - this.countExistingRows();
                if (cap <= 0) {
                    window.alert(`첨부파일은 최대 ${this.maxFiles}개까지 등록할 수 있습니다.`);
                    this.fileInput.value = '';
                    this.filePreview.innerHTML = '';
                    return;
                }
                let use = files;
                if (files.length > cap) {
                    window.alert(`첨부파일은 최대 ${this.maxFiles}개까지 등록할 수 있습니다. (초과분은 제외됩니다.)`);
                    use = files.slice(0, cap);
                }
                this.replaceFiles(use);
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
                    window.alert('각 파일은 10MB 이하여야 합니다.');
                    return;
                }
                this.mergeFiles(files);
            });

            this.filePreview.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-remove-ae-abs-index]');
                if (!btn) {
                    return;
                }
                const index = Number(btn.dataset.removeAeAbsIndex);
                if (Number.isNaN(index)) {
                    return;
                }
                this.removeFile(index);
            });
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
                const duplicated = merged.some((old) => old.name === file.name && old.size === file.size);
                if (!duplicated) {
                    merged.push(file);
                }
            });
            const existingN = this.countExistingRows();
            if (existingN + merged.length > this.maxFiles) {
                window.alert(`첨부파일은 최대 ${this.maxFiles}개까지 등록할 수 있습니다.`);
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
                    <button type="button" class="board-file-remove" data-remove-ae-abs-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                this.filePreview.appendChild(el);
            });
        }
    }

    document.addEventListener('click', (event) => {
        const removeBtn = event.target.closest('.board-attachment-remove[data-existing-abstract-file-id]');
        if (!removeBtn) {
            return;
        }
        const fileId = Number(removeBtn.dataset.existingAbstractFileId);
        if (Number.isNaN(fileId)) {
            return;
        }
        const removedContainer = document.getElementById('bo-ae-abs-removed-files');
        if (removedContainer) {
            const dup = removedContainer.querySelector(`input[name="remove_file_ids[]"][value="${String(fileId)}"]`);
            if (!dup) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'remove_file_ids[]';
                hidden.value = String(fileId);
                removedContainer.appendChild(hidden);
            }
        }
        const row = removeBtn.closest('.existing-file');
        if (row) {
            row.remove();
        }
    });

    new AbstractAttachmentFileManager();

    const validateAttachmentsForSubmit = () => {
        const fileInput = document.getElementById('bo-ae-abs-attachments');
        if (!fileInput) {
            return true;
        }
        const files = Array.from(fileInput.files || []);
        const maxBytes = 10 * 1024 * 1024;
        const nExisting = form.querySelectorAll('.bo-ae-abs-attachment-block .board-attachment-item.existing-file').length;
        if (nExisting + files.length > maxFiles) {
            window.alert(`첨부파일은 최대 ${maxFiles}개까지 등록할 수 있습니다.`);
            return false;
        }
        for (let i = 0; i < files.length; i += 1) {
            if (files[i].size > maxBytes) {
                window.alert('각 파일은 10MB 이하여야 합니다.');
                return false;
            }
        }
        return true;
    };

    form.addEventListener('submit', (e) => {
        if (!validateAttachmentsForSubmit()) {
            e.preventDefault();
        }
    });
});
