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

    const LOCAL_SIGUNGU_BY_MAP = {
        '01': {
            '25': '금천구', '24': '관악구', '23': '구로구', '22': '양천구', '21': '서초구',
            '20': '동작구', '19': '영등포구', '18': '강서구', '17': '강남구', '16': '송파구',
            '15': '강동구', '14': '용산구', '13': '마포구', '12': '성동구', '11': '광진구',
            '10': '중구', '09': '서대문구', '08': '종로구', '07': '은평구', '06': '동대문구',
            '05': '성북구', '04': '중랑구', '03': '강북구', '02': '노원구', '01': '도봉구',
        },
        '02': {
            '31': '안성시', '30': '평택시', '29': '용인시', '28': '수원시', '27': '이천시',
            '26': '오산시', '25': '화성시', '24': '광주시', '23': '여주시', '22': '성남시',
            '21': '과천시', '20': '안산시', '19': '군포시', '18': '의왕시', '17': '안양시',
            '16': '시흥시', '15': '광명시', '14': '부천시', '13': '양평군', '12': '하남시',
            '11': '구리시', '10': '남양주시', '09': '의정부시', '08': '가평군', '07': '김포시',
            '06': '고양시', '05': '파주시', '04': '양주시', '03': '포천시', '02': '동두천시',
            '01': '연천군',
        },
        '03': {
            '10': '기타 옹진군', '09': '중구', '08': '연수구', '07': '남동구', '06': '미추홀구',
            '05': '동구', '04': '부평구', '03': '계양구', '02': '서구', '01': '강화군',
        },
        '04': {
            '18': '태백시', '17': '삼척시', '16': '동해시', '15': '영월군', '14': '정선군',
            '13': '원주시', '12': '횡성군', '11': '평창군', '10': '강릉시', '09': '홍천군',
            '08': '춘천시', '07': '양양군', '06': '속초시', '05': '인제군', '04': '고성군',
            '03': '양구군', '02': '화천군', '01': '철원군',
        },
        '05': {
            '11': '영동군', '10': '옥천군', '09': '보은군', '08': '청주시', '07': '증평군',
            '06': '괴산군', '05': '진천군', '04': '음성군', '03': '충주시', '02': '단양군',
            '01': '제천시',
        },
        '06': {
            '15': '금산군', '14': '논산시', '13': '계룡시', '12': '서천군', '11': '부여군',
            '10': '보령시', '09': '청양군', '08': '공주시', '07': '홍성군', '06': '예산군',
            '05': '천안시', '04': '아산시', '03': '태안군', '02': '서산시', '01': '당진시',
        },
        '07': {
            '19': '금남면', '18': '부강면', '17': '대평동', '16': '보람동', '15': '소담동',
            '14': '한솔동', '13': '새롬동', '12': '종촌동', '11': '도담동', '10': '아름동',
            '09': '고운동', '08': '연기면', '07': '장군면', '06': '연동면', '05': '안서면',
            '04': '조치원읍', '03': '전동면', '02': '전의면', '01': '소정면',
        },
        '08': {
            '21': '경주시', '20': '청도군', '19': '경산시', '18': '영천시', '17': '포항시',
            '16': '고령군', '15': '성주군', '14': '칠곡군', '13': '김천시', '12': '구미시',
            '11': '청송군', '10': '의성군', '09': '영덕군', '08': '안동시', '07': '영양군',
            '06': '상주시', '05': '예천군', '04': '문경시', '03': '영주시', '02': '봉화군',
            '01': '울진군',
        },
        '09': {
            '16': '달성군', '15': '달서구', '14': '수성구', '13': '남구', '12': '중구',
            '11': '서구', '10': '북구', '09': '동구', '08': '산성면', '07': '부계면',
            '06': '삼국유사면', '05': '효령면', '04': '우보면', '03': '의흥면', '02': '군위읍',
            '01': '소보면',
        },
        '10': {
            '14': '남원시', '13': '고창군', '12': '순창군', '11': '정읍시', '10': '부안군',
            '09': '장수군', '08': '임실군', '07': '진안군', '06': '무주군', '05': '김제시',
            '04': '전주시', '03': '완주군', '02': '익산시', '01': '군산시',
        },
        '11': {
            '05': '동구', '04': '남구', '03': '서구', '02': '북구', '01': '광산구',
        },
        '12': {
            '22': '해남군', '21': '강진군', '20': '영암군', '19': '나주시', '18': '무안군',
            '17': '함평군', '16': '고흥군', '15': '여수시', '14': '보성군', '13': '장흥군',
            '12': '광양시', '11': '순천시', '10': '화순군', '09': '곡성군', '08': '구례군',
            '07': '담양군', '06': '장성군', '05': '영광군', '04': '목포시', '03': '진도군',
            '02': '완도군', '01': '신안군',
        },
        '13': {
            '18': '남해군', '17': '거제시', '16': '통영시', '15': '고성군', '14': '창원시',
            '13': '사천시', '12': '하동군', '11': '진주시', '10': '함안군', '09': '의령군',
            '08': '김해시', '07': '양산시', '06': '밀양시', '05': '창녕군', '04': '산청군',
            '03': '합천군', '02': '함양군', '01': '거창군',
        },
        '14': {
            '05': '동구', '04': '남구', '03': '중구', '02': '북구', '01': '울주군',
        },
        '15': {
            '16': '영도구', '15': '사하구', '14': '중구', '13': '서구', '12': '남구',
            '11': '동구', '10': '부산진구', '09': '사상구', '08': '강서구', '07': '수영구',
            '06': '연제구', '05': '동래구', '04': '북구', '03': '해운대구', '02': '금정구',
            '01': '기장군',
        },
        '16': {
            '02': '서귀포시', '01': '제주시',
        },
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
            sigunguSelect.disabled = false;
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
        let sigunguMap = {};

        try {
            sigunguMap = JSON.parse(mapRoot.dataset.sigunguJson || '{}');
        } catch {
            sigunguMap = {};
        }

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

            const localIndex = resolveVisibleLocalIndex($parentSvg[0]);
            const canonicalSido = MAP_INDEX_TO_SIDO[localIndex] || '';
            const sigungu = resolveLocalSigungu($parentSvg[0], localIndex, index, sigunguMap[canonicalSido] || []);

            if (mapIndexInput && localIndex) {
                mapIndexInput.value = localIndex;
            }
            if (sidoSelect && canonicalSido) {
                sidoSelect.value = canonicalSido;
            }
            if (sigunguSelect && sigungu) {
                ensureSelectOption(sigunguSelect, sigungu);
                sigunguSelect.value = sigungu;
            }

            searchForm.submit();
        });
    }

    function resolveVisibleLocalIndex(svg) {
        if (!svg) {
            return '';
        }
        const className = Array.from(svg.classList || []).find((name) => /^map_svg\d{2}$/.test(name));
        return className ? className.replace('map_svg', '') : '';
    }

    function resolveLocalSigungu(svg, localIndex, mapNumber, sigunguList) {
        const paddedNumber = String(mapNumber).padStart(2, '0');
        const mappedName = LOCAL_SIGUNGU_BY_MAP[localIndex]?.[paddedNumber];
        if (mappedName) {
            return mappedName;
        }
        if (!svg || !Array.isArray(sigunguList) || sigunguList.length === 0) {
            return '';
        }
        const areaGroups = Array.from(svg.querySelectorAll('g.map > g:not(.no_pointer)'));
        const clickedGroup = svg.querySelector(`g.map > g.map${paddedNumber}, g.map > g.map${mapNumber}`);
        const areaIndex = clickedGroup ? areaGroups.indexOf(clickedGroup) : -1;

        return areaIndex >= 0 ? (sigunguList[areaIndex] || '') : '';
    }

    function ensureSelectOption(select, value) {
        if (!select || !value) {
            return;
        }
        const exists = Array.from(select.options).some((option) => option.value === value);
        if (exists) {
            return;
        }
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        select.appendChild(option);
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
				document.body.classList.add('over_h');
			});
		});

		popup.querySelectorAll('.js-doctor-popup-close').forEach((el) => {
			el.addEventListener('click', () => {
				closeDoctorPopup();
				document.body.classList.remove('over_h');
			});
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
