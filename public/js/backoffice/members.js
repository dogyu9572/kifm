document.addEventListener('DOMContentLoaded', function () {
    bindMemberDeleteActions();

    if (typeof jQuery === 'undefined') {
        return;
    }

    const $ = jQuery;

    document.querySelectorAll('.bo-member-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const msg = form.getAttribute('data-confirm') || '삭제하시겠습니까?';
            if (!window.confirm(msg)) {
                event.preventDefault();
            }
        });
    });

    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            if (this.form) {
                this.form.submit();
            }
        });
    }

    function toYmd(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + day;
    }

    document.querySelectorAll('.bo-date-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const preset = this.getAttribute('data-preset');
            const start = document.getElementById('date_start');
            const end = document.getElementById('date_end');
            if (!start || !end) {
                return;
            }
            const today = new Date();
            if (preset === 'all') {
                start.value = '';
                end.value = '';
                return;
            }
            if (preset === 'today') {
                const v = toYmd(today);
                start.value = v;
                end.value = v;
                return;
            }
            if (preset === 'yesterday') {
                const d = new Date(today);
                d.setDate(d.getDate() - 1);
                const v = toYmd(d);
                start.value = v;
                end.value = v;
                return;
            }
            if (preset === 'week') {
                const d = new Date(today);
                d.setDate(d.getDate() - 6);
                start.value = toYmd(d);
                end.value = toYmd(today);
                return;
            }
            if (preset === 'month') {
                const d = new Date(today.getFullYear(), today.getMonth(), 1);
                start.value = toYmd(d);
                end.value = toYmd(today);
                return;
            }
            if (preset === 'year') {
                const d = new Date(today.getFullYear(), 0, 1);
                start.value = toYmd(d);
                end.value = toYmd(today);
            }
        });
    });

    function parseDateValue(value) {
        if (!value) {
            return null;
        }
        const parsed = new Date(value + 'T00:00:00');
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function applyMembershipPaymentFilter() {
        const filter = document.querySelector('[data-history-filter="membership-payments"]');
        if (!filter) {
            return;
        }

        const startInput = filter.querySelector('.bo-history-date-start');
        const endInput = filter.querySelector('.bo-history-date-end');
        const start = parseDateValue(startInput ? startInput.value : '');
        const end = parseDateValue(endInput ? endInput.value : '');
        document.querySelectorAll('[data-membership-payment-row]').forEach(function (row) {
            const rowDate = parseDateValue(row.getAttribute('data-history-date') || '');
            const visible = !rowDate || ((!start || rowDate >= start) && (!end || rowDate <= end));
            row.classList.toggle('d-none', !visible);
        });
    }

    document.querySelectorAll('.bo-history-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const filter = this.closest('[data-history-filter="membership-payments"]');
            if (!filter) {
                return;
            }
            const months = Number(this.getAttribute('data-months') || '0');
            const end = new Date();
            const start = new Date(end);
            start.setMonth(start.getMonth() - months);
            const startInput = filter.querySelector('.bo-history-date-start');
            const endInput = filter.querySelector('.bo-history-date-end');
            if (startInput) {
                startInput.value = toYmd(start);
            }
            if (endInput) {
                endInput.value = toYmd(end);
            }
        });
    });

    document.querySelectorAll('.bo-history-filter-apply').forEach(function (btn) {
        btn.addEventListener('click', applyMembershipPaymentFilter);
    });

    document.querySelectorAll('.bo-history-filter-reset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const filter = this.closest('[data-history-filter="membership-payments"]');
            if (!filter) {
                return;
            }
            const startInput = filter.querySelector('.bo-history-date-start');
            const endInput = filter.querySelector('.bo-history-date-end');
            if (startInput) {
                startInput.value = '';
            }
            if (endInput) {
                endInput.value = '';
            }
            applyMembershipPaymentFilter();
        });
    });

    $(document).on('click', '#btnSearchAddress', function () {
        if (typeof daum === 'undefined') return;
        new daum.Postcode({
            oncomplete: function (data) {
                $('#address_postcode').val(data.zonecode);
                $('#address_base').val(data.address);
                $('#address_detail').focus();
            },
        }).open();
    });

    $(document).on('click', '#btnSearchWorkplaceAddress', function () {
        if (typeof daum === 'undefined') return;
        new daum.Postcode({
            oncomplete: function (data) {
                $('#workplace_zipcode').val(data.zonecode);
                $('#workplace_address').val(data.address);
                $('#workplace_address_detail').focus();
            },
        }).open();
    });

    $(document).on('input', '#school_name_direct', function () {
        if ($(this).val()) {
            $('#school_name').val($(this).val());
        }
    });

    function toggleSchoolPopup(isOpen) {
        const popup = document.getElementById('searchSchool');
        if (!popup) {
            return;
        }

        popup.classList.toggle('is-open', isOpen);
    }

    function renderSchoolList(schools, message) {
        const list = document.getElementById('popSchoolList');
        if (!list) {
            return;
        }

        if (!Array.isArray(schools) || schools.length === 0) {
            list.innerHTML = '<p class="no_data">' + (message || '검색 결과가 없습니다.') + '</p>';
            return;
        }

        list.innerHTML = schools
            .map(function (schoolName) {
                return (
                    '<button type="button" class="btn btn-light btn-sm btn-school-select" data-school-name="' +
                    schoolName +
                    '">' +
                    schoolName +
                    '</button>'
                );
            })
            .join('');
    }

    function searchSchool() {
        const keyword = ($('#popSchoolKeyword').val() || '').trim();
        if (!keyword) {
            renderSchoolList([], '학교명을 입력해주세요.');
            return;
        }

        fetch('/backoffice/members/search-school?keyword=' + encodeURIComponent(keyword), {
            method: 'GET',
            headers: { Accept: 'application/json' },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                renderSchoolList(result.schools || [], result.message || '');
            })
            .catch(function () {
                renderSchoolList([], '학교 검색 중 오류가 발생했습니다.');
            });
    }

    $(document).on('click', '#btnSearchSchool', function () {
        const currentSchoolName = ($('#school_name').val() || '').trim();
        if (currentSchoolName) {
            $('#popSchoolKeyword').val(currentSchoolName);
        }
        toggleSchoolPopup(true);
    });

    $(document).on('click', '[data-school-popup-close]', function () {
        toggleSchoolPopup(false);
    });

    $(document).on('click', '#popSchoolSearch', function () {
        searchSchool();
    });

    $(document).on('keydown', '#popSchoolKeyword', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchSchool();
        }
    });

    $(document).on('click', '.btn-school-select', function () {
        const schoolName = $(this).data('school-name');
        if (!schoolName) {
            return;
        }

        $('#school_name').val(schoolName);
        $('#school_name_direct').val('');
        toggleSchoolPopup(false);
    });

    $(document).on('click', '#popSchoolRegister', function () {
        const schoolName = ($('#popSchoolKeyword').val() || '').trim();
        if (!schoolName) {
            alert('등록할 학교명을 입력해주세요.');
            return;
        }

        $('#school_name').val(schoolName);
        $('#school_name_direct').val(schoolName);
        toggleSchoolPopup(false);
    });

    $(document).on('click', '#btnCheckEmail', function () {
        const email = $('#email').val();
        const excludeId = $(this).data('exclude-id');
        if (!email) return alert('이메일을 입력해주세요.');
        checkDuplicate('/backoffice/members/check-email', { email, exclude_id: excludeId }, '#emailCheckResult', '#email');
    });

    $(document).on('click', '#btnCheckPhone', function () {
        const phone = $('#phone_number').val();
        const excludeId = $(this).data('exclude-id');
        if (!phone) return alert('휴대폰번호를 입력해주세요.');
        checkDuplicate('/backoffice/members/check-phone', { phone, exclude_id: excludeId }, '#phoneCheckResult', '#phone_number');
    });

    $(document).on('change', '#select-all', function () {
        $('.bo-row-checkbox').prop('checked', $(this).prop('checked'));
    });

    $(document).on('change', '.bo-row-checkbox', function () {
        const total = $('.bo-row-checkbox').length;
        const checked = $('.bo-row-checkbox:checked').length;
        $('#select-all').prop('checked', total > 0 && total === checked);
    });

    $(document).on('click', '#btnExport', function () {
        const formData = new FormData(document.getElementById('searchForm'));
        const params = new URLSearchParams(formData);
        window.location.href = '/backoffice/members/export?' + params.toString();
    });
});

