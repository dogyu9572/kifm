(function () {
    var pluginKey = '6e84442a-797f-47cb-b698-d3acdf060ef3';
    var w = window;

    if (w.ChannelIO) {
        w.console.error('ChannelIO script included twice.');
        return;
    }

    var ch = function () {
        ch.c(arguments);
    };

    ch.q = [];
    ch.c = function (args) {
        ch.q.push(args);
    };
    w.ChannelIO = ch;

    function loadChannelScript() {
        if (w.ChannelIOInitialized) {
            return;
        }

        w.ChannelIOInitialized = true;

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = true;
        script.src = 'https://cdn.channel.io/plugin/ch-plugin-web.js';

        var firstScript = document.getElementsByTagName('script')[0];
        if (firstScript.parentNode) {
            firstScript.parentNode.insertBefore(script, firstScript);
        }
    }

    if (document.readyState === 'complete') {
        loadChannelScript();
    } else {
        w.addEventListener('DOMContentLoaded', loadChannelScript);
        w.addEventListener('load', loadChannelScript);
    }

    w.ChannelIO('boot', {
        pluginKey: pluginKey,
        hideChannelButtonOnBoot: true
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn_chatbot').forEach(function (button) {
            button.addEventListener('click', function () {
                w.ChannelIO('showMessenger');
            });
        });
    });
})();
