document.addEventListener('DOMContentLoaded', () => {
    const membersInput = document.getElementById('address_members_input');
    const sourceInput = document.getElementById('address_members_source');
    const selectedContainer = document.getElementById('address_selected_members');
    const searchButton = document.getElementById('address_member_search_btn');

    const parseJson = (rawValue) => {
        if (!rawValue) return [];
        try {
            return JSON.parse(rawValue);
        } catch (_error) {
            return [];
        }
    };

    const sourceMembers = parseJson(sourceInput?.value);
    let selectedMembers = parseJson(membersInput?.value);

    const syncMembersInput = () => {
        if (membersInput) {
            membersInput.value = JSON.stringify(selectedMembers);
        }
    };

    const renderSelectedMembers = () => {
        if (!selectedContainer) return;
        selectedContainer.innerHTML = '';
        selectedMembers.forEach((member, index) => {
            const item = document.createElement('div');
            item.className = 'existing-file';
            item.innerHTML = `
                <span>${member.name} / ${member.email ?? '-'} / ${member.phone ?? '-'}</span>
                <button type="button" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> 제거</button>
            `;
            item.querySelector('button')?.addEventListener('click', () => {
                selectedMembers.splice(index, 1);
                syncMembersInput();
                renderSelectedMembers();
            });
            selectedContainer.appendChild(item);
        });
    };

    searchButton?.addEventListener('click', async () => {
        const keyword = String(document.getElementById('address_member_keyword')?.value || '').trim();
        if (keyword === '') return;

        const endpoint = `/backoffice/address-books/search-members?name=${encodeURIComponent(keyword)}&login_id=${encodeURIComponent(keyword)}&email=${encodeURIComponent(keyword)}&phone=${encodeURIComponent(keyword)}`;
        const response = await fetch(endpoint);
        const data = await response.json();
        const found = Array.isArray(data.members) ? data.members[0] : null;
        if (!found) return;

        if (selectedMembers.some((member) => String(member.member_id) === String(found.member_id))) {
            return;
        }

        selectedMembers.push(found);
        syncMembersInput();
        renderSelectedMembers();
    });

    document.querySelectorAll('.js-delete-confirm-form').forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            if (!window.confirm('정말 주소록을 삭제하시겠습니까?')) {
                event.preventDefault();
            }
        });
    });

    syncMembersInput();
    renderSelectedMembers();
});
