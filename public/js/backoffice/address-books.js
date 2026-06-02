document.addEventListener('DOMContentLoaded', () => {
    const membersInput = document.getElementById('address_members_input');
    const selectedContainer = document.getElementById('address_selected_members');
    const selectorRoot = document.querySelector('.js-address-member-selector');
    const openButton = document.getElementById('address_member_search_btn');
    const modal = document.querySelector('.js-address-member-modal');
    const closeButton = modal?.querySelector('.js-close-address-member-modal');
    const searchButton = modal?.querySelector('.js-search-address-member');
    const searchField = modal?.querySelector('.js-address-member-search-field');
    const keywordInput = modal?.querySelector('.js-address-member-keyword');
    const resultsBody = modal?.querySelector('.js-address-member-results');
    const paginationRoot = modal?.querySelector('.js-address-member-pagination');
    const searchUrl = selectorRoot?.dataset.searchUrl;
    let currentPage = 1;

    const escapeHtml = (value) =>
        String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    const parseJson = (rawValue) => {
        if (!rawValue) return [];
        try {
            return JSON.parse(rawValue);
        } catch (_error) {
            return [];
        }
    };

    let selectedMembers = parseJson(membersInput?.value);

    const memberId = (member) => member.member_id ?? member.id ?? '';
    const memberKey = (member) => {
        const id = memberId(member);
        if (id !== '') {
            return `id:${id}`;
        }

        return `manual:${member.name ?? ''}:${member.email ?? ''}:${member.phone ?? member.phone_number ?? ''}`;
    };
    const isSelected = (member) => selectedMembers.some((selected) => memberKey(selected) === memberKey(member));

    const normalizeMember = (member) => ({
        member_id: memberId(member),
        name: member.name ?? '',
        login_id: member.login_id ?? '',
        email: member.email ?? '',
        phone: member.phone ?? member.phone_number ?? '',
        source_type: member.source_type ?? 'SEARCH',
    });

    const syncMembersInput = () => {
        if (membersInput) {
            membersInput.value = JSON.stringify(selectedMembers);
        }
    };

    const renderSelectedMembers = () => {
        if (!selectedContainer) return;
        selectedContainer.innerHTML = '';

        if (selectedMembers.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'existing-file';
            empty.textContent = '추가된 회원이 없습니다.';
            selectedContainer.appendChild(empty);
            return;
        }

        selectedMembers.forEach((member, index) => {
            const item = document.createElement('div');
            item.className = 'existing-file';
            item.innerHTML = `
                <span>${escapeHtml(member.name)} / ${escapeHtml(member.login_id || '-')} / ${escapeHtml(member.email || '-')} / ${escapeHtml(member.phone || '-')}</span>
                <button type="button" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> 제거</button>
            `;
            item.querySelector('button')?.addEventListener('click', () => {
                selectedMembers.splice(index, 1);
                syncMembersInput();
                renderSelectedMembers();
                refreshSelectedButtons();
            });
            selectedContainer.appendChild(item);
        });
    };

    const addMember = (rawMember) => {
        const member = normalizeMember(rawMember);
        if (isSelected(member)) {
            return;
        }

        selectedMembers.push(member);
        syncMembersInput();
        renderSelectedMembers();
    };

    const closeModal = () => {
        if (modal) {
            modal.style.display = 'none';
        }
    };

    const openModal = () => {
        if (!modal || !keywordInput) {
            return;
        }
        modal.style.display = 'block';
        keywordInput.focus();
        fetchMembers(1);
    };

    const refreshSelectedButtons = () => {
        resultsBody?.querySelectorAll('.js-select-address-member').forEach((button) => {
            const member = {
                id: button.dataset.id ?? '',
                name: button.dataset.name ?? '',
                login_id: button.dataset.loginId ?? '',
                email: button.dataset.email ?? '',
                phone_number: button.dataset.phone ?? '',
            };
            const selected = isSelected(normalizeMember(member));
            button.textContent = selected ? '추가됨' : '추가';
            button.disabled = selected;
        });
    };

    const renderRows = (members, meta) => {
        if (!resultsBody) return;

        if (!Array.isArray(members) || members.length === 0) {
            resultsBody.innerHTML = '<tr><td colspan="6" class="text-center">검색 결과가 없습니다.</td></tr>';
            return;
        }

        const startNo = ((Number(meta?.current_page ?? 1) - 1) * Number(meta?.per_page ?? 10)) + 1;
        resultsBody.innerHTML = members.map((member, idx) => {
            const normalized = normalizeMember(member);
            const selected = isSelected(normalized);
            return `
                <tr>
                    <td>${startNo + idx}</td>
                    <td>${escapeHtml(normalized.name || '-')}</td>
                    <td>${escapeHtml(normalized.login_id || '-')}</td>
                    <td>${escapeHtml(normalized.phone || '-')}</td>
                    <td>${escapeHtml(normalized.email || '-')}</td>
                    <td><button type="button" class="btn btn-sm btn-primary js-select-address-member"
                        data-id="${escapeHtml(normalized.member_id)}"
                        data-name="${escapeHtml(normalized.name)}"
                        data-login-id="${escapeHtml(normalized.login_id)}"
                        data-phone="${escapeHtml(normalized.phone)}"
                        data-email="${escapeHtml(normalized.email)}"
                        ${selected ? 'disabled' : ''}
                    >${selected ? '추가됨' : '추가'}</button></td>
                </tr>
            `;
        }).join('');
    };

    const renderPagination = (meta) => {
        if (!paginationRoot || !meta || (meta.last_page ?? 1) <= 1) {
            if (paginationRoot) {
                paginationRoot.innerHTML = '';
            }
            return;
        }

        const current = Number(meta.current_page ?? 1);
        const last = Number(meta.last_page ?? 1);
        const start = Math.max(1, current - 2);
        const end = Math.min(last, start + 4);
        const numberButtons = [];

        for (let page = start; page <= end; page += 1) {
            numberButtons.push(`<li class="page-item ${page === current ? 'active' : ''}"><a class="page-link js-address-member-page" data-page="${page}" href="#">${page}</a></li>`);
        }

        paginationRoot.innerHTML = `
            <ul class="pagination">
                <li class="page-item ${current <= 1 ? 'disabled' : ''}">
                    <a class="page-link js-address-member-page" data-page="${Math.max(1, current - 1)}" href="#" aria-label="이전 페이지"><i class="fas fa-chevron-left"></i></a>
                </li>
                ${numberButtons.join('')}
                <li class="page-item ${current >= last ? 'disabled' : ''}">
                    <a class="page-link js-address-member-page" data-page="${Math.min(last, current + 1)}" href="#" aria-label="다음 페이지"><i class="fas fa-chevron-right"></i></a>
                </li>
            </ul>
        `;
    };

    const fetchMembers = async (page = 1) => {
        if (!searchUrl || !resultsBody) {
            return;
        }

        currentPage = page;
        const url = new URL(searchUrl, window.location.origin);
        url.searchParams.set('page', String(page));
        if (searchField?.value) {
            url.searchParams.set('search_field', searchField.value);
        }
        if (keywordInput?.value.trim()) {
            url.searchParams.set('keyword', keywordInput.value.trim());
        }

        resultsBody.innerHTML = '<tr><td colspan="6" class="text-center">조회 중입니다...</td></tr>';
        if (paginationRoot) {
            paginationRoot.innerHTML = '';
        }

        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                throw new Error('member search failed');
            }
            const payload = await response.json();
            renderRows(payload.data ?? payload.members ?? [], payload.meta ?? null);
            renderPagination(payload.meta ?? null);
        } catch (_error) {
            resultsBody.innerHTML = '<tr><td colspan="6" class="text-center">조회 중 오류가 발생했습니다.</td></tr>';
        }
    };

    openButton?.addEventListener('click', openModal);
    closeButton?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    searchButton?.addEventListener('click', () => fetchMembers(1));
    keywordInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            fetchMembers(1);
        }
    });

    paginationRoot?.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('js-address-member-page')) {
            return;
        }
        event.preventDefault();
        if (target.closest('.disabled')) {
            return;
        }

        const page = Number(target.dataset.page ?? '1');
        if (!Number.isNaN(page) && page > 0 && page !== currentPage) {
            fetchMembers(page);
        }
    });

    resultsBody?.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('js-select-address-member')) {
            return;
        }

        addMember({
            id: target.dataset.id ?? '',
            name: target.dataset.name ?? '',
            login_id: target.dataset.loginId ?? '',
            phone_number: target.dataset.phone ?? '',
            email: target.dataset.email ?? '',
        });
        target.textContent = '추가됨';
        target.disabled = true;
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
