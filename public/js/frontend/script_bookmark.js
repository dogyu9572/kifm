// 북마크
	$('.bookmark').on('click', function() {
		$(this).toggleClass('on');
		const isPressed = $(this).hasClass('on');
		$(this).attr('aria-pressed', isPressed);
		if (isPressed) {
			$(this).attr('aria-label', '북마크 취소');
		} else {
			$(this).attr('aria-label', '이 행사를 북마크에 추가');
		}
	});