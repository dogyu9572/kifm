document.addEventListener('DOMContentLoaded', () => {
    const escapeAttr = (value) =>
        String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    const selectorRoots = document.querySelectorAll('.js-member-selector');

    selectorRoots.forEach((root) => {
        if (root.dataset.readonly === '1') {
            return;
        }

        const searchUrl = root.dataset.searchUrl;
        const modal = root.closest('.bo-form-section')?.nextElementSibling?.classList.contains('js-member-modal')
            ? root.closest('.bo-form-section').nextElementSibling
            : document.querySelector('.js-member-modal');

        if (!searchUrl || !modal) {
            return;
        }

        const memberIdInput = root.querySelector('.js-member-id');
        const memberLabelInput = root.querySelector('.js-member-label');
        const memberDisplayInput = root.querySelector('.js-member-display');
        const openButton = root.querySelector('.js-open-member-modal');
        const closeButton = modal.querySelector('.js-close-member-modal');
        const searchButton = modal.querySelector('.js-search-member');
        const searchField = modal.querySelector('.js-member-search-field');
        const keywordInput = modal.querySelector('.js-member-keyword');
        const resultsBody = modal.querySelector('.js-member-results');
        const paginationRoot = modal.querySelector('.js-member-pagination');
        let currentPage = 1;

        const closeModal = () => {
            modal.style.display = 'none';
        };

        const openModal = () => {
            modal.style.display = 'block';
            keywordInput.focus();
        };

        const resetFilters = () => {
            if (searchField) {
                searchField.value = 'all';
            }
            if (keywordInput) {
                keywordInput.value = '';
            }
        };

        const renderRows = (members, meta) => {
            if (!Array.isArray(members) || members.length === 0) {
                resultsBody.innerHTML = '<tr><td colspan="6" class="text-center">검색 결과가 없습니다.</td></tr>';
                return;
            }

            const startNo = ((Number(meta?.current_page ?? 1) - 1) * Number(meta?.per_page ?? 10)) + 1;
            resultsBody.innerHTML = members.map((member, idx) => {
                const label = `${member.name ?? ''}`;
                const indexNo = startNo + idx;
                const phone = escapeAttr(member.phone_number ?? '');
                const email = escapeAttr(member.email ?? '');
                const loginId = escapeAttr(member.login_id ?? '');
                const licenseNumber = escapeAttr(member.license_number ?? '');
                const organization = escapeAttr(member.organization ?? '');
                const position = escapeAttr(member.position ?? '');
                const name = escapeAttr(member.name ?? '');
                return `
                    <tr>
                        <td>${indexNo}</td>
                        <td>${member.name ?? '-'}</td>
                        <td>${member.login_id ?? '-'}</td>
                        <td>${member.phone_number ?? '-'}</td>
                        <td>${member.email ?? '-'}</td>
                        <td><button type="button" class="btn btn-sm btn-primary js-select-member"
                            data-id="${member.id}"
                            data-label="${escapeAttr(label)}"
                            data-name="${name}"
                            data-organization="${organization}"
                            data-position="${position}"
                            data-phone="${phone}"
                            data-email="${email}"
                            data-login-id="${loginId}"
                            data-license-number="${licenseNumber}"
                        >선택</button></td>
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
                const activeClass = page === current ? 'active' : '';
                numberButtons.push(`<li class="page-item ${activeClass}"><a class="page-link js-member-page" data-page="${page}" href="#">${page}</a></li>`);
            }

            paginationRoot.innerHTML = `
                <ul class="pagination">
                    <li class="page-item ${current <= 1 ? 'disabled' : ''}">
                        <a class="page-link js-member-page" data-page="${Math.max(1, current - 1)}" href="#" aria-label="이전 페이지">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    ${numberButtons.join('')}
                    <li class="page-item ${current >= last ? 'disabled' : ''}">
                        <a class="page-link js-member-page" data-page="${Math.min(last, current + 1)}" href="#" aria-label="다음 페이지">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            `;
        };

        const fetchMembers = async (page = 1) => {
            currentPage = page;
            const keyword = keywordInput.value.trim();
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('page', String(page));
            if (searchField && searchField.value) {
                url.searchParams.set('search_field', searchField.value);
            }
            if (keyword !== '') {
                url.searchParams.set('keyword', keyword);
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
                    throw new Error('검색 요청 실패');
                }
                const payload = await response.json();
                renderRows(payload.data ?? [], payload.meta ?? null);
                renderPagination(payload.meta ?? null);
            } catch (error) {
                resultsBody.innerHTML = '<tr><td colspan="6" class="text-center">조회 중 오류가 발생했습니다.</td></tr>';
                if (paginationRoot) {
                    paginationRoot.innerHTML = '';
                }
            }
        };

        openButton?.addEventListener('click', () => {
            resetFilters();
            openModal();
            fetchMembers(1);
        });

        closeButton?.addEventListener('click', closeModal);

        modal.addEventListener('click', (event) => {
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
            if (!(target instanceof HTMLElement) || !target.classList.contains('js-member-page')) {
                return;
            }
            event.preventDefault();
            if (target.closest('.disabled')) {
                return;
            }

            const page = Number(target.dataset.page ?? '1');
            if (Number.isNaN(page) || page < 1 || page === currentPage) {
                return;
            }

            fetchMembers(page);
        });

        resultsBody?.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement) || !target.classList.contains('js-select-member')) {
                return;
            }

            memberIdInput.value = target.dataset.id ?? '';
            memberLabelInput.value = target.dataset.label ?? '';
            memberDisplayInput.value = target.dataset.label ?? '';
            root.dispatchEvent(
                new CustomEvent('bo-member-selected', {
                    bubbles: true,
                    detail: {
                        id: target.dataset.id ?? '',
                        label: target.dataset.label ?? '',
                        name: target.dataset.name ?? '',
                        organization: target.dataset.organization ?? '',
                        position: target.dataset.position ?? '',
                        phone_number: target.dataset.phone ?? '',
                        email: target.dataset.email ?? '',
                        login_id: target.dataset.loginId ?? '',
                        license_number: target.dataset.licenseNumber ?? '',
                    },
                }),
            );
            closeModal();
        });
    });
});

