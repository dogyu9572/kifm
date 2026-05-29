(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-committee-bylaws-select]').forEach(function (select) {
            select.addEventListener('change', function () {
                var form = select.closest('[data-committee-bylaws-form]');

                if (form) {
                    form.submit();
                }
            });
        });
    });
})();
