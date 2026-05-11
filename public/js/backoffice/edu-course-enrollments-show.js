document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bo-edu-course-enrollment-form');
    const paymentMethodInputs = form?.querySelectorAll('input[name="payment_method"]') ?? [];
    const bankRow = form?.querySelector('.bo-bank-row');
    const receiptIssueInputs = form?.querySelectorAll('input[name="receipt_issue"]') ?? [];
    const receiptRow = form?.querySelector('.bo-receipt-row');

    const syncBankRow = () => {
        const selected = Array.from(paymentMethodInputs).find((input) => input.checked);
        const visible = selected?.value === 'bank_transfer';
        if (bankRow) {
            bankRow.classList.toggle('bo-hidden', !visible);
        }
    };

    const syncReceiptRow = () => {
        const selected = Array.from(receiptIssueInputs).find((input) => input.checked);
        const visible = selected?.value === 'YES';
        if (receiptRow) {
            receiptRow.classList.toggle('bo-hidden', !visible);
        }
    };

    paymentMethodInputs.forEach((input) => {
        input.addEventListener('change', syncBankRow);
    });
    receiptIssueInputs.forEach((input) => {
        input.addEventListener('change', syncReceiptRow);
    });

    syncBankRow();
    syncReceiptRow();
});

