(function () {
    const MAX_SINGLE_SIZE = 10 * 1024 * 1024;

    function formatPhoneKoreaDisplay(raw) {
        const d = String(raw || '').replace(/\D/g, '').slice(0, 11);
        if (!d.length) {
            return '';
        }
        if (d.length <= 3) {
            return d;
        }
        if (d.startsWith('010') || d.length === 11 || /^01[16789]/.test(d)) {
            if (d.length <= 7) {
                return d.slice(0, 3) + '-' + d.slice(3);
            }
            return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
        }
        if (d.length <= 7) {
            return d.slice(0, 3) + '-' + d.slice(3);
        }
        return d.slice(0, 3) + '-' + d.slice(3, 7) + '-' + d.slice(7);
    }

    function refreshFileList(list) {
        if (!list) {
            return;
        }
        if (list.children.length === 0) {
            list.classList.add('none');
        } else {
            list.classList.remove('none');
        }
    }

    document.querySelectorAll('[data-history-back]').forEach((button) => {
        button.addEventListener('click', function () {
            window.history.back();
        });
    });

    document.querySelectorAll('input[type="tel"]').forEach((input) => {
        input.value = formatPhoneKoreaDisplay(input.value);
        if (input.readOnly) {
            return;
        }
        input.addEventListener('input', function () {
            input.value = formatPhoneKoreaDisplay(input.value);
        });
    });

    document.querySelectorAll('[data-abstract-form]').forEach((form) => {
        const fileInput = form.querySelector('[data-abstract-file-input]');
        const fileNameInput = form.querySelector('[data-abstract-file-name]');
        const fileList = form.querySelector('[data-abstract-file-list]');
        const removedFiles = form.querySelector('[data-abstract-removed-files]');

        refreshFileList(fileList);

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                if (!file) {
                    if (fileNameInput) {
                        fileNameInput.value = '';
                    }
                    return;
                }

                if (file.size > MAX_SINGLE_SIZE) {
                    window.alert('파일 용량은 10MB를 초과할 수 없습니다.');
                    fileInput.value = '';
                    if (fileNameInput) {
                        fileNameInput.value = '';
                    }
                    return;
                }

                if (fileNameInput) {
                    fileNameInput.value = file.name;
                }
                if (fileList) {
                    fileList.innerHTML = '';
                    const item = document.createElement('a');
                    item.href = 'javascript:void(0);';
                    item.textContent = file.name;
                    fileList.appendChild(item);
                    refreshFileList(fileList);
                }
            });
        }

        form.querySelectorAll('[data-remove-existing-file]').forEach((button) => {
            button.addEventListener('click', function () {
                if (!window.confirm('해당 파일을 삭제하시겠습니까?')) {
                    return;
                }

                const fileId = button.dataset.fileId;
                if (removedFiles && fileId) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'remove_file_ids[]';
                    hidden.value = fileId;
                    removedFiles.appendChild(hidden);
                }

                const item = button.closest('[data-existing-file]');
                if (item) {
                    item.remove();
                }
                refreshFileList(fileList);
            });
        });
    });
})();
