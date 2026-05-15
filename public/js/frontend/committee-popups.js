/**
 * 산하위원회 위원회 팝업: 새창(normal) 자동 오픈, 레이어 위치·크기(data-* 반영)
 */
(function () {
    'use strict';

    function applyCommitteeLayerLayout() {
        document.querySelectorAll('.popup-layer.committee-scope-popup').forEach(function (el) {
            var w = el.getAttribute('data-width');
            var h = el.getAttribute('data-height');
            var top = el.getAttribute('data-top');
            var left = el.getAttribute('data-left');
            el.style.position = 'absolute';
            el.style.zIndex = '99999';
            if (w) {
                el.style.width = w + 'px';
            }
            if (h) {
                el.style.height = 'auto';
            }
            if (top) {
                el.style.top = top + 'px';
            }
            if (left) {
                el.style.left = left + 'px';
            }
        });
    }

    function getCookie(name) {
        var value = '; ' + document.cookie;
        var parts = value.split('; ' + name + '=');
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    function isPopupHiddenForToday(popupId) {
        var v = getCookie('popup_hide_' + popupId);
        return v === '1' || v === 'true';
    }

    function openCommitteeWindowPopups() {
        document.querySelectorAll('.committee-window-popup-launcher').forEach(function (el) {
            var url = el.getAttribute('data-committee-window-popup-url');
            var name = el.getAttribute('data-committee-window-popup-name') || 'committee_popup';
            var feat = el.getAttribute('data-committee-window-popup-features') || '';
            var idMatch = name.match(/^popup_(\d+)$/);
            var popupId = idMatch ? idMatch[1] : null;
            if (popupId && isPopupHiddenForToday(popupId)) {
                el.remove();
                return;
            }
            if (url) {
                window.open(url, name, feat);
            }
            el.remove();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyCommitteeLayerLayout();
        openCommitteeWindowPopups();
    });
})();
