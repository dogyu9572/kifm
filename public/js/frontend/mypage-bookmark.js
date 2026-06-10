/**
 * 마이페이지 북마크: 선택 해제
 */
(function () {
    'use strict';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var wrap = document.querySelector('[data-mypage-bookmark]');
        if (!wrap) {
            return;
        }

        document.querySelectorAll('[data-bookmark-content-type-filter]').forEach(function (select) {
            select.addEventListener('change', function () {
                if (select.form) {
                    select.form.submit();
                }
            });
        });

        var destroyUrl = wrap.getAttribute('data-destroy-url');
        if (!destroyUrl) {
            return;
        }

        wrap.querySelectorAll('[data-bookmark-id]').forEach(function (button) {
            button.addEventListener('click', function () {
                var id = parseInt(button.getAttribute('data-bookmark-id') || '0', 10);
                if (!id || !window.confirm('북마크를 해제하시겠습니까?')) {
                    return;
                }

                fetch(destroyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ ids: [id] }),
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (!result.ok) {
                            window.alert(result.data.message || '북마크 해제에 실패했습니다.');
                            return;
                        }

                        var row = button.closest('tr');
                        if (row) {
                            row.remove();
                        }
                        window.alert(result.data.message || '북마크가 해제되었습니다.');
                    });
            });
        });
    });
})();
