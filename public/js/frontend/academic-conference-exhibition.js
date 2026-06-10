(function () {
    document.querySelectorAll('[data-exhibition-tabs]').forEach(function (tabs) {
        const links = tabs.querySelectorAll('[data-exhibition-tab]');
        const panels = document.querySelectorAll('[data-exhibition-panel]');

        links.forEach(function (link) {
            link.addEventListener('click', function (event) {
                const targetId = link.getAttribute('href');
                if (!targetId || !targetId.startsWith('#')) {
                    return;
                }

                const targetPanel = document.querySelector(targetId);
                if (!targetPanel) {
                    return;
                }

                event.preventDefault();
                links.forEach(function (item) {
                    item.closest('li')?.classList.remove('on');
                });
                panels.forEach(function (panel) {
                    panel.hidden = panel !== targetPanel;
                });
                link.closest('li')?.classList.add('on');
            });
        });
    });
})();
