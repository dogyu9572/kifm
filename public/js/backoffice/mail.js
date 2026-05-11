document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('mail-form');
    const recipientRadios = document.querySelectorAll('input[name="recipient_type"]');
    const scheduleEnabled = document.getElementById('schedule_enabled');
    const scheduleWrap = document.querySelector('.js-scheduled-at-wrap');
    const subjectInput = document.getElementById('subject');
    const subjectCount = document.getElementById('subject_count');

    const parseJsonInput = (id) => {
        const value = document.getElementById(id)?.value;
        if (!value) return [];
        try {
            return JSON.parse(value);
        } catch (_error) {
            return [];
        }
    };

    const sourceMembers = parseJsonInput('mail_members_source');
    const sourceExecutives = parseJsonInput('mail_executives_source');
    const sourceAddressBooks = parseJsonInput('mail_address_books_source');

    const selectedAddressBooksInput = document.getElementById('selected_address_books_input');
    const selectedMemberIdsInput = document.getElementById('selected_member_ids_input');
    const selectedExecutiveIdsInput = document.getElementById('selected_executive_ids_input');

    let selectedAddressBooks = parseJsonInput('selected_address_books_input');
    let selectedMemberIds = parseJsonInput('selected_member_ids_input');
    let selectedExecutiveIds = parseJsonInput('selected_executive_ids_input');

    const syncHiddenInputs = () => {
        if (selectedAddressBooksInput) selectedAddressBooksInput.value = JSON.stringify(selectedAddressBooks);
        if (selectedMemberIdsInput) selectedMemberIdsInput.value = JSON.stringify(selectedMemberIds);
        if (selectedExecutiveIdsInput) selectedExecutiveIdsInput.value = JSON.stringify(selectedExecutiveIds);
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
        const target = document.getElementById('selected_address_books');
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
        const target = document.getElementById('member_search_result');
        renderTagList(
            target,
            sourceMembers
                .filter((member) => selectedMemberIds.includes(member.id))
                .map((member) => ({ id: member.id, label: `${member.name} (${member.email ?? '-'})` })),
            (id) => {
                selectedMemberIds = selectedMemberIds.filter((memberId) => memberId !== id);
                syncHiddenInputs();
                renderMembers();
            }
        );
    };

    const renderExecutives = () => {
        const target = document.getElementById('executive_search_result');
        renderTagList(
            target,
            sourceExecutives
                .filter((executive) => selectedExecutiveIds.includes(executive.id))
                .map((executive) => ({ id: executive.id, label: `${executive.name} (${executive.email ?? '-'})` })),
            (id) => {
                selectedExecutiveIds = selectedExecutiveIds.filter((executiveId) => executiveId !== id);
                syncHiddenInputs();
                renderExecutives();
            }
        );
    };

    document.getElementById('add_address_book_btn')?.addEventListener('click', () => {
        const select = document.getElementById('address_book_select');
        const selectedId = Number(select?.value || 0);
        if (!selectedId || selectedAddressBooks.includes(selectedId)) return;
        selectedAddressBooks.push(selectedId);
        syncHiddenInputs();
        renderAddressBooks();
    });

    document.getElementById('member_search_btn')?.addEventListener('click', () => {
        const keyword = String(document.getElementById('member_search_keyword')?.value || '').trim();
        const candidate = sourceMembers.find((member) => member.name.includes(keyword) || String(member.email ?? '').includes(keyword));
        if (!candidate || selectedMemberIds.includes(candidate.id)) return;
        selectedMemberIds.push(candidate.id);
        syncHiddenInputs();
        renderMembers();
    });

    document.getElementById('executive_search_btn')?.addEventListener('click', () => {
        const keyword = String(document.getElementById('executive_search_keyword')?.value || '').trim();
        const candidate = sourceExecutives.find((executive) => executive.name.includes(keyword) || String(executive.email ?? '').includes(keyword));
        if (!candidate || selectedExecutiveIds.includes(candidate.id)) return;
        selectedExecutiveIds.push(candidate.id);
        syncHiddenInputs();
        renderExecutives();
    });

    const updateRecipientPanels = () => {
        const selectedType = document.querySelector('input[name="recipient_type"]:checked')?.value;
        document.querySelectorAll('.js-recipient-panel').forEach((panel) => {
            const panelType = panel.getAttribute('data-panel');
            panel.style.display = panelType === selectedType ? '' : 'none';
        });
    };

    const updateSchedulePanel = () => {
        if (!scheduleWrap || !scheduleEnabled) return;
        scheduleWrap.style.display = scheduleEnabled.checked ? '' : 'none';
    };

    const updateSubjectCount = () => {
        if (!subjectInput || !subjectCount) return;
        subjectCount.textContent = String(subjectInput.value.length);
    };

    recipientRadios.forEach((radio) => radio.addEventListener('change', updateRecipientPanels));
    scheduleEnabled?.addEventListener('change', updateSchedulePanel);
    subjectInput?.addEventListener('input', updateSubjectCount);

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
    renderExecutives();
    updateRecipientPanels();
    updateSchedulePanel();
    updateSubjectCount();
    if (!form) return;
});
