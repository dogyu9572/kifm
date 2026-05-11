/**
 * 연수교육 등록/수정: 차수 탭, 추가/삭제/재색인, 단발성·다회차 전환, CKEditor 동기화
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-edu-training-form');
    const deleteBtn = document.getElementById('bo-edu-training-delete-btn');
    const deleteForm = document.getElementById('bo-edu-training-delete-form');
    const panelsContainer = document.getElementById('bo-round-panels');
    const tabsContainer = document.getElementById('bo-round-tabs');
    const addRoundBtn = document.getElementById('bo-add-round-btn');
    const removeRoundBtn = document.getElementById('bo-remove-current-round');
    const roundsSection = document.getElementById('bo-rounds-section');
    const singleMethodBlock = document.getElementById('bo-training-method-single');
    const useRoundRadios = document.querySelectorAll('.js-use-round');
    const trainingMethodSelect = document.getElementById('training_method_select');

    let currentTab = 0;

    const getPanels = () => [...panelsContainer.querySelectorAll('.bo-round-panel')];

    const formatFileSize = (bytes) => `(${(bytes / 1024 / 1024).toFixed(2)}MB)`;

    class TextbookFileManager {
        constructor() {
            this.fileInput = document.getElementById('textbook_file');
            this.filePreview = document.getElementById('textbookPreview');
            this.fileUpload = this.fileInput?.closest('.board-file-upload');
            this.maxFileSize = Number(this.fileInput?.dataset.maxFileSizeMb || 20) * 1024 * 1024;
            if (this.fileInput && this.filePreview && this.fileUpload) {
                this.init();
            }
        }

        init() {
            this.fileInput.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) {
                    this.filePreview.innerHTML = '';
                    return;
                }
                if (file.size > this.maxFileSize) {
                    window.alert('교재 파일은 20MB 이하만 업로드할 수 있습니다.');
                    this.fileInput.value = '';
                    this.filePreview.innerHTML = '';
                    return;
                }
                this.render(file);
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
                const file = e.dataTransfer?.files?.[0];
                if (!file) {
                    return;
                }
                if (file.size > this.maxFileSize) {
                    window.alert('교재 파일은 20MB 이하만 업로드할 수 있습니다.');
                    return;
                }
                const dt = new DataTransfer();
                dt.items.add(file);
                this.fileInput.files = dt.files;
                this.render(file);
            });

            this.filePreview.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-remove-textbook-file]');
                if (!btn) {
                    return;
                }
                this.fileInput.value = '';
                this.filePreview.innerHTML = '';
            });
        }

        render(file) {
            this.filePreview.innerHTML = `
                <div class="board-file-item">
                    <div class="board-file-info">
                        <i class="fas fa-file"></i>
                        <span class="board-file-name">${file.name}</span>
                        <span class="board-file-size">${formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="board-file-remove" data-remove-textbook-file="1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }
    }

    class AttachmentFileManager {
        constructor() {
            this.fileInput = document.getElementById('attachment_files');
            this.filePreview = document.getElementById('attachmentFilePreview');
            this.fileUpload = this.fileInput?.closest('.board-file-upload');
            this.maxFileSize = Number(this.fileInput?.dataset.maxFileSizeMb || 20) * 1024 * 1024;
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
                    window.alert('첨부파일은 각 파일당 20MB 이하만 업로드할 수 있습니다.');
                    this.fileInput.value = '';
                    this.filePreview.innerHTML = '';
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
                    window.alert('첨부파일은 각 파일당 20MB 이하만 업로드할 수 있습니다.');
                    return;
                }
                this.mergeFiles(files);
            });

            this.filePreview.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-remove-attachment-index]');
                if (!btn) {
                    return;
                }
                const index = Number(btn.dataset.removeAttachmentIndex);
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
                const duplicated = merged.some(
                    (old) => old.name === file.name && old.size === file.size
                );
                if (!duplicated) {
                    merged.push(file);
                }
            });
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
                    <button type="button" class="board-file-remove" data-remove-attachment-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                this.filePreview.appendChild(el);
            });
        }
    }

    const syncCapacityDisabled = (panel) => {
        const unlimited = panel.querySelector('.js-capacity-unlimited');
        const input = panel.querySelector('.js-capacity-input');
        if (!unlimited || !input) {
            return;
        }
        if (unlimited.checked) {
            input.value = '';
            input.disabled = true;
        } else {
            input.disabled = false;
        }
    };

    const syncGradePriceRow = (chk) => {
        const row = chk.closest('.bo-grade-row');
        const wrap = row?.querySelector('.js-grade-price-wrap');
        const price = row?.querySelector('.js-grade-price');
        if (!wrap || !price) {
            return;
        }
        if (chk.checked) {
            wrap.classList.remove('bo-hidden');
        } else {
            wrap.classList.add('bo-hidden');
            price.value = '';
        }
    };

    const bindGradePriceUi = (panel) => {
        panel.querySelectorAll('.js-grade-eligible').forEach((chk) => {
            chk.addEventListener('change', () => syncGradePriceRow(chk));
            syncGradePriceRow(chk);
        });
    };

    const bindPanel = (panel) => {
        panel.querySelector('.js-capacity-unlimited')?.addEventListener('change', () => {
            syncCapacityDisabled(panel);
        });
        syncCapacityDisabled(panel);
        bindGradePriceUi(panel);
    };

    const reindexRoundPanels = () => {
        const panels = getPanels();
        panels.forEach((panel, i) => {
            panel.dataset.panelIndex = String(i);
            panel.querySelectorAll('[name^="rounds["]').forEach((el) => {
                el.name = el.name.replace(/rounds\[\d+\]/, `rounds[${i}]`);
            });
        });
    };

    const rebuildTabs = () => {
        if (!tabsContainer) {
            return;
        }
        const panels = getPanels();
        tabsContainer.innerHTML = '';
        panels.forEach((panel, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.tabIndex = String(idx);
            btn.className = `btn btn-sm bo-round-tab ${idx === currentTab ? 'btn-primary' : 'btn-outline-secondary'}`;
            const headingLabel = panel.querySelector('.js-round-heading-label')?.textContent?.trim();
            btn.textContent = headingLabel || `${idx + 1}차`;
            btn.addEventListener('click', () => switchTab(idx));
            tabsContainer.appendChild(btn);
        });
        updateRemoveButtonState();
    };

    const switchTab = (idx) => {
        const panels = getPanels();
        if (idx < 0 || idx >= panels.length) {
            return;
        }
        currentTab = idx;
        panels.forEach((p, i) => {
            p.classList.toggle('bo-hidden', i !== idx);
        });
        tabsContainer?.querySelectorAll('.bo-round-tab').forEach((btn, i) => {
            btn.classList.toggle('btn-primary', i === idx);
            btn.classList.toggle('btn-outline-secondary', i !== idx);
        });
        updateRemoveButtonState();
    };

    const updateRemoveButtonState = () => {
        if (!removeRoundBtn) {
            return;
        }
        const n = getPanels().length;
        removeRoundBtn.disabled = n <= 1 || currentTab === 0;
    };

    const syncUseRoundUi = () => {
        const useRound = document.querySelector('.js-use-round:checked')?.value === '1';
        if (roundsSection) {
            roundsSection.style.display = useRound ? '' : 'none';
        }
        if (singleMethodBlock) {
            singleMethodBlock.style.display = useRound ? 'none' : '';
        }
        if (trainingMethodSelect) {
            if (useRound) {
                trainingMethodSelect.removeAttribute('name');
                trainingMethodSelect.disabled = true;
            } else {
                trainingMethodSelect.setAttribute('name', 'training_method');
                trainingMethodSelect.disabled = false;
            }
        }

        if (useRound) {
            getPanels().forEach((panel) => {
                panel.querySelectorAll('input, select, textarea').forEach((el) => {
                    el.disabled = false;
                });
                syncCapacityDisabled(panel);
            });
        } else {
            panelsContainer?.querySelectorAll('input, select, textarea').forEach((el) => {
                el.disabled = true;
            });
        }
    };

    getPanels().forEach((p) => bindPanel(p));

    rebuildTabs();
    switchTab(0);

    useRoundRadios.forEach((radio) => {
        radio.addEventListener('change', () => {
            syncUseRoundUi();
        });
    });
    syncUseRoundUi();

    addRoundBtn?.addEventListener('click', () => {
        const panels = getPanels();
        if (panels.length >= 5) {
            window.alert('연수 차수는 최대 5개까지 추가할 수 있습니다.');
            return;
        }
        const template = panels[0];
        if (!template) {
            return;
        }
        const clone = template.cloneNode(true);
        clone.querySelectorAll('input, select, textarea').forEach((el) => {
            if (el.type === 'checkbox') {
                el.checked = false;
            } else if (el.type === 'hidden' && el.name.includes('[eligible]')) {
                el.value = '0';
            } else {
                el.value = '';
            }
        });
        clone.querySelectorAll('.js-grade-price-wrap').forEach((w) => w.classList.add('bo-hidden'));
        const nextIdx = panels.length;
        const defaultLabel = `${nextIdx + 1}차`;
        const heading = clone.querySelector('.js-round-heading-label');
        if (heading) {
            heading.textContent = defaultLabel;
        }
        clone.classList.remove('bo-hidden');
        panelsContainer.appendChild(clone);
        bindPanel(clone);
        reindexRoundPanels();
        rebuildTabs();
        switchTab(nextIdx);
    });

    removeRoundBtn?.addEventListener('click', () => {
        const panels = getPanels();
        if (panels.length <= 1) {
            window.alert('최소 1개의 차수는 필요합니다.');
            return;
        }
        if (currentTab === 0) {
            window.alert('1차는 삭제할 수 없습니다.');
            return;
        }
        if (!window.confirm('현재 차수를 삭제하시겠습니까?')) {
            return;
        }
        panels[currentTab]?.remove();
        reindexRoundPanels();
        rebuildTabs();
        switchTab(Math.min(currentTab, getPanels().length - 1));
    });

    if (deleteBtn && deleteForm) {
        deleteBtn.addEventListener('click', () => {
            const ok = window.confirm('정말 이 연수교육을 삭제하시겠습니까?');
            if (ok) {
                deleteForm.submit();
            }
        });
    }

    document.addEventListener('click', (event) => {
        const removeTextbookBtn = event.target.closest('.board-attachment-remove[data-existing-textbook-remove]');
        if (removeTextbookBtn) {
            const removedTextbookContainer = document.getElementById('bo-removed-textbook');
            if (removedTextbookContainer && !removedTextbookContainer.querySelector('input[name="delete_textbook_file"]')) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'delete_textbook_file';
                hidden.value = '1';
                removedTextbookContainer.appendChild(hidden);
            }
            const row = removeTextbookBtn.closest('.existing-file');
            if (row) {
                row.remove();
            }
            return;
        }

        const removeBtn = event.target.closest('.board-attachment-remove[data-existing-attachment-id]');
        if (!removeBtn) {
            return;
        }
        const attachmentId = Number(removeBtn.dataset.existingAttachmentId);
        if (Number.isNaN(attachmentId)) {
            return;
        }
        const removedContainer = document.getElementById('bo-removed-attachments');
        if (removedContainer) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'delete_attachment_ids[]';
            hidden.value = String(attachmentId);
            removedContainer.appendChild(hidden);
        }
        const row = removeBtn.closest('.existing-file');
        if (row) {
            row.remove();
        }
    });

    if (form) {
        form.addEventListener('submit', () => {
            const useRound = document.querySelector('.js-use-round:checked')?.value === '1';
            if (!useRound) {
                panelsContainer?.querySelectorAll('input, select, textarea').forEach((el) => {
                    el.disabled = true;
                });
            }
            if (typeof window.syncBackofficeCKEditorFields === 'function') {
                window.syncBackofficeCKEditorFields(form);
            }
        });
    }

    new TextbookFileManager();
    new AttachmentFileManager();
});
