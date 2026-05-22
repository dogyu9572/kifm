(function ($) {
    function initMarquee() {
        $('[data-kifm-marquee]').each(function () {
            var $container = $(this);
            var $wrapper = $container.find('.marquee_wrapper');
            var $group = $container.find('.marquee_group');

            if (! $wrapper.length || ! $group.length) {
                return;
            }

            while ($group.width() < $(window).width()) {
                $group.append($group.children().first().clone());
            }

            $wrapper.append($group.clone());

            var speed = 10;
            var currentX = 0;

            function scrollMarquee() {
                var groupWidth = $group.outerWidth();
                var remainingDistance = groupWidth + currentX;
                var duration = remainingDistance * speed;

                $({ moveX: currentX }).animate({ moveX: -groupWidth }, {
                    duration: duration,
                    easing: 'linear',
                    step: function (now) {
                        currentX = now;
                        $wrapper.css('transform', 'translateX(' + now + 'px)');
                    },
                    complete: function () {
                        currentX = 0;
                        scrollMarquee();
                    }
                });
            }

            scrollMarquee();

            $container.on('mouseenter', function () {
                $({ moveX: currentX }).stop();
                $wrapper.stop();
            }).on('mouseleave', function () {
                scrollMarquee();
            });
        });
    }

    function initCopyAddress() {
        $('[data-copy-address]').on('click', function () {
            var textToCopy = $(this).siblings('.copy_txt').text().trim();
            var $temp = $('<textarea>');

            $('body').append($temp);
            $temp.val(textToCopy).select();

            try {
                if (document.execCommand('copy')) {
                    alert('주소가 복사되었습니다.');
                } else {
                    alert('복사에 실패했습니다.');
                }
            } catch (err) {
                alert('이 브라우저에서는 복사를 지원하지 않습니다.');
            }

            $temp.remove();
        });
    }

    function initRoughMap() {
        var $map = $('[data-kifm-roughmap]').first();

        if (! $map.length) {
            return;
        }

        function renderKakaoMap() {
            var address = String($map.data('kifmMapAddress') || '').trim();
            var javascriptKey = String($map.data('kifmMapKey') || '').trim();

            if (! address || ! javascriptKey) {
                renderRoughMap();
                return;
            }

            loadKakaoMapScript(javascriptKey)
                .then(function () {
                    if (! window.kakao || ! window.kakao.maps || ! window.kakao.maps.services) {
                        renderRoughMap();
                        return;
                    }

                    window.kakao.maps.load(function () {
                        var geocoder = new window.kakao.maps.services.Geocoder();
                        geocoder.addressSearch(address, function (result, status) {
                            if (status !== window.kakao.maps.services.Status.OK || ! result.length) {
                                renderRoughMap();
                                return;
                            }

                            var coords = new window.kakao.maps.LatLng(result[0].y, result[0].x);
                            $map.empty().append('<div class="kifm_kakao_map_canvas"></div>');

                            var map = new window.kakao.maps.Map($map.find('.kifm_kakao_map_canvas')[0], {
                                center: coords,
                                level: 3
                            });
                            var marker = new window.kakao.maps.Marker({
                                position: coords
                            });
                            marker.setMap(map);
                        });
                    });
                })
                .catch(function () {
                    renderRoughMap();
                });
        }

        function loadKakaoMapScript(javascriptKey) {
            if (window.kakao && window.kakao.maps && window.kakao.maps.services) {
                return Promise.resolve();
            }

            return new Promise(function (resolve, reject) {
                var existing = document.querySelector('script[data-kakao-map-sdk]');

                if (existing) {
                    existing.addEventListener('load', resolve);
                    existing.addEventListener('error', reject);
                    return;
                }

                var script = document.createElement('script');
                script.dataset.kakaoMapSdk = 'true';
                script.src = 'https://dapi.kakao.com/v2/maps/sdk.js?appkey='
                    + encodeURIComponent(javascriptKey)
                    + '&autoload=false&libraries=services';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        function renderRoughMap() {
            $map.empty();
            renderRoughMapLoader();
        }

        function renderMap() {
            if (! window.daum || ! window.daum.roughmap) {
                return;
            }

            new window.daum.roughmap.Lander({
                timestamp: $map.data('timestamp'),
                key: $map.data('key'),
                mapWidth: String($map.data('width') || 800),
                mapHeight: String($map.data('height') || 480)
            }).render();
        }

        function renderRoughMapLoader() {
            if (window.daum && window.daum.roughmap) {
                renderMap();
                return;
            }

            var script = document.createElement('script');
            script.charset = 'UTF-8';
            script.src = 'https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js';
            script.onload = renderMap;
            document.head.appendChild(script);
        }

        renderKakaoMap();
    }

    function initLegacyRoughMap() {
        if ($('[data-kifm-roughmap]').length) {
            return;
        }

        var $map = $('#daumRoughmapContainer1777006266657').first();
        if (! $map.length) {
            return;
        }

        function renderMap() {
            if (! window.daum || ! window.daum.roughmap) {
                return;
            }

            new window.daum.roughmap.Lander({
                timestamp: '1777006266657',
                key: '2aspkv7dzuzp',
                mapWidth: '800',
                mapHeight: '480'
            }).render();
        }

        if (window.daum && window.daum.roughmap) {
            renderMap();
            return;
        }

        var script = document.createElement('script');
        script.charset = 'UTF-8';
        script.src = 'https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js';
        script.onload = renderMap;
        document.head.appendChild(script);
    }

    $(function () {
        initMarquee();
        initCopyAddress();
        initRoughMap();
        initLegacyRoughMap();
    });
})(jQuery);