function bindMemberDeleteActions() {
    document.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('.btn-delete-member');
        if (deleteButton) {
            event.preventDefault();
            submitMemberDelete(deleteButton);
            return;
        }

        const bulkDeleteButton = event.target.closest('#btnDeleteMultiple');
        if (bulkDeleteButton) {
            event.preventDefault();
            submitSelectedMemberDelete();
        }
    });
}

function submitMemberDelete(button) {
    const actionUrl = button.getAttribute('data-url');
    if (!actionUrl) {
        alert('탈퇴 처리 URL을 찾을 수 없습니다.');
        return;
    }
    if (!confirm('정말로 탈퇴 처리하시겠습니까?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = actionUrl;
    form.style.display = 'none';
    form.appendChild(hiddenInput('_token', csrfToken()));
    form.appendChild(hiddenInput('_method', 'DELETE'));
    document.body.appendChild(form);
    form.submit();
}

function submitSelectedMemberDelete() {
    const ids = Array.from(document.querySelectorAll('.bo-row-checkbox:checked')).map(function (checkbox) {
        return checkbox.value;
    });

    if (ids.length === 0) {
        alert('삭제할 회원을 선택해주세요.');
        return;
    }
    if (!confirm('선택한 ' + ids.length + '명의 회원을 탈퇴 처리하시겠습니까?')) {
        return;
    }

    postJson('/backoffice/members/delete-multiple', { ids: ids })
        .then(function (result) {
            if (result && result.success === false) {
                alert(result.message || '탈퇴 처리 중 오류가 발생했습니다.');
                return;
            }
            location.reload();
        })
        .catch(function () {
            alert('탈퇴 처리 중 오류가 발생했습니다.');
        });
}

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function hiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}

function jsonHeaders() {
    return {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        Accept: 'application/json',
    };
}

function postJson(url, data) {
    const payload = Object.assign({}, data, { _token: csrfToken() });

    return fetch(url, {
        method: 'POST',
        headers: jsonHeaders(),
        body: JSON.stringify(payload),
    }).then((r) => r.json());
}

function checkDuplicate(url, payload, resultSelector, focusSelector) {
    postJson(url, payload)
        .then(function (result) {
            const target = document.querySelector(resultSelector);
            if (!target) return;
            target.textContent = result.message;
            target.className = 'check-result ' + (result.available ? 'success' : 'error');
            if (!result.available) {
                const focusTarget = document.querySelector(focusSelector);
                if (focusTarget) {
                    focusTarget.focus();
                }
            }
        })
        .catch(function () {
            alert('중복 확인 중 오류가 발생했습니다.');
        });
}
