(function () {
    let courseCompletionAlertShown = false;

    function initSlides() {
        if (typeof Swiper === 'undefined' || !document.querySelector('.online_academy_slide_wrap')) {
            return;
        }

        const academyImgSlide = new Swiper('.slide_img', {
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 800,
        });

        const academyTxtSlide = new Swiper('.slide_txt', {
            loop: true,
            speed: 800,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.online_academy_head .next',
                prevEl: '.online_academy_head .prev',
            },
            on: {
                slideChangeTransitionStart: function () {
                    $('.online_academy_head .line span').stop().css('width', '0');
                },
                slideChangeTransitionEnd: function () {
                    $('.online_academy_head .line span').animate({ width: '100%' }, 5000, 'linear');
                },
                init: function () {
                    $('.online_academy_head .line span').animate({ width: '100%' }, 5000, 'linear');
                },
            },
        });

        academyTxtSlide.controller.control = academyImgSlide;
    }

    function initPageAlert() {
        const alertElement = document.querySelector('[data-page-alert]');
        const message = alertElement?.dataset.pageAlert || '';
        if (message) {
            window.alert(message);
        }
    }

    function initKeywordFilter() {
        const form = document.getElementById('online-academy-filter-form');
        const customBox = document.querySelector('[data-keyword-select]');
        if (!form || !customBox) {
            return;
        }

        const maxSelect = 5;
        const selectPanel = customBox.querySelector('.select_check');
        const userSelect = customBox.querySelector('.user_select');
        const hiddenWrap = customBox.querySelector('.js-keyword-hidden-inputs');

        customBox.querySelector('.select_type')?.addEventListener('click', function () {
            $(selectPanel).stop().slideToggle(300);
        });

        customBox.querySelectorAll('.select_list button').forEach((button) => {
            button.addEventListener('click', function () {
                const isTotal = button.dataset.keywordValue === '전체';
                const isOn = button.classList.contains('on');

                if (isTotal) {
                    customBox.querySelectorAll('.select_list button').forEach((other) => {
                        if (other !== button) {
                            other.classList.remove('on');
                        }
                    });
                    button.classList.toggle('on');
                    return;
                }

                customBox.querySelectorAll('[data-keyword-value="전체"]').forEach((totalButton) => {
                    totalButton.classList.remove('on');
                });

                const checkedCount = customBox.querySelectorAll('.select_list button.on:not([data-keyword-value="전체"])').length;
                if (!isOn && checkedCount >= maxSelect) {
                    window.alert('최대 ' + maxSelect + '개까지 선택 가능합니다.');
                    return;
                }
                button.classList.toggle('on');
            });
        });

        customBox.querySelector('.select_check .btn_reset')?.addEventListener('click', function () {
            customBox.querySelectorAll('.select_list button').forEach((button) => button.classList.remove('on'));
        });

        customBox.querySelector('.select_check .btn_check')?.addEventListener('click', function () {
            const selected = Array.from(customBox.querySelectorAll('.select_list button.on'))
                .map((button) => button.dataset.keywordValue || button.textContent.trim())
                .filter((value) => value && value !== '전체');

            userSelect.innerHTML = '';
            hiddenWrap.innerHTML = '';

            selected.forEach((value) => {
                const li = document.createElement('li');
                li.textContent = value;
                userSelect.appendChild(li);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'keywords[]';
                input.value = value;
                hiddenWrap.appendChild(input);
            });

            customBox.classList.toggle('on', selected.length > 0);
            $(selectPanel).stop().slideUp(300);
            form.submit();
        });
    }

    function initListAnchorFocus() {
        const listSection = document.querySelector('.online_academy_list');
        if (!listSection) {
            return;
        }
        const form = document.getElementById('online-academy-filter-form');
        const courseTypeInput = form?.querySelector('[data-course-type-input]');

        document.querySelectorAll('[data-online-academy-tabs] a').forEach((link) => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const url = link.getAttribute('href');
                const courseType = link.dataset.courseType || '';
                link.closest('li')?.classList.add('on');
                link.closest('li')?.parentElement?.querySelectorAll('li').forEach((li) => {
                    if (li !== link.closest('li')) {
                        li.classList.remove('on');
                    }
                });
                if (courseTypeInput) {
                    courseTypeInput.value = courseType;
                }

                const headerHeight = $('.header').outerHeight() || 0;
                const targetPos = $(listSection).offset().top - headerHeight;
                $('html, body').stop().animate({
                    scrollTop: Math.max(0, targetPos),
                }, 500);

                if (url) {
                    loadCourseList(url);
                }
            });
        });

        const hasFilter = new URLSearchParams(window.location.search).toString() !== '';
        if (hasFilter) {
            window.requestAnimationFrame(function () {
                const headerHeight = $('.header').outerHeight() || 0;
                const targetPos = $(listSection).offset().top - headerHeight;
                window.scrollTo(0, Math.max(0, targetPos));
            });
        }
    }

    function loadCourseList(url) {
        const list = document.querySelector('.gallery_academy');
        const pagination = document.querySelector('.board-pagination');
        if (!list) {
            window.location.href = url;
            return;
        }

        list.classList.add('is-loading');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Failed to load course list.');
                }
                return response.text();
            })
            .then((html) => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextList = doc.querySelector('.gallery_academy');
                const nextPagination = doc.querySelector('.board-pagination');
                if (!nextList) {
                    throw new Error('Course list not found.');
                }

                list.replaceWith(nextList);
                if (pagination && nextPagination) {
                    pagination.replaceWith(nextPagination);
                } else if (pagination && !nextPagination) {
                    pagination.remove();
                } else if (!pagination && nextPagination) {
                    nextList.insertAdjacentElement('afterend', nextPagination);
                }

                window.history.pushState({}, '', url);
            })
            .catch(() => {
                window.location.href = url;
            })
            .finally(() => {
                document.querySelector('.gallery_academy')?.classList.remove('is-loading');
            });
    }

    function initStateBars() {
        document.querySelectorAll('.state_line').forEach((line) => {
            const percentText = line.querySelector('.left strong')?.textContent || '';
            const percentValue = percentText.replace(/[^0-9]/g, '') || '0';
            const bar = line.querySelector('.bar');
            if (bar) {
                bar.style.width = percentValue + '%';
            }
        });

        document.querySelectorAll('.test_state_line').forEach((line) => {
            const stepText = line.querySelector('.step')?.textContent || '';
            const parts = stepText.split('/');
            const currentStep = parseFloat(parts[0]);
            const totalSteps = parseFloat(parts[1]);
            const bar = line.querySelector('.bar');
            if (bar && totalSteps > 0) {
                bar.style.width = (currentStep / totalSteps) * 100 + '%';
            }
        });
    }

    function formatDurationText(durationSec) {
        const seconds = Math.max(0, parseInt(durationSec, 10) || 0);
        const minutes = Math.floor(seconds / 60);
        const remainSeconds = seconds % 60;

        if (remainSeconds <= 0) {
            return minutes + '분';
        }

        return minutes + '분 ' + remainSeconds + '초';
    }

    function setCourseProgress(progressRate, watchedMin, durationSec) {
        const progress = Math.max(0, Math.min(100, parseInt(progressRate, 10) || 0));
        const watched = Math.max(0, parseInt(watchedMin, 10) || 0);
        const container = document.querySelector('.online_academy_view');
        const line = document.querySelector('.state_line');
        const bar = line?.querySelector('.bar');
        const percentText = line?.querySelector('.percent_val');
        const watchedText = line?.querySelector('.watched_min');
        const durationText = line?.querySelector('.duration_text');
        const testButton = document.querySelector('.btn_test');
        const buttonArea = document.querySelector('.btn_area');
        const textBox = document.querySelector('.txtbox');
        const previousProgress = Math.max(0, Math.min(100, parseInt(container?.dataset.initialProgress || '0', 10) || 0));

        if (bar) {
            bar.style.width = progress + '%';
            bar.setAttribute('aria-valuenow', String(progress));
        }
        if (percentText) {
            percentText.textContent = progress + '%';
        }
        if (watchedText) {
            watchedText.textContent = watched + '분';
        }
        if (durationText && durationSec !== undefined) {
            durationText.textContent = formatDurationText(durationSec);
        }
        if (container) {
            container.dataset.initialProgress = String(progress);
        }

        if (progress >= 100) {
            container?.classList.add('percent100');
            buttonArea?.classList.add('end');
            textBox?.removeAttribute('aria-hidden');
            testButton?.classList.remove('disabled');
            testButton?.setAttribute('aria-disabled', 'false');
            if (previousProgress < 100 && !courseCompletionAlertShown) {
                courseCompletionAlertShown = true;
                window.alert('강의 수강을 완료하였습니다.');
            }
        }
    }

    function initTestButton() {
        const percentText = document.querySelector('.state_line .left strong');
        const testButton = document.querySelector('.btn_test');
        const buttonArea = document.querySelector('.btn_area');
        const container = document.querySelector('.online_academy_view');
        const textBox = document.querySelector('.txtbox');
        if (!percentText || !testButton || !buttonArea || !container) {
            return;
        }

        if (percentText.textContent.replace(/\s/g, '') === '100%') {
            setCourseProgress(100, document.querySelector('.watched_min')?.textContent.replace(/[^0-9]/g, '') || 0);
        }

        testButton.addEventListener('click', function (event) {
            if (testButton.getAttribute('aria-disabled') === 'true') {
                event.preventDefault();
            }
        });
    }

    function initTestEnd() {
        const end = document.querySelector('.test_end');
        if (!end) {
            return;
        }
        const passed = end.dataset.passed === '1';
        if (passed) {
            end.classList.add('pass');
            end.closest('.test_page')?.querySelectorAll('.btn_kwg, .btn_wkk').forEach((button) => button.remove());
        } else {
            end.classList.add('fail');
            end.closest('.test_page')?.querySelectorAll('.btn_woo2').forEach((button) => button.remove());
        }
    }

    function initExamEndBackGuard() {
        const page = document.querySelector('[data-prevent-online-exam-back]');
        if (!page) {
            return;
        }

        const fallbackUrl = page.dataset.onlineExamBackUrl || '/online_academy/';
        window.history.replaceState({ onlineExamEnd: true }, '', window.location.href);
        window.history.pushState({ onlineExamEnd: true }, '', window.location.href);
        window.addEventListener('popstate', function () {
            window.location.replace(fallbackUrl);
        });
    }

    function initHistoryBack() {
        document.querySelectorAll('[data-history-back]').forEach((button) => {
            button.addEventListener('click', function () {
                window.history.back();
            });
        });
    }

    function initPaymentStickyBox() {
		const detail = document.querySelector('.academic_event_view_detail');
		const app = document.querySelector('.abso_application');
		const header = document.querySelector('header');
		
		if (!detail || !app) {
			return;
		}

		let touchStartY = 0;
		let startBottom = 0;
		let isDragging = false;
		let isUserOpened = false;

		const onScroll = function () {
			const scrollTop = window.scrollY || document.documentElement.scrollTop;
			const windowHeight = window.innerHeight;
			const windowWidth = window.innerWidth;
			
			const headerHeight = header ? header.offsetHeight : 0;
			const detailTop = detail.getBoundingClientRect().top + scrollTop;
			const detailHeight = detail.offsetHeight;
			const appHeight = app.offsetHeight;

			const mobileInboxInner = document.querySelector('.training_course_view_wrap .inbox');

			if (windowWidth <= 767) {
				if (mobileInboxInner) {
					mobileInboxInner.style.paddingBottom = (appHeight + 20) + 'px';
				}
				
				const mobileUnfixPoint = (detailTop + detailHeight) - windowHeight;

				if (scrollTop >= mobileUnfixPoint) {
					detail.classList.add('unfixed');
					detail.classList.remove('fixed');
					if (!isDragging) {
						app.style.transition = 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)';
						app.classList.add('open');
						app.style.bottom = '0px';
					}
				} else {
					detail.classList.remove('unfixed');
					detail.classList.add('fixed');
					if (!isDragging) {
						if (isUserOpened) {
							app.classList.add('open');
							app.style.bottom = '0px';
						} else {
							app.classList.remove('open');
							app.style.bottom = `calc(32px - ${appHeight}px)`;
						}
					}
				}
			} else {
				if (mobileInboxInner) {
					mobileInboxInner.style.paddingBottom = '';
				}
				app.classList.remove('open');
				app.style.bottom = '';
				app.style.transition = '';

				const fixStartPoint = detailTop - headerHeight - 50;
				const unfixPoint = detailTop + detailHeight - appHeight - 100;

				if (scrollTop >= fixStartPoint) {
					if (scrollTop >= unfixPoint) {
						detail.classList.add('unfixed');
						detail.classList.remove('fixed');
					} else {
						detail.classList.add('fixed');
						detail.classList.remove('unfixed');
					}
				} else {
					detail.classList.remove('fixed', 'unfixed');
				}
			}
		};

		app.addEventListener('touchstart', function (e) {
			if (window.innerWidth > 767 || detail.classList.contains('unfixed')) { return; }
			const touch = e.touches[0];
			touchStartY = touch.clientY;
			isDragging = true;
			app.style.transition = 'none';
			
			const windowHeight = window.innerHeight;
			const appRect = app.getBoundingClientRect();
			startBottom = windowHeight - appRect.bottom;
		}, { passive: true });

		app.addEventListener('touchmove', function (e) {
			if (!isDragging || window.innerWidth > 767 || detail.classList.contains('unfixed')) { return; }
			const touch = e.touches[0];
			const diffY = touchStartY - touch.clientY;
			
			if (Math.abs(diffY) > 5) {
				if (e.cancelable) { e.preventDefault(); }
			}
			
			let currentBottom = startBottom + diffY;
			if (currentBottom > 0) { currentBottom = 0; }
			app.style.bottom = currentBottom + 'px';
		}, { passive: false });

		const onTouchEnd = function (e) {
			if (!isDragging || window.innerWidth > 767) { return; }
			isDragging = false;
			const touch = e.changedTouches[0];
			const diffY = touchStartY - touch.clientY;
			const appHeight = app.offsetHeight;
			const isUnfixed = detail.classList.contains('unfixed');

			app.style.transition = 'bottom 0.3s cubic-bezier(0.25, 1, 0.5, 1)';
			const closedBottom = isUnfixed
				? `-${appHeight - 33}px`
				: `calc(32px - ${appHeight}px)`;

			if (diffY > 30) {
				app.classList.add('open');
				app.style.bottom = '0px';
				isUserOpened = true;
			} else if (diffY < -30) {
				app.classList.remove('open');
				app.style.bottom = closedBottom;
				isUserOpened = false;
			} else {
				if (app.classList.contains('open')) {
					app.style.bottom = '0px';
					isUserOpened = true;
				} else {
					app.style.bottom = closedBottom;
					isUserOpened = false;
				}
			}
		};

		app.addEventListener('touchend', onTouchEnd);
		app.addEventListener('touchcancel', onTouchEnd);

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);
		
		onScroll();
	}

    function loadVimeoSdk() {
        if (window.Vimeo?.Player) {
            return Promise.resolve(window.Vimeo);
        }

        return new Promise((resolve, reject) => {
            const existingScript = document.querySelector('script[src="https://player.vimeo.com/api/player.js"]');
            if (existingScript) {
                existingScript.addEventListener('load', () => resolve(window.Vimeo));
                existingScript.addEventListener('error', reject);
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://player.vimeo.com/api/player.js';
            script.async = true;
            script.onload = function () {
                if (window.Vimeo?.Player) {
                    resolve(window.Vimeo);
                    return;
                }
                reject(new Error('Vimeo Player SDK is not available.'));
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function initVimeoProgress() {
        const container = document.querySelector('.online_academy_view[data-progress-url]');
        const iframe = container?.querySelector('iframe[data-vimeo-player]');
        if (!container || !iframe) {
            return;
        }

        const progressUrl = container.dataset.progressUrl || '';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let lastSavedAt = 0;
        let lastPayload = null;
        let isSaving = false;

        function initialPosition() {
            const position = parseInt(container.dataset.initialPosition || '0', 10) || 0;
            const progress = parseInt(container.dataset.initialProgress || '0', 10) || 0;
            return progress < 100 ? position : 0;
        }

        async function saveProgress(player, ended) {
            if (!progressUrl || isSaving) {
                return;
            }

            isSaving = true;
            try {
                const values = await Promise.all([
                    player.getCurrentTime(),
                    player.getDuration(),
                ]);
                const currentTime = Math.max(0, Number(values[0]) || 0);
                const duration = Math.max(0, Number(values[1]) || 0);
                const payload = {
                    current_time: currentTime,
                    duration: duration,
                    ended: Boolean(ended),
                };
                const payloadKey = [
                    Math.floor(payload.current_time),
                    Math.floor(payload.duration),
                    payload.ended ? 1 : 0,
                ].join(':');

                if (!payload.ended && payloadKey === lastPayload) {
                    return;
                }
                lastPayload = payloadKey;

                const response = await fetch(progressUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                });
                const data = await response.json().catch(() => ({}));
                if (response.ok && data.success) {
                    setCourseProgress(data.progress_rate, data.watched_min, data.video_duration_sec);
                    if (data.last_position_sec !== undefined) {
                        container.dataset.initialPosition = String(data.last_position_sec);
                    }
                    if (data.video_duration_sec !== undefined) {
                        container.dataset.initialDuration = String(data.video_duration_sec);
                    }
                }
            } catch (error) {
                // Progress saving is retried by the next player event.
            } finally {
                isSaving = false;
            }
        }

        loadVimeoSdk()
            .then((Vimeo) => {
                const player = new Vimeo.Player(iframe);
                const resumeAt = initialPosition();

                if (resumeAt > 0) {
                    player.getDuration()
                        .then((duration) => {
                            if (duration && resumeAt < duration - 5) {
                                return player.setCurrentTime(resumeAt);
                            }
                            return null;
                        })
                        .catch(() => {});
                }

                player.on('timeupdate', function (event) {
                    const now = Date.now();
                    if (now - lastSavedAt < 10000 && (event.percent || 0) < 0.99) {
                        return;
                    }
                    lastSavedAt = now;
                    saveProgress(player, false);
                });
                player.on('pause', function () {
                    saveProgress(player, false);
                });
                player.on('ended', function () {
                    saveProgress(player, true);
                });
                window.addEventListener('beforeunload', function () {
                    saveProgress(player, false);
                });
            })
            .catch(() => {});
    }

    function initCheckoutForm() {
		const form = document.getElementById('online-academy-checkout-form');
		if (!form) {
			return;
		}

		const courseInput = form.querySelector('input[name="course_id"]');
		const itemInput = form.querySelector('input[name="course_item"]');
		const couponInput = document.getElementById('coupon_num');
		const couponButton = document.getElementById('online-coupon-apply-btn');
		const couponResult = document.querySelector('#online-coupon-result dd');
		const summarySubtotal = document.getElementById('online-summary-subtotal');
		const summaryDiscount = document.getElementById('online-summary-discount');
		const summaryTotal = document.getElementById('online-summary-total');
		const submitAmount = document.getElementById('online-submit-amount');
		const bankElements = Array.from(document.querySelectorAll('.type_bank_hide'));
		const cardMessages = Array.from(document.querySelectorAll('.type_card'));
		const paymentMethodInput = document.getElementById('online-payment-method');
		const paymentRadios = Array.from(form.querySelectorAll('input[name="payment_method_display"]'));
		const receiptRadios = Array.from(form.querySelectorAll('input[name="receipt_issue"]'));
		const cashReceiptArea = document.querySelector('.cash_receipt_area');
		const submitButton = form.querySelector('button[type="submit"]');
		const termsCheckbox = document.getElementById('terms_agree');
		let appliedDiscount = 0;
		let appliedCouponCode = '';
		let tossSdkPromise = null;
		let csrfPreparedForSubmit = false;

		function numberFormat(value) {
			return new Intl.NumberFormat('ko-KR').format(Math.max(0, Number(value) || 0));
		}

		function subtotal() {
			return Number(itemInput?.dataset.price) || 0;
		}

		function csrfToken() {
			return form.querySelector('input[name="_token"]')?.value || '';
		}

		function setCsrfToken(token) {
			if (!token) {
				return;
			}

			const csrfInput = form.querySelector('input[name="_token"]');
			const csrfMeta = document.querySelector('meta[name="csrf-token"]');
			if (csrfInput) {
				csrfInput.value = token;
			}
			if (csrfMeta) {
				csrfMeta.setAttribute('content', token);
			}
		}

		async function refreshCsrfToken() {
			if (!form.dataset.csrfUrl) {
				return csrfToken();
			}

			const response = await fetch(form.dataset.csrfUrl, {
				method: 'GET',
				headers: {
					'Accept': 'application/json',
				},
			});
			const data = await response.json().catch(() => ({}));
			if (!response.ok || !data.token) {
				throw new Error('CSRF token refresh failed.');
			}

			setCsrfToken(data.token);
			return data.token;
		}

		async function fetchWithCsrfRetry(url, options) {
			const requestOptions = Object.assign({}, options);
			requestOptions.headers = Object.assign({}, requestOptions.headers || {}, {
				'X-CSRF-TOKEN': csrfToken(),
			});

			let response = await fetch(url, requestOptions);
			if (response.status !== 419) {
				return response;
			}

			await refreshCsrfToken();
			requestOptions.headers['X-CSRF-TOKEN'] = csrfToken();
			return fetch(url, requestOptions);
		}

		function setCouponMessage(message) {
			if (couponResult) {
				couponResult.textContent = message;
			}
		}

		function resetCoupon() {
			appliedDiscount = 0;
			appliedCouponCode = '';
			setCouponMessage('적용된 쿠폰이 없습니다.');
			updateSummary();
		}

		function updateSubmitButton() {
			if (!submitButton) {
				return;
			}
			const currentSubtotal = subtotal();
			const discount = Math.min(appliedDiscount, currentSubtotal);
			const total = Math.max(0, currentSubtotal - discount);

			if (termsCheckbox && termsCheckbox.checked) {
				submitButton.className = 'btn_submit btn_wbb';
				submitButton.innerHTML = `<span class="sound_only" id="online-submit-amount">${numberFormat(total)}원 </span>결제하기`;
			} else {
				submitButton.className = 'btn_submit btn_wgg';
				submitButton.innerHTML = `<span class="sound_only" id="online-submit-amount">${numberFormat(total)}원 </span>결제 약관에 동의해주세요.`;
			}
		}

		function updateSummary() {
			const currentSubtotal = subtotal();
			const discount = Math.min(appliedDiscount, currentSubtotal);
			const total = Math.max(0, currentSubtotal - discount);

			if (summarySubtotal) {
				summarySubtotal.textContent = numberFormat(currentSubtotal);
			}
			if (summaryDiscount) {
				summaryDiscount.textContent = discount > 0 ? '-' + numberFormat(discount) : '0';
			}
			if (summaryTotal) {
				summaryTotal.textContent = numberFormat(total);
			}
			updateSubmitButton();
		}

		function currentTotal() {
			return Number((summaryTotal?.textContent || '0').replace(/\D/g, '')) || 0;
		}

		async function applyCoupon() {
			const couponCode = couponInput ? couponInput.value.trim() : '';
			if (!couponCode) {
				window.alert('쿠폰번호를 입력해주세요.');
				return;
			}

			couponButton.disabled = true;
			try {
				const response = await fetchWithCsrfRetry(form.dataset.couponUrl, {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						course_id: courseInput?.value || '',
						coupon_code: couponCode,
					}),
				});
				const data = await response.json();
				if (!response.ok || !data.success) {
					resetCoupon();
					window.alert(data.message || '사용 가능한 쿠폰이 아닙니다.');
					return;
				}

				appliedCouponCode = couponCode.toUpperCase();
				appliedDiscount = Number(data.discount) || 0;
				if (couponInput) {
					couponInput.value = appliedCouponCode;
				}
				setCouponMessage(`${appliedCouponCode} / ${numberFormat(appliedDiscount)}원 할인`);
				window.alert('쿠폰이 적용되었습니다.');
				updateSummary();
			} catch (error) {
				window.alert('쿠폰 확인 중 오류가 발생했습니다.');
			} finally {
				couponButton.disabled = false;
			}
		}

		function selectedPaymentMethod() {
			return form.querySelector('input[name="payment_method_display"]:checked')?.value || 'card';
		}

		function syncPaymentMethod() {
			const method = selectedPaymentMethod() === 'bank' ? 'bank_transfer' : 'card';
			if (paymentMethodInput) {
				paymentMethodInput.value = method;
			}

			return method;
		}

		function toggleReceiptArea() {
			const isReceipt = form.querySelector('input[name="receipt_issue"]:checked')?.value === 'YES';
			const isBank = syncPaymentMethod() === 'bank_transfer';
			if (cashReceiptArea) {
				cashReceiptArea.style.display = isBank && isReceipt ? 'block' : 'none';
			}
		}

		function togglePaymentMethod() {
			const isBank = syncPaymentMethod() === 'bank_transfer';
			bankElements.forEach((element) => {
				element.style.display = isBank ? 'block' : 'none';
			});
			cardMessages.forEach((element) => {
				element.style.display = isBank ? 'none' : 'block';
			});
			toggleReceiptArea();
		}

		function validateBankTransferFields() {
			if (syncPaymentMethod() !== 'bank_transfer') {
				return true;
			}

			const depositor = form.querySelector('#bank_depositor');
			if (depositor && !depositor.value.trim()) {
				window.alert('입금자명을 입력해주세요.');
				depositor.focus();
				return false;
			}

			const depositDate = form.querySelector('#bank_deposit_date');
			if (depositDate && !depositDate.value.trim()) {
				window.alert('입금 예정일을 선택해주세요.');
				depositDate.focus();
				return false;
			}

			const refundAccount = form.querySelector('#training-refund-account');
			if (refundAccount && !refundAccount.value.trim()) {
				window.alert('환불 계좌번호를 입력해주세요.');
				refundAccount.focus();
				return false;
			}

			const refundHolder = form.querySelector('#training-refund-holder');
			if (refundHolder && !refundHolder.value.trim()) {
				window.alert('예금주명을 입력해주세요.');
				refundHolder.focus();
				return false;
			}

			const isReceipt = form.querySelector('input[name="receipt_issue"]:checked')?.value === 'YES';
			const receiptNumber = form.querySelector('#receipt_number');
			if (isReceipt && receiptNumber && !receiptNumber.value.trim()) {
				window.alert('현금영수증 번호를 입력해주세요.');
				receiptNumber.focus();
				return false;
			}

			return true;
		}

		function loadTossSdk() {
			if (window.TossPayments) {
				return Promise.resolve(window.TossPayments);
			}
			if (tossSdkPromise) {
				return tossSdkPromise;
			}

			tossSdkPromise = new Promise((resolve, reject) => {
				const script = document.createElement('script');
				script.src = 'https://js.tosspayments.com/v2/standard';
				script.async = true;
				script.onload = function () {
					if (window.TossPayments) {
						resolve(window.TossPayments);
						return;
					}
					reject(new Error('TossPayments SDK is not available.'));
				};
				script.onerror = function () {
					reject(new Error('Failed to load TossPayments SDK.'));
				};
				document.head.appendChild(script);
			});

			return tossSdkPromise;
		}

		async function requestTossPayment() {
			syncPaymentMethod();
			const response = await fetchWithCsrfRetry(form.action, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
				},
				body: new FormData(form),
			});
			const data = await response.json().catch(() => ({}));

			if (!response.ok || !data.success) {
				const errors = data.errors || {};
				const firstError = Object.keys(errors).length > 0 ? errors[Object.keys(errors)[0]][0] : null;
				window.alert(firstError || data.message || '입력 내용을 확인해주세요.');
				return;
			}

			const TossPayments = await loadTossSdk();
			const tossPayments = TossPayments(data.clientKey);
			const payment = tossPayments.payment({ customerKey: data.customerKey });
			await payment.requestPayment({
				method: 'CARD',
				amount: {
					value: Number(data.amount) || 0,
					currency: 'KRW',
				},
				orderId: data.orderId,
				orderName: data.orderName,
				customerName: data.customerName,
				customerEmail: data.customerEmail,
				customerMobilePhone: String(data.customerMobilePhone || '').replace(/\D/g, ''),
				successUrl: data.successUrl,
				failUrl: data.failUrl,
			});
		}

		if (couponButton) {
			couponButton.addEventListener('click', applyCoupon);
		}
		if (couponInput) {
			couponInput.addEventListener('input', function () {
				if (appliedCouponCode) {
					resetCoupon();
				}
			});
		}
		paymentRadios.forEach((input) => input.addEventListener('change', togglePaymentMethod));
		receiptRadios.forEach((input) => input.addEventListener('change', toggleReceiptArea));
		termsCheckbox?.addEventListener('change', updateSubmitButton);

		form.addEventListener('submit', async function (event) {
			if (termsCheckbox && !termsCheckbox.checked) {
				event.preventDefault();
				window.alert('결제 이용 약관 및 개인정보 처리 동의에 체크해주세요.');
				return;
			}

			if (syncPaymentMethod() === 'bank_transfer' || currentTotal() <= 0) {
				if (!validateBankTransferFields()) {
					event.preventDefault();
					return;
				}

				if (csrfPreparedForSubmit) {
					return;
				}

				event.preventDefault();
				try {
					await refreshCsrfToken();
					csrfPreparedForSubmit = true;
					form.requestSubmit();
				} catch (error) {
					window.alert('결제 요청을 준비하는 중 오류가 발생했습니다. 페이지를 새로고침 후 다시 시도해주세요.');
				}
				return;
			}

			event.preventDefault();
			if (submitButton) {
				submitButton.disabled = true;
			}
			try {
				await requestTossPayment();
			} catch (error) {
				window.alert('토스페이먼츠 결제창을 여는 중 오류가 발생했습니다.');
			} finally {
				if (submitButton) {
					submitButton.disabled = false;
				}
			}
		});

		togglePaymentMethod();
		updateSummary();
	}
	
	function initMobileTabsSelect() {
        const container = document.querySelector('.mo_tabs_select');
        if (!container) return;

        const btnSelect = container.querySelector('.btn_select_opcl');
        const tabsList = container.querySelector('.tabs');
        const activeTabLink = container.querySelector('.tabs li.on a');

        if (!btnSelect || !tabsList) return;
        if (activeTabLink) {
            btnSelect.textContent = activeTabLink.textContent;
        }
        tabsList.style.transition = 'max-height 0.15s ease-out';
        tabsList.style.overflow = 'hidden';
        btnSelect.addEventListener('click', function () {
            container.classList.toggle('on');
            if (!tabsList.style.maxHeight || tabsList.style.maxHeight === '0px') {
                tabsList.style.maxHeight = tabsList.scrollHeight + 'px';
            } else {
                tabsList.style.maxHeight = '0px';
            }
        });
    }

    function initOnlineAcademy() {
        initPageAlert();
        initSlides();
        initKeywordFilter();
        initListAnchorFocus();
        initStateBars();
        initTestButton();
        initTestEnd();
        initExamEndBackGuard();
        initHistoryBack();
        initPaymentStickyBox();
        initVimeoProgress();
        initCheckoutForm();
		initMobileTabsSelect();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnlineAcademy);
    } else {
        initOnlineAcademy();
    }
	
	
})();
