(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-auto-submit-form]').forEach(function (select) {
            select.addEventListener('change', function () {
                var form = select.closest('form');

                if (form) {
                    form.submit();
                }
            });
        });
    });
})();
