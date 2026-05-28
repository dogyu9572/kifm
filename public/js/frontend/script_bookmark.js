/**
 * 공개 화면 북마크 저장/해제
 */
(function () {
    'use strict';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function setState(button, bookmarked) {
        var defaultLabel = button.getAttribute('data-default-label') || '이 게시글을 북마크에 추가';
        button.classList.toggle('on', bookmarked);
        button.setAttribute('aria-pressed', bookmarked ? 'true' : 'false');
        button.setAttribute('aria-label', bookmarked ? '북마크 취소' : defaultLabel);
    }

    function syncSameButtons(source, bookmarked) {
        var type = source.getAttribute('data-content-type');
        var id = source.getAttribute('data-content-id');
        document.querySelectorAll('[data-bookmark-toggle]').forEach(function (button) {
            if (button.getAttribute('data-content-type') === type && button.getAttribute('data-content-id') === id) {
                setState(button, bookmarked);
            }
        });
    }

    function payload(button) {
        return {
            content_type: button.getAttribute('data-content-type'),
            content_id: parseInt(button.getAttribute('data-content-id') || '0', 10),
            title: button.getAttribute('data-title') || '',
            menu_label: button.getAttribute('data-menu-label') || '',
            url: button.getAttribute('data-url') || window.location.href,
        };
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-bookmark-toggle]');
        if (!button) {
            return;
        }
        event.preventDefault();

        var toggleUrl = button.getAttribute('data-bookmark-url');
        var data = payload(button);

        if (!toggleUrl || !data.content_type || !data.content_id) {
            window.alert('북마크할 수 없는 항목입니다.');
            return;
        }

        fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
        })
            .then(function (response) {
                if (response.status === 401 || response.status === 403) {
                    var loginUrl = button.getAttribute('data-login-url') || '/member/login';
                    if (window.confirm('로그인 후 북마크를 이용할 수 있습니다. 로그인하시겠습니까?')) {
                        window.location.href = loginUrl;
                    }
                    return null;
                }

                return response.json().then(function (json) {
                    return { ok: response.ok, data: json };
                }).catch(function () {
                    return {
                        ok: false,
                        data: { message: '로그인 후 북마크를 이용할 수 있습니다.' },
                    };
                });
            })
            .then(function (result) {
                if (!result) {
                    return;
                }
                if (!result.ok) {
                    window.alert(result.data.message || '북마크 처리에 실패했습니다.');
                    return;
                }

                var isBookmarked = !!result.data.bookmarked;
                syncSameButtons(button, isBookmarked);
                if (isBookmarked) {
                    window.alert('북마크로 추가되었습니다.');
                }
            })
            .catch(function () {
                window.alert('북마크 처리 중 오류가 발생했습니다.');
            });
    });
})();
