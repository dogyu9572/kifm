document.addEventListener('DOMContentLoaded', () => {
    const deleteForms = document.querySelectorAll('.js-delete-confirm-form');
    const selectAllCheckbox = document.getElementById('select-all');
    const executiveCheckboxes = document.querySelectorAll('.executive-checkbox');
    const bulkDeleteButton = document.getElementById('bulk-delete-btn');
    const groupRadios = document.querySelectorAll('input[name="group_no"]');
    const photoUploadGroup = document.getElementById('photo-upload-group');
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const existingPhotoRemoveButton = document.querySelector('[data-existing-photo-remove]');
    const sortableTbody = document.getElementById('sortable-tbody');

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const confirmed = window.confirm('정말 이 임원 정보를 삭제하시겠습니까?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    const updateBulkDeleteButton = () => {
        if (!bulkDeleteButton) {
            return;
        }

        const checkedCount = Array.from(executiveCheckboxes).filter((checkbox) => checkbox.checked).length;
        bulkDeleteButton.disabled = checkedCount === 0;
        bulkDeleteButton.innerHTML = `<i class="fas fa-trash"></i> 선택 삭제 (${checkedCount})`;
    };

    if (selectAllCheckbox && executiveCheckboxes.length > 0) {
        selectAllCheckbox.addEventListener('change', () => {
            executiveCheckboxes.forEach((checkbox) => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButton();
        });

        executiveCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const checkedCount = Array.from(executiveCheckboxes).filter((item) => item.checked).length;
                selectAllCheckbox.checked = checkedCount === executiveCheckboxes.length;
                updateBulkDeleteButton();
            });
        });

        updateBulkDeleteButton();
    } else if (bulkDeleteButton) {
        bulkDeleteButton.style.display = 'none';
    }

    if (bulkDeleteButton) {
        bulkDeleteButton.addEventListener('click', async () => {
            const selectedIds = Array.from(executiveCheckboxes)
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value);

            if (selectedIds.length === 0) {
                window.alert('삭제할 임원을 선택해주세요.');
                return;
            }

            if (!window.confirm(`선택한 ${selectedIds.length}명의 임원을 삭제하시겠습니까?`)) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const formData = new FormData();
            selectedIds.forEach((id) => formData.append('executive_ids[]', id));

            const response = await fetch('/backoffice/society-executives/bulk-destroy', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: formData,
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                window.alert(data.message ?? '선택 삭제 중 오류가 발생했습니다.');
                return;
            }

            window.alert(data.message ?? '선택한 임원 정보가 삭제되었습니다.');
            window.location.reload();
        });
    }

    const updateSortOrder = async () => {
        if (!sortableTbody) {
            return;
        }

        const rows = Array.from(sortableTbody.querySelectorAll('tr[data-post-id]'));
        const updates = rows.map((row, index) => ({
            post_id: Number(row.dataset.postId),
            sort_order: rows.length - index,
        }));

        rows.forEach((row, index) => {
            const display = row.querySelector('.sort-order-display');
            if (display) {
                display.textContent = String(rows.length - index);
            }
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch('/backoffice/society-executives/update-sort-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ updates }),
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            window.alert(data.message ?? '정렬 순서 저장 중 오류가 발생했습니다.');
            return;
        }
    };

    if (sortableTbody && typeof Sortable !== 'undefined') {
        new Sortable(sortableTbody, {
            handle: '.sort-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: async (event) => {
                event.item.classList.remove('dragging');
                await updateSortOrder();
            },
            onStart: (event) => {
                event.item.classList.add('dragging');
            },
        });
    }

    const updatePhotoUploadVisibility = () => {
        if (!photoUploadGroup || groupRadios.length === 0) {
            return;
        }

        const selectedGroup = document.querySelector('input[name="group_no"]:checked')?.value;
        photoUploadGroup.style.display = selectedGroup === '1' ? '' : 'none';
    };

    if (groupRadios.length > 0) {
        groupRadios.forEach((radio) => {
            radio.addEventListener('change', updatePhotoUploadVisibility);
        });
        updatePhotoUploadVisibility();
    }

    const removeExistingPhoto = () => {
        const existingPhotoWrapper = document.getElementById('existingPhotoWrapper');
        if (existingPhotoWrapper) {
            existingPhotoWrapper.remove();
        }

        let removePhotoInput = document.querySelector('input[name="remove_photo"]');
        if (!removePhotoInput) {
            removePhotoInput = document.createElement('input');
            removePhotoInput.type = 'hidden';
            removePhotoInput.name = 'remove_photo';
            removePhotoInput.value = '1';
            document.querySelector('form')?.appendChild(removePhotoInput);
        }
    };

    if (existingPhotoRemoveButton) {
        existingPhotoRemoveButton.addEventListener('click', removeExistingPhoto);
    }

    if (photoInput && photoPreview) {
        const maxFileSize = 5 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const uploadWrapper = photoInput.closest('.board-file-upload');

        const renderPhotoPreview = (file) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                photoPreview.innerHTML = `
                    <div class="board-file-item">
                        <div class="board-file-info">
                            <img src="${event.target?.result}" alt="사진 미리보기" class="thumbnail-preview">
                            <span class="board-file-name">${file.name}</span>
                            <span class="board-file-size">(${(file.size / 1024 / 1024).toFixed(2)}MB)</span>
                        </div>
                        <button type="button" class="board-file-remove" id="photo-remove-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                document.getElementById('photo-remove-btn')?.addEventListener('click', () => {
                    photoInput.value = '';
                    photoPreview.innerHTML = '';
                });
            };
            reader.readAsDataURL(file);
        };

        const handlePhotoFile = (file) => {
            if (!allowedTypes.includes(file.type)) {
                window.alert('이미지 파일만 업로드 가능합니다. (JPG, PNG, GIF)');
                photoInput.value = '';
                return;
            }

            if (file.size > maxFileSize) {
                window.alert('사진 파일은 5MB 이하만 업로드 가능합니다.');
                photoInput.value = '';
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(file);
            photoInput.files = dt.files;
            removeExistingPhoto();
            renderPhotoPreview(file);
        };

        photoInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (file) {
                handlePhotoFile(file);
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
                    handlePhotoFile(file);
                }
            });
        }
    }
});
