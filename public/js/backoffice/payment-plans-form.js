/**
 * 결제 항목 등록/수정 폼: 결제 유형·회원 여부에 따른 입력 영역 표시 및 비활성 처리
 */
document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('bo-payment-category');
    const memberRadios = document.querySelectorAll('.bo-member-status-radio');
    const memberBlock = document.getElementById('bo-member-only-fields');
    const placeholder = document.getElementById('bo-amount-placeholder');
    const panelConference = document.getElementById('bo-amount-conference');
    const panelSingle = document.getElementById('bo-amount-single');
    const deleteBtn = document.getElementById('bo-payment-delete-btn');
    const deleteForm = document.getElementById('bo-payment-delete-form');

    const setInputsDisabled = (root, disabled) => {
        if (!root) {
            return;
        }
        root.querySelectorAll('input, select, textarea').forEach((el) => {
            el.disabled = disabled;
        });
    };

    const syncAmountPanels = () => {
        const category = categorySelect ? categorySelect.value : '';

        if (placeholder) {
            placeholder.classList.toggle('bo-hidden', category !== '');
        }
        if (panelConference) {
            const show = category === 'conference';
            panelConference.classList.toggle('bo-hidden', !show);
            setInputsDisabled(panelConference, !show);
        }
        if (panelSingle) {
            const show = category === 'membership' || category === 'education';
            panelSingle.classList.toggle('bo-hidden', !show);
            setInputsDisabled(panelSingle, !show);
        }
    };

    const syncMemberPanels = () => {
        const checked = document.querySelector('.bo-member-status-radio:checked');
        const isMember = checked && checked.value === 'member';

        if (memberBlock) {
            memberBlock.classList.toggle('bo-hidden', !isMember);
            setInputsDisabled(memberBlock, !isMember);
        }
    };

    const refresh = () => {
        syncAmountPanels();
        syncMemberPanels();
    };

    if (categorySelect) {
        categorySelect.addEventListener('change', refresh);
    }

    memberRadios.forEach((radio) => {
        radio.addEventListener('change', refresh);
    });

    refresh();

    if (deleteBtn && deleteForm) {
        deleteBtn.addEventListener('click', () => {
            const confirmed = window.confirm('정말 이 결제 항목을 삭제하시겠습니까?');
            if (!confirmed) {
                return;
            }
            deleteForm.submit();
        });
    }
});
