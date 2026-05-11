document.addEventListener('DOMContentLoaded', () => {
    const recipientRadios = document.querySelectorAll('input[name="recipient_type"]');
    const scheduleCheckbox = document.getElementById('sms_schedule_enabled');
    const scheduleWrap = document.querySelector('.js-sms-scheduled-at-wrap');
    const smsBody = document.getElementById('body');
    const byteCount = document.getElementById('sms_byte_count');

    const parseJsonInput = (id) => {
        const value = document.getElementById(id)?.value;
        if (!value) return [];
        try {
            return JSON.parse(value);
        } catch (_error) {
            return [];
        }
    };

    const sourceMembers = parseJsonInput('sms_members_source');
    const sourceAddressBooks = parseJsonInput('sms_address_books_source');

    const selectedAddressBooksInput = document.getElementById('sms_selected_address_books_input');
    const selectedMemberIdsInput = document.getElementById('sms_selected_member_ids_input');
    let selectedAddressBooks = parseJsonInput('sms_selected_address_books_input');
    let selectedMemberIds = parseJsonInput('sms_selected_member_ids_input');

    const syncHiddenInputs = () => {
        if (selectedAddressBooksInput) selectedAddressBooksInput.value = JSON.stringify(selectedAddressBooks);
        if (selectedMemberIdsInput) selectedMemberIdsInput.value = JSON.stringify(selectedMemberIds);
    };

    const renderTagList = (container, rows, removeHandler) => {
        if (!container) return;
        container.innerHTML = '';
        rows.forEach((row) => {
            const item = document.createElement('div');
            item.className = 'existing-file';
            item.innerHTML = `
                <span>${row.label}</span>
                <button type="button" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> 제거</button>
            `;
            item.querySelector('button')?.addEventListener('click', () => removeHandler(row.id));
            container.appendChild(item);
        });
    };

    const renderAddressBooks = () => {
        const target = document.getElementById('sms_selected_address_books');
        renderTagList(
            target,
            sourceAddressBooks
                .filter((book) => selectedAddressBooks.includes(book.id))
                .map((book) => ({ id: book.id, label: `${book.name} (${book.member_count}명)` })),
            (id) => {
                selectedAddressBooks = selectedAddressBooks.filter((bookId) => bookId !== id);
                syncHiddenInputs();
                renderAddressBooks();
            }
        );
    };

    const renderMembers = () => {
        const target = document.getElementById('sms_member_search_result');
        renderTagList(
            target,
            sourceMembers
                .filter((member) => selectedMemberIds.includes(member.id))
                .map((member) => ({ id: member.id, label: `${member.name} (${member.phone ?? '-'})` })),
            (id) => {
                selectedMemberIds = selectedMemberIds.filter((memberId) => memberId !== id);
                syncHiddenInputs();
                renderMembers();
            }
        );
    };

    document.getElementById('sms_add_address_book_btn')?.addEventListener('click', () => {
        const select = document.getElementById('sms_address_book_select');
        const selectedId = Number(select?.value || 0);
        if (!selectedId || selectedAddressBooks.includes(selectedId)) return;
        selectedAddressBooks.push(selectedId);
        syncHiddenInputs();
        renderAddressBooks();
    });

    document.getElementById('sms_member_search_btn')?.addEventListener('click', () => {
        const keyword = String(document.getElementById('sms_member_search_keyword')?.value || '').trim();
        const candidate = sourceMembers.find((member) => member.name.includes(keyword) || String(member.phone ?? '').includes(keyword));
        if (!candidate || selectedMemberIds.includes(candidate.id)) return;
        selectedMemberIds.push(candidate.id);
        syncHiddenInputs();
        renderMembers();
    });

    const updateRecipientPanels = () => {
        const selectedType = document.querySelector('input[name="recipient_type"]:checked')?.value;
        document.querySelectorAll('.js-sms-recipient-panel').forEach((panel) => {
            panel.style.display = panel.getAttribute('data-panel') === selectedType ? '' : 'none';
        });
    };

    const updateSchedulePanel = () => {
        if (!scheduleWrap || !scheduleCheckbox) return;
        scheduleWrap.style.display = scheduleCheckbox.checked ? '' : 'none';
    };

    const updateByteCount = () => {
        if (!smsBody || !byteCount) return;
        let bytes = 0;
        for (const char of smsBody.value) {
            bytes += char.charCodeAt(0) > 127 ? 2 : 1;
        }
        byteCount.textContent = String(bytes);
    };

    recipientRadios.forEach((radio) => radio.addEventListener('change', updateRecipientPanels));
    scheduleCheckbox?.addEventListener('change', updateSchedulePanel);
    smsBody?.addEventListener('input', updateByteCount);

    document.querySelectorAll('.js-delete-confirm-form').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            if (!window.confirm('정말 삭제하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('.js-cancel-schedule-form').forEach((cancelForm) => {
        cancelForm.addEventListener('submit', (event) => {
            if (!window.confirm('예약 발송을 취소하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });

    syncHiddenInputs();
    renderAddressBooks();
    renderMembers();
    updateRecipientPanels();
    updateSchedulePanel();
    updateByteCount();
});
