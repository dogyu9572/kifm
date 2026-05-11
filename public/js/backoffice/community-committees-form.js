document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-community-committee-form');
    if (!form) {
        return;
    }

    const memberBody = form.querySelector('#committee-members-body');
    const memberCount = form.querySelector('#committee_member_count');
    const noLimitCheckbox = form.querySelector('#no_member_limit');
    const memberLimitInput = form.querySelector('#member_limit');
    const removeThumbnailButton = form.querySelector('[data-remove-existing-target="thumbnail"]');
    const deleteThumbnailCheckbox = document.getElementById('delete_thumbnail');
    const existingThumbnailItem = document.getElementById('bo-thumbnail-existing-item');

    const hideExistingThumbnail = () => {
        if (deleteThumbnailCheckbox) {
            deleteThumbnailCheckbox.checked = true;
        }
        if (existingThumbnailItem) {
            existingThumbnailItem.classList.add('bo-hidden');
        }
    };

    const reindexRows = () => {
        const rows = memberBody?.querySelectorAll('.js-committee-member-row') ?? [];
        rows.forEach((row, idx) => {
            const noCell = row.querySelector('td');
            if (noCell) {
                noCell.textContent = String(idx + 1);
            }

            row.querySelectorAll('input,select').forEach((input) => {
                const name = input.getAttribute('name');
                if (!name) {
                    return;
                }
                input.setAttribute('name', name.replace(/committee_members\[\d+\]/, `committee_members[${idx}]`));
            });
        });

        if (memberCount) {
            memberCount.textContent = `(${rows.length}명)`;
        }
    };

    const syncLimitInput = () => {
        if (!memberLimitInput || !noLimitCheckbox) {
            return;
        }
        const disabled = noLimitCheckbox.checked;
        memberLimitInput.disabled = disabled;
        if (disabled) {
            memberLimitInput.value = '';
        }
    };

    const ensureEmptyRow = () => {
        if (!memberBody) {
            return;
        }
        const rows = memberBody.querySelectorAll('.js-committee-member-row');
        const emptyRow = memberBody.querySelector('.js-committee-member-empty');

        if (rows.length === 0 && !emptyRow) {
            const tr = document.createElement('tr');
            tr.className = 'js-committee-member-empty';
            tr.innerHTML = '<td colspan="7" class="text-center">등록된 인원이 없습니다.</td>';
            memberBody.appendChild(tr);
        }
        if (rows.length > 0 && emptyRow) {
            emptyRow.remove();
        }
        reindexRows();
    };

    const createRow = (member) => {
        if (!memberBody) {
            return;
        }
        const exists = memberBody.querySelector(`.js-committee-member-row[data-member-id="${member.id}"]`);
        if (exists) {
            window.alert('이미 등록된 회원입니다.');
            return;
        }

        const tr = document.createElement('tr');
        tr.className = 'js-committee-member-row';
        tr.dataset.memberId = String(member.id);
        tr.innerHTML = `
            <td></td>
            <td>
                <select name="committee_members[0][role]" class="board-form-control board-form-control--max-xs">
                    <option value="chairman">위원장</option>
                    <option value="secretary">간사</option>
                    <option value="member" selected>위원</option>
                </select>
            </td>
            <td>
                ${member.name ?? '-'}
                <input type="hidden" name="committee_members[0][user_id]" value="${member.id}">
                <input type="hidden" name="committee_members[0][name]" value="${member.name ?? ''}">
                <input type="hidden" name="committee_members[0][email]" value="${member.email ?? ''}">
                <input type="hidden" name="committee_members[0][phone]" value="${member.phone_number ?? ''}">
                <input type="hidden" name="committee_members[0][organization]" value="${member.organization ?? ''}">
                <input type="hidden" name="committee_members[0][login_id]" value="${member.login_id ?? ''}">
            </td>
            <td>${member.email ?? '-'}</td>
            <td>${member.phone_number ?? '-'}</td>
            <td>${member.organization ?? '-'}</td>
            <td><button type="button" class="btn btn-outline-danger btn-sm js-remove-committee-member">삭제</button></td>
        `;
        memberBody.appendChild(tr);
        ensureEmptyRow();
    };

    form.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }
        if (target.classList.contains('js-remove-committee-member')) {
            const row = target.closest('.js-committee-member-row');
            row?.remove();
            ensureEmptyRow();
        }
    });

    form.addEventListener('bo-member-selected', (event) => {
        const customEvent = event;
        const detail = customEvent.detail ?? {};
        if (!detail.id) {
            return;
        }
        createRow(detail);
    });

    noLimitCheckbox?.addEventListener('change', syncLimitInput);
    removeThumbnailButton?.addEventListener('click', () => {
        hideExistingThumbnail();
    });

    syncLimitInput();
    ensureEmptyRow();
});

