/**
 * 백오피스 행사 통계 페이지 보조 스크립트
 * 라우팅: /backoffice/stats/events
 *
 * 역할:
 * - 검색 form 제출 시 기간 입력 검증 (시작일 > 종료일 인 경우 사용자 안내)
 *   (서버 측에서도 swap 처리하지만, 사용자에게 입력 실수 피드백 제공 목적)
 */
(function () {
    function init() {
        var container = document.getElementById('bo-stats-events-index');
        if (!container) {
            return;
        }

        var form = container.querySelector('.filter-form');
        var dateFrom = container.querySelector('#date_from');
        var dateTo = container.querySelector('#date_to');
        if (!form || !dateFrom || !dateTo) {
            return;
        }

        form.addEventListener('submit', function (event) {
            var from = (dateFrom.value || '').trim();
            var to = (dateTo.value || '').trim();
            if (from === '' || to === '') {
                return;
            }
            if (from > to) {
                event.preventDefault();
                alert('시작일이 종료일보다 늦습니다. 날짜를 다시 확인해주세요.');
                dateFrom.focus();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
