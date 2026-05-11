/**
 * 게시글 댓글: 답글 폼 표시/숨김
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.bo-comment-reply-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-bo-reply-target');
            if (!id) {
                return;
            }
            var el = document.getElementById(id);
            if (!el) {
                return;
            }
            el.classList.toggle('d-none');
        });
    });

    document.querySelectorAll('.bo-comments-panel .js-delete-confirm-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('이 댓글을 삭제하시겠습니까?')) {
                e.preventDefault();
            }
        });
    });
});
