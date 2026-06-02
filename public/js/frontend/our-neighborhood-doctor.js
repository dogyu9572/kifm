/**
 * 우리동네 주치의: 지도 UX, 검색 필터, 상세 팝업
 */
(function () {
    const MAP_INDEX_TO_SIDO = {
        '01': '서울특별시',
        '02': '경기도',
        '03': '인천광역시',
        '04': '강원특별자치도',
        '05': '충청북도',
        '06': '충청남도',
        '07': '세종특별자치시',
        '08': '경상북도',
        '09': '대구광역시',
        '10': '전북특별자치도',
        '11': '광주광역시',
        '12': '전라남도',
        '13': '경상남도',
        '14': '울산광역시',
        '15': '부산광역시',
        '16': '제주특별자치도',
    };

    const LOCAL_NAMES = {
        '01': '서울',
        '02': '경기',
        '03': '인천',
        '04': '강원',
        '05': '충북',
        '06': '충남',
        '07': '세종',
        '08': '경북',
        '09': '대구',
        '10': '전북',
        '11': '광주',
        '12': '전남',
        '13': '경남',
        '14': '울산',
        '15': '부산',
        '16': '제주',
    };

    let lastFocusedElement = null;
    let roughmapLoaderPromise = null;
    const kakaoMapLoaderPromises = {};

    document.addEventListener('DOMContentLoaded', () => {
        const mapRoot = document.getElementById('our-doctor-map-root');
        const searchForm = document.getElementById('our-doctor-search-form');
        if (!mapRoot || !searchForm) {
            return;
        }

        initSigunguCascade(mapRoot, searchForm);
        initNationalMap(mapRoot, searchForm);
        initLocalMapUi(mapRoot, searchForm);
        initPopup();
        restoreLocalMapView(mapRoot);
    });

    function initSigunguCascade(mapRoot, searchForm) {
        const sidoSelect = searchForm.querySelector('#sido-select');
        const sigunguSelect = searchForm.querySelector('#gugun-select');
        const mapIndexInput = searchForm.querySelector('#map-index-input');
        let sigunguMap = {};

        try {
            sigunguMap = JSON.parse(mapRoot.dataset.sigunguJson || '{}');
        } catch {
            sigunguMap = {};
        }

        const rebuildSigungu = (resetSelection) => {
            if (!sigunguSelect) {
                return;
            }
            const sido = (sidoSelect?.value || '').trim();
            let list = sido && Array.isArray(sigunguMap[sido]) ? sigunguMap[sido] : [];
            if (sido && list.length === 0) {
                const hit = Object.keys(sigunguMap).find((key) => key === sido);
                list = hit && Array.isArray(sigunguMap[hit]) ? sigunguMap[hit] : [];
            }
            const previous = resetSelection ? '' : sigunguSelect.value;
            sigunguSelect.innerHTML = '<option value="">군/구</option>';
            list.forEach((name) => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                if (!resetSelection && previous === name) {
                    opt.selected = true;
                }
                sigunguSelect.appendChild(opt);
            });
            if (!resetSelection && previous && !list.includes(previous)) {
                const optExtra = document.createElement('option');
                optExtra.value = previous;
                optExtra.textContent = previous;
                optExtra.selected = true;
                sigunguSelect.appendChild(optExtra);
            }
        };

        sidoSelect?.addEventListener('change', () => {
            if (mapIndexInput) {
                mapIndexInput.value = '';
            }
            rebuildSigungu(true);
        });

        rebuildSigungu(false);
    }

    function initNationalMap(mapRoot, searchForm) {
        const mapIndexInput = searchForm.querySelector('#map-index-input');
        const sidoSelect = searchForm.querySelector('#sido-select');

        $('.national_map_svg').on('mouseenter', 'g.map > g, g.name > g', function () {
            const classList = ($(this).attr('class') || '').split(' ');
            const targetClass = classList.find((c) => c.includes('map') || c.includes('name'));
            if (!targetClass) {
                return;
            }
            const index = targetClass.replace(/[^0-9]/g, '');
            $(`.national_map_svg .map${index}, .national_map_svg .name${index}`).addClass('hover');
        }).on('mouseleave', 'g.map > g, g.name > g', function () {
            $('.national_map_svg g').removeClass('hover');
        });

        $('.national_map_svg').on('click', 'g.map > g, g.name > g', function () {
            const classList = ($(this).attr('class') || '').split(' ');
            const targetClass = classList.find((c) => c.includes('map') || c.includes('name'));
            if (!targetClass) {
                return;
            }
            const index = targetClass.replace(/[^0-9]/g, '').padStart(2, '0');
            if (mapIndexInput) {
                mapIndexInput.value = index;
            }
            const canonicalSido = MAP_INDEX_TO_SIDO[index] || '';
            if (sidoSelect && canonicalSido) {
                sidoSelect.value = canonicalSido;
            }
            searchForm.querySelector('#gugun-select').value = '';
            searchForm.submit();
        });
    }

    function initLocalMapUi(mapRoot, searchForm) {
        const mapIndexInput = searchForm.querySelector('#map-index-input');
        const sidoSelect = searchForm.querySelector('#sido-select');
        const sigunguSelect = searchForm.querySelector('#gugun-select');

        $('.point_local').on('click', function () {
            if (mapIndexInput) {
                mapIndexInput.value = '';
            }
            if (sidoSelect) {
                sidoSelect.value = '';
            }
            if (sigunguSelect) {
                sigunguSelect.value = '';
            }
            searchForm.submit();
        });

        $('.svg_local').on('mouseenter', 'g.map > g, g.name > g', function () {
            const classList = ($(this).attr('class') || '').split(' ');
            const targetClass = classList.find((c) => c.includes('map') || c.includes('name'));
            if (!targetClass) {
                return;
            }
            const index = targetClass.replace(/[^0-9]/g, '');
            const $parentSvg = $(this).closest('svg');
            $parentSvg.find(`.map${index}, .name${index}`).addClass('hover');
        }).on('mouseleave', 'g.map > g, g.name > g', function () {
            $('.svg_local g').removeClass('hover');
        });

        $('.svg_local').on('click', 'g.map > g, g.name > g', function () {
            const classList = ($(this).attr('class') || '').split(' ');
            const targetClass = classList.find((c) => c.includes('map') || c.includes('name'));
            if (!targetClass) {
                return;
            }
            const index = targetClass.replace(/[^0-9]/g, '');
            const $parentSvg = $(this).closest('svg');
            $parentSvg.find('g').removeClass('click');
            $parentSvg.find(`.map${index}, .name${index}`).addClass('click');
        });
    }

    function restoreLocalMapView(mapRoot) {
        const selectedIndex = (mapRoot.dataset.selectedMapIndex || '').padStart(2, '0');
        if (!selectedIndex || selectedIndex === '00') {
            return;
        }
        const label = LOCAL_NAMES[selectedIndex] || mapRoot.dataset.localMapLabel || '';
        $('.point_local strong').text(label);
        $('.svg_national').addClass('is-map-hidden');
        $('.svg_local').addClass('show');
        $('.point_local').addClass('show');
        $('.svg_local .map_svg').removeClass('show');
        $(`.map_svg${selectedIndex}`).addClass('show');
    }

    function initPopup() {
        const popup = document.getElementById('pop_doctor');
        if (!popup) {
            return;
        }

        document.querySelectorAll('.js-doctor-popup-open').forEach((btn) => {
            btn.addEventListener('click', () => {
                openDoctorPopup(btn.dataset.popupUrl);
            });
        });

        popup.querySelectorAll('.js-doctor-popup-close').forEach((el) => {
            el.addEventListener('click', () => closeDoctorPopup());
        });
    }

    function openDoctorPopup(url) {
        if (!url) {
            return;
        }
        lastFocusedElement = document.activeElement;
        const popup = document.getElementById('pop_doctor');

        fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then((res) => {
                if (!res.ok) {
                    throw new Error('load failed');
                }
                return res.json();
            })
            .then((json) => {
                if (!json?.success || !json.data) {
                    throw new Error('invalid payload');
                }
                fillPopup(json.data);
                $(popup).fadeIn(300, () => {
                    popup.querySelector('.btn_close')?.focus();
                    drawDoctorMap(json.data);
                });
            })
            .catch(() => {
                window.alert('병원 정보를 불러오지 못했습니다.');
            });
    }

    function fillPopup(data) {
        const popup = document.getElementById('pop_doctor');
        if (!popup) {
            return;
        }

        const photoEl = popup.querySelector('.js-popup-photo');
        if (photoEl && data.photo_url) {
            photoEl.src = data.photo_url;
            photoEl.alt = data.doctor_name || '';
        }

        setText(popup, '.js-popup-hospital', data.hospital_name || '');
        setText(popup, '.js-popup-doctor', data.doctor_name ? `${data.doctor_name} 선생님` : '');

        const homepageLink = popup.querySelector('.js-popup-homepage');
        if (homepageLink) {
            if (data.homepage) {
                homepageLink.href = data.homepage;
                homepageLink.classList.remove('is-hidden');
            } else {
                homepageLink.href = '#';
                homepageLink.classList.add('is-hidden');
            }
        }

        const addressEl = popup.querySelector('.js-popup-address');
        if (addressEl) {
            addressEl.innerHTML = `<span class="sound_only">주소 : </span>${escapeHtml(data.full_address || '')}`;
        }

        const phoneEl = popup.querySelector('.js-popup-phone');
        if (phoneEl) {
            const phone = data.phone || '';
            const href = data.phone_href || '#';
            phoneEl.innerHTML = `<span class="sound_only">전화번호 : </span><a href="${escapeHtml(href)}">${escapeHtml(phone)}</a>`;
        }

        const introEl = popup.querySelector('.js-popup-introduction');
        if (introEl) {
            introEl.textContent = data.introduction_html || '';
        }
    }

    function roughmapContainerId(timestamp) {
        return timestamp ? `daumRoughmapContainer${timestamp}` : '';
    }

    function resolveRoughmapContainer(data) {
        const popup = document.getElementById('pop_doctor');
        const timestamp = data?.roughmap?.timestamp
            || popup?.dataset?.roughmapTimestamp
            || '';
        const id = roughmapContainerId(String(timestamp));
        return id ? document.getElementById(id) : null;
    }

    function closeDoctorPopup() {
        const popup = document.getElementById('pop_doctor');
        const mapContainer = resolveMapContainer(null);
        $(popup).fadeOut(300, () => {
            if (mapContainer) {
                mapContainer.innerHTML = '';
            }
            if (lastFocusedElement) {
                lastFocusedElement.focus();
            }
        });
    }

    function resolveMapContainer(data) {
        return resolveRoughmapContainer(data) || document.querySelector('.js-roughmap-container');
    }

    function drawDoctorMap(data) {
        const map = data?.map || {};
        const lat = Number(map.lat);
        const lng = Number(map.lng);
        const javascriptKey = String(map.javascript_key || '');

        if (Number.isFinite(lat) && Number.isFinite(lng) && javascriptKey !== '') {
            drawKakaoMap(data, lat, lng, javascriptKey);
            return;
        }

        drawRoughMap(data);
    }

    function drawKakaoMap(data, lat, lng, javascriptKey) {
        const mapContainer = resolveMapContainer(data);

        if (!mapContainer) {
            return;
        }

        mapContainer.innerHTML = '<div class="js-kakao-map-canvas kakao_map_canvas"></div>';
        const canvas = mapContainer.querySelector('.js-kakao-map-canvas');
        if (!canvas) {
            return;
        }

        loadKakaoMapScript(javascriptKey)
            .then(() => {
                if (!hasKakaoMapLoader()) {
                    clearMapContainer(data);
                    return;
                }

                kakao.maps.load(() => {
                    if (!hasKakaoMapSdk()) {
                        clearMapContainer(data);
                        return;
                    }

                    const center = new kakao.maps.LatLng(lat, lng);
                    const map = new kakao.maps.Map(canvas, {
                        center,
                        level: 3,
                    });
                    const marker = new kakao.maps.Marker({
                        position: center,
                    });
                    marker.setMap(map);
                });
            })
            .catch(() => {
                clearMapContainer(data);
            });
    }

    function clearMapContainer(data) {
        const mapContainer = resolveMapContainer(data);
        if (mapContainer) {
            mapContainer.innerHTML = '';
        }
    }

    function hasKakaoMapLoader() {
        return typeof kakao !== 'undefined'
            && kakao.maps
            && typeof kakao.maps.load === 'function';
    }

    function hasKakaoMapSdk() {
        return hasKakaoMapLoader()
            && typeof kakao.maps.Map === 'function'
            && typeof kakao.maps.LatLng === 'function';
    }

    function loadKakaoMapScript(javascriptKey) {
        if (hasKakaoMapLoader()) {
            return Promise.resolve();
        }

        if (kakaoMapLoaderPromises[javascriptKey]) {
            return kakaoMapLoaderPromises[javascriptKey];
        }

        kakaoMapLoaderPromises[javascriptKey] = new Promise((resolve, reject) => {
            const existing = document.querySelector('script[data-kakao-map-sdk]');
            if (existing) {
                if (hasKakaoMapLoader()) {
                    resolve();
                    return;
                }
                existing.addEventListener('load', () => resolve());
                existing.addEventListener('error', () => reject());
                return;
            }

            const script = document.createElement('script');
            script.dataset.kakaoMapSdk = 'true';
            script.src = `https://dapi.kakao.com/v2/maps/sdk.js?appkey=${encodeURIComponent(javascriptKey)}&autoload=false`;
            script.onload = () => resolve();
            script.onerror = () => reject();
            document.body.appendChild(script);
        });

        return kakaoMapLoaderPromises[javascriptKey];
    }

    function drawRoughMap(data) {
        const popup = document.getElementById('pop_doctor');
        const timestamp = data?.roughmap?.timestamp || popup?.dataset?.roughmapTimestamp || '1776648816237';
        const key = data?.roughmap?.key || popup?.dataset?.roughmapKey || 'me5vcjov52w';
        const mapContainer = resolveMapContainer(data);

        if (!mapContainer) {
            return;
        }
        mapContainer.innerHTML = '';

        loadRoughmapScript()
            .then(() => {
                if (typeof daum === 'undefined' || !daum.roughmap?.Lander) {
                    return;
                }
                new daum.roughmap.Lander({
                    timestamp: String(timestamp),
                    key: String(key),
                    mapWidth: '880',
                    mapHeight: '256',
                }).render();
            })
            .catch(() => {
                mapContainer.innerHTML = '';
            });
    }

    function loadRoughmapScript() {
        if (typeof daum !== 'undefined' && daum.roughmap?.Lander) {
            return Promise.resolve();
        }
        if (roughmapLoaderPromise) {
            return roughmapLoaderPromise;
        }
        roughmapLoaderPromise = new Promise((resolve, reject) => {
            const existing = document.querySelector('script.daum_roughmap_loader_script');
            if (existing) {
                if (typeof daum !== 'undefined' && daum.roughmap?.Lander) {
                    resolve();
                    return;
                }
                existing.addEventListener('load', () => resolve());
                existing.addEventListener('error', () => reject());
                return;
            }
            const script = document.createElement('script');
            script.charset = 'UTF-8';
            script.className = 'daum_roughmap_loader_script';
            script.src = 'https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js';
            script.onload = () => resolve();
            script.onerror = () => reject();
            document.body.appendChild(script);
        });
        return roughmapLoaderPromise;
    }

    function setText(root, selector, text) {
        const el = root.querySelector(selector);
        if (el) {
            el.textContent = text;
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
})();
