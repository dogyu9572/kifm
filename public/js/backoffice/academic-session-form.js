document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#bo-academic-session-form');
    if (!form) {
        return;
    }
    form.addEventListener('submit', () => {
        if (typeof window.syncBackofficeCKEditorFields === 'function') {
            window.syncBackofficeCKEditorFields(form);
        }
    });
});
