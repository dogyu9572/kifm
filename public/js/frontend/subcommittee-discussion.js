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
    });
}());
