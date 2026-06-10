(function () {
    function showInitialValidationMessage(form) {
        var message = form.getAttribute('data-validation-message');
        if (message) {
            window.alert(message);
        }
    }

    function bindRequiredTextAlert(form, fields) {
        form.addEventListener('submit', function (event) {
            for (var i = 0; i < fields.length; i += 1) {
                var field = form.querySelector('[name="' + fields[i].name + '"]');
                if (!field || field.value.trim() !== '') {
                    continue;
                }

                window.alert(fields[i].message);
                field.focus();
                event.preventDefault();
                return;
            }
        });
    }

    function bindAttachmentInputs() {
        document.querySelectorAll('.input_attach input[type="file"]').forEach(function (input) {
            var container = input.closest('.input_attach');
            if (!container) {
                return;
            }

            var text = container.querySelector('p');
            if (!text) {
                return;
            }

            var defaultText = text.textContent;

            input.addEventListener('change', function () {
                var file = input.files && input.files[0] ? input.files[0] : null;
                text.textContent = '';

                if (!file) {
                    text.textContent = defaultText;
                    container.classList.remove('in');
                    return;
                }

                container.classList.add('in');

                var name = document.createElement('span');
                name.className = 'attach_file_name';
                name.textContent = file.name + ' ';

                var button = document.createElement('button');
                button.type = 'button';
                button.textContent = '삭제';
                button.addEventListener('click', function () {
                    input.value = '';
                    text.textContent = defaultText;
                    container.classList.remove('in');
                });

                text.appendChild(name);
                text.appendChild(button);
            });
        });
    }

    function closeOptionAreas(except) {
        document.querySelectorAll('.option_area ul.is-open').forEach(function (menu) {
            if (except && menu === except) {
                return;
            }

            menu.classList.remove('is-open');
        });
    }

    function bindOptionAreaToggle() {
        document.querySelectorAll('.option_area .btn_option').forEach(function (button) {
            var target = button.nextElementSibling;
            if (!target || target.tagName.toLowerCase() !== 'ul') {
                return;
            }

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                closeOptionAreas(target);
                target.classList.toggle('is-open');
            });
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.option_area')) {
                closeOptionAreas();
            }
        });
    }

    function setCommentEditMode(id, editing) {
        var body = document.querySelector('[data-comment-body="' + id + '"]');
        var form = document.querySelector('[data-comment-edit-form][data-comment-id="' + id + '"]');

        if (!body || !form) {
            return;
        }

        body.hidden = editing;
        form.hidden = !editing;

        if (editing) {
            var input = form.querySelector('[name="content"]');
            if (input) {
                input.focus();
            }
        }
    }

    function bindCommentEditForms() {
        document.querySelectorAll('[data-comment-edit-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var id = button.getAttribute('data-comment-id');
                closeOptionAreas();
                setCommentEditMode(id, true);
            });
        });

        document.querySelectorAll('[data-comment-edit-cancel]').forEach(function (button) {
            button.addEventListener('click', function () {
                var id = button.getAttribute('data-comment-id');
                setCommentEditMode(id, false);
            });
        });
    }

    function bindCommentDeleteForms() {
        document.querySelectorAll('[data-comment-delete-form]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                closeOptionAreas();
                if (!window.confirm('댓글을 삭제하시겠습니까?')) {
                    event.preventDefault();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-subcommittee-discussion-form]').forEach(function (form) {
            showInitialValidationMessage(form);
            bindRequiredTextAlert(form, [
                { name: 'title', message: '토론 주제를 입력해 주세요.' },
                { name: 'content', message: '내용을 입력해 주세요.' },
                { name: 'captcha', message: '자동등록방지 문자를 입력해 주세요.' }
            ]);
        });

        document.querySelectorAll('[data-subcommittee-discussion-comment-form]').forEach(function (form) {
            showInitialValidationMessage(form);
            bindRequiredTextAlert(form, [
                { name: 'content', message: '답글 내용을 작성해주세요.' }
            ]);
        });

        var chatArea = document.querySelector('.chat_area');
        if (chatArea) {
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        bindAttachmentInputs();
        bindOptionAreaToggle();
        bindCommentEditForms();
        bindCommentDeleteForms();
    });
}());
