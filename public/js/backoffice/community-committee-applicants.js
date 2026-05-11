document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('.js-reject-modal');
    const form = document.querySelector('.js-reject-form');
    const targetInput = document.querySelector('.js-reject-target');
    const reasonInput = document.querySelector('.js-reject-reason');
    const title = document.querySelector('.js-reject-modal-title');
    const submitButton = document.querySelector('.js-reject-submit');

    if (!modal || !form || !targetInput || !reasonInput || !title || !submitButton) {
        return;
    }

    const openModal = (button) => {
        const actionType = button.dataset.actionType === 'cancel' ? 'cancel' : 'reject';
        const actionUrl = button.dataset.actionUrl || '';
        const applicantName = button.dataset.applicantName || '';

        form.action = actionUrl;
        targetInput.value = applicantName;
        reasonInput.value = '';

        if (actionType === 'cancel') {
            title.textContent = '승인 취소 사유 입력';
            submitButton.textContent = '승인 취소';
        } else {
            title.textContent = '반려 사유 입력';
            submitButton.textContent = '반려 저장';
        }

        modal.style.display = 'block';
        reasonInput.focus();
    };

    const closeModal = () => {
        modal.style.display = 'none';
        reasonInput.value = '';
    };

    document.addEventListener('click', (event) => {
        const openButton = event.target.closest('.js-open-reject-modal');
        if (openButton) {
            openModal(openButton);
            return;
        }

        if (event.target.closest('.js-close-reject-modal')) {
            closeModal();
            return;
        }

        if (event.target === modal) {
            closeModal();
            return;
        }

        const reasonButton = event.target.closest('.js-show-reason');
        if (reasonButton) {
            const reason = reasonButton.dataset.reason || '';
            window.alert(reason !== '' ? reason : '등록된 반려 사유가 없습니다.');
            return;
        }

        const approveButton = event.target.closest('.js-approve-button');
        if (approveButton) {
            const approved = window.confirm('해당 신청을 승인하시겠습니까?');
            if (!approved) {
                event.preventDefault();
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });

    form.addEventListener('submit', (event) => {
        const reason = reasonInput.value.trim();
        if (reason === '') {
            event.preventDefault();
            window.alert('사유를 입력해주세요.');
        }
    });
});
