(function () {
    const MAX_SINGLE_SIZE = 10 * 1024 * 1024;
    const MAX_FILE_COUNT = 5;
    const FILE_ROW_SELECTOR = '[data-inquiry-file-row]';

    function getFileRows(form) {
        return Array.prototype.slice.call(form.querySelectorAll(FILE_ROW_SELECTOR));
    }

    function getSelectedFileCount(form) {
        return getFileRows(form).filter(function (row) {
            const input = row.querySelector('[data-abstract-file-input]');
            return input && input.files && input.files.length > 0;
        }).length;
    }

    function refreshFileList(fileList) {
        if (!fileList) {
            return;
        }

        if (fileList.querySelectorAll('a').length === 0) {
            fileList.classList.add('none');
            return;
        }

        fileList.classList.remove('none');
    }

    function getFileList(form) {
        return form.querySelector('[data-abstract-file-list]');
    }

    function removeNewFileLink(fileInput, fileList) {
        if (!fileInput || !fileList) {
            return;
        }

        fileList.querySelectorAll('[data-new-file-row-id]').forEach(function (link) {
            if (link.dataset.newFileRowId === fileInput.dataset.fileRowId) {
                link.remove();
            }
        });
        refreshFileList(fileList);
    }

    function resetFileRow(row) {
        const textInput = row ? row.querySelector('[data-abstract-file-name]') : null;
        const fileInput = row ? row.querySelector('[data-abstract-file-input]') : null;
        const form = row ? row.closest('[data-mypage-inquiry-form]') : null;
        const fileList = form ? getFileList(form) : null;

        removeNewFileLink(fileInput, fileList);

        if (fileInput) {
            fileInput.value = '';
        }
        if (textInput) {
            textInput.value = '';
        }
    }

    document.querySelectorAll('[data-mypage-inquiry-form]').forEach(function (form) {
        if (form.dataset.inquiryUploadReady === '1') {
            return;
        }
        form.dataset.inquiryUploadReady = '1';

        form.addEventListener('change', function (event) {
            const fileInput = event.target && event.target.matches('[data-abstract-file-input]')
                ? event.target
                : null;
            if (!fileInput) {
                return;
            }

            const fileRow = fileInput.closest(FILE_ROW_SELECTOR);
            const textInput = fileRow ? fileRow.querySelector('[data-abstract-file-name]') : null;
            const fileList = getFileList(form);
            const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

            removeNewFileLink(fileInput, fileList);

            if (!file) {
                if (textInput) {
                    textInput.value = '';
                }
                return;
            }

            if (file.size > MAX_SINGLE_SIZE) {
                window.alert('파일 용량은 10MB를 초과할 수 없습니다.');
                fileInput.value = '';
                if (textInput) {
                    textInput.value = '';
                }
                return;
            }

            if (getSelectedFileCount(form) > MAX_FILE_COUNT) {
                window.alert('첨부파일은 최대 5개까지 등록할 수 있습니다.');
                fileInput.value = '';
                if (textInput) {
                    textInput.value = '';
                }
                return;
            }

            if (textInput) {
                textInput.value = file.name;
            }

            if (fileList) {
                const link = document.createElement('a');
                link.href = 'javascript:void(0);';
                link.textContent = file.name;
                link.dataset.newFileRowId = fileInput.dataset.fileRowId || '';
                fileList.appendChild(link);
                refreshFileList(fileList);
            }
        });

        form.addEventListener('click', function (event) {
            const plusButton = event.target && event.target.closest('.btn_plus');
            if (plusButton) {
                const currentFlex = plusButton.closest(FILE_ROW_SELECTOR);
                const fileWrap = currentFlex ? currentFlex.closest('.file_wrap') : null;
                if (!currentFlex || !fileWrap) {
                    return;
                }

                if (getFileRows(form).length >= MAX_FILE_COUNT) {
                    window.alert('첨부파일은 최대 5개까지 등록할 수 있습니다.');
                    return;
                }

                const cloneFlex = currentFlex.cloneNode(true);
                const textInput = cloneFlex.querySelector('[data-abstract-file-name]');
                const fileInput = cloneFlex.querySelector('[data-abstract-file-input]');
                const label = cloneFlex.querySelector('label');
                const button = cloneFlex.querySelector('.btn_plus');
                const uniqueId = 'file_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

                if (textInput) {
                    textInput.value = '';
                }
                if (fileInput) {
                    fileInput.value = '';
                    fileInput.id = uniqueId;
                    fileInput.dataset.fileRowId = uniqueId;
                }
                if (label) {
                    label.setAttribute('for', uniqueId);
                }
                if (button) {
                    button.textContent = '삭제';
                    button.classList.remove('btn_plus');
                    button.classList.add('btn_minus');
                }

                fileWrap.appendChild(cloneFlex);
                return;
            }

            const minusButton = event.target && event.target.closest('.btn_minus');
            if (minusButton) {
                const currentFlex = minusButton.closest(FILE_ROW_SELECTOR);
                resetFileRow(currentFlex);
                if (currentFlex) {
                    currentFlex.remove();
                }
                return;
            }

            const fileLink = event.target && event.target.closest('[data-abstract-file-list] a');
            if (fileLink) {
                event.preventDefault();

                const fileList = fileLink.closest('[data-abstract-file-list]');
                const existingIndex = fileLink.dataset.existingAttachmentIndex;

                if (existingIndex !== undefined) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'delete_attachments[]';
                    hidden.value = existingIndex;
                    form.appendChild(hidden);
                }

                if (fileLink.dataset.newFileRowId) {
                    const fileInput = form.querySelector('[data-file-row-id="' + fileLink.dataset.newFileRowId + '"]');
                    const row = fileInput ? fileInput.closest(FILE_ROW_SELECTOR) : null;
                    if (fileInput) {
                        fileInput.value = '';
                    }
                    if (row) {
                        const textInput = row.querySelector('[data-abstract-file-name]');
                        if (textInput) {
                            textInput.value = '';
                        }
                    }
                }

                fileLink.remove();
                refreshFileList(fileList);
            }
        });

        getFileRows(form).forEach(function (row, index) {
            const fileInput = row.querySelector('[data-abstract-file-input]');
            if (fileInput && !fileInput.dataset.fileRowId) {
                fileInput.dataset.fileRowId = fileInput.id || 'file_' + index;
            }
        });
        refreshFileList(getFileList(form));
    });
})();
