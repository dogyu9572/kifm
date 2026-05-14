// 팝업
	var lastFocusedElement;
	function layerShow(id) {
		lastFocusedElement = document.activeElement;
		$("#" + id).fadeIn(300, function() {
			$(this).find(".btn_close").focus();
		});
	}
	function layerHide(id) {
		$("#" + id).fadeOut(300, function() {
			if (lastFocusedElement) lastFocusedElement.focus();
		});
	}