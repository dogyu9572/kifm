(function () {
    'use strict';

    function listUrl(form) {
        var params = new URLSearchParams(new FormData(form));
        Array.from(params.keys()).forEach(function (key) {
            if (params.get(key) === '') {
                params.delete(key);
            }
        });

        return form.getAttribute('action') + (params.toString() ? '?' + params.toString() : '');
    }

    function replaceContentFrom(url, focusSelector) {
        var body = document.querySelector('.academic_event_body');
        if (!body) {
            window.location.href = url;
            return;
        }

        body.classList.add('is-loading');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to load conferences.');
                }
                return response.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nextBody = doc.querySelector('.academic_event_body');
                var currentPagination = document.querySelector('.board-pagination');
                var nextPagination = doc.querySelector('.board-pagination');

                if (!nextBody) {
                    throw new Error('Conference list not found.');
                }

                body.replaceWith(nextBody);
                if (currentPagination && nextPagination) {
                    currentPagination.replaceWith(nextPagination);
                } else if (currentPagination && !nextPagination) {
                    currentPagination.remove();
                } else if (!currentPagination && nextPagination) {
                    nextBody.insertAdjacentElement('afterend', nextPagination);
                }

                window.history.pushState({}, '', url);
                bindEvents();

                var focusTarget = focusSelector ? document.querySelector(focusSelector) : null;
                if (focusTarget) {
                    focusTarget.focus();
                }
            })
            .catch(function () {
                window.location.href = url;
            })
            .finally(function () {
                document.querySelector('.academic_event_body')?.classList.remove('is-loading');
            });
    }

    function bindEvents() {
        document.querySelectorAll('[data-closed-conference]').forEach(function (link) {
            if (link.dataset.closedBound === '1') {
                return;
            }
            link.dataset.closedBound = '1';
            link.addEventListener('click', function (event) {
                event.preventDefault();
                window.alert('마감된 행사입니다.');
            });
        });

        document.querySelectorAll('[data-auto-submit-form]').forEach(function (select) {
            if (select.dataset.ajaxBound === '1') {
                return;
            }
            select.dataset.ajaxBound = '1';
            select.addEventListener('change', function () {
                var form = select.closest('form');

                if (form) {
                    replaceContentFrom(listUrl(form), '#' + select.id);
                }
            });
        });

        document.querySelectorAll('.academic_event_body form.board_top').forEach(function (form) {
            if (form.dataset.ajaxBound === '1') {
                return;
            }
            form.dataset.ajaxBound = '1';
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                replaceContentFrom(listUrl(form), '#event-search');
            });
        });

        document.querySelectorAll('.academic_event_body .tabs a, .board-pagination a').forEach(function (link) {
            if (link.dataset.ajaxBound === '1') {
                return;
            }
            link.dataset.ajaxBound = '1';
            link.addEventListener('click', function (event) {
                var url = link.getAttribute('href');
                if (!url || url === '#') {
                    return;
                }

                event.preventDefault();
                replaceContentFrom(url, null);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
    });
})();
