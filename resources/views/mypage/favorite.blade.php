@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="find-id-heading">
	<div class="inner">
		<h1 class="sub_title" id="find-id-heading">{{ $sName }}</h1>
		
		@include('mypage.mypage_tab')
		
		<div class="favorite_wrap">
			<p class="excl c_black" role="alert">즐겨찾는 메뉴는 <strong class="c_red">최대 6개까지 저장이 가능합니다.</strong></p>
			<div class="glbox">
				<section class="box">
					<fieldset>
						<legend><h2 class="mytit">학회소개</h2></legend>
						<ul class="favorite_list">
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite11"><label for="favorite11"><i></i><span>학회개요</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite12"><label for="favorite12"><i></i><span>인사말</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite13"><label for="favorite13"><i></i><span>학회 연혁</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite14"><label for="favorite14"><i></i><span>회칙</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite15"><label for="favorite15"><i></i><span>임원진</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite16"><label for="favorite16"><i></i><span>오시는 길</span></label></div></li>
						</ul>
					</fieldset>
					<fieldset>
						<legend><h2 class="mytit">학술대회</h2></legend>
						<ul class="favorite_list">
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite21"><label for="favorite21"><i></i><span>학술대회</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite22"><label for="favorite22"><i></i><span>연수강좌</span></label></div></li>
						</ul>
					</fieldset>
					<fieldset>
						<legend><h2 class="mytit">산하위원회</h2></legend>
					</fieldset>
					<fieldset>
						<legend><h2 class="mytit">학회 자료실</h2></legend>
						<ul class="favorite_list">
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite31"><label for="favorite31"><i></i><span>일반 자료실</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite32"><label for="favorite32"><i></i><span>학술자료</span></label></div></li>
						</ul>
					</fieldset>
					<fieldset>
						<legend><h2 class="mytit">회원광장</h2></legend>
						<ul class="favorite_list">
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite41"><label for="favorite41"><i></i><span>학회공지</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite42"><label for="favorite42"><i></i><span>기타공지</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite43"><label for="favorite43"><i></i><span>학회 앨범</span></label></div></li>
							<li><div class="checkbox"><input type="checkbox" name="favorite" id="favorite44"><label for="favorite44"><i></i><span>회비 납부 안내</span></label></div></li>
						</ul>
					</fieldset>
					<fieldset>
						<legend><h2 class="mytit">우리동네 주치의</h2></legend>
					</fieldset>
					<fieldset>
						<legend><h2 class="mytit">온라인 아카데미</h2></legend>
					</fieldset>
				</section>
			</div>
			<div class="btns_btm flex_center">
				<button type="button" class="btn btn_kwg btn_reset">초기화</button>
				<button type="button" class="btn btn_wbb btn_save">저장</button>
			</div>
		</div>
		
	</div>
</section>
	
</main>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const MAX_CHECK = 6;
    const COOKIE_NAME = 'my_favorites';

    const savedFavorites = getCookie(COOKIE_NAME);
    if (savedFavorites) {
        const favoriteArray = savedFavorites.split(',');
        favoriteArray.forEach(id => {
            $(`#${id}`).prop('checked', true);
        });
    }

    $('input[name="favorite"]').on('change', function() {
        const checkedCount = $('input[name="favorite"]:checked').length;
        if (checkedCount > MAX_CHECK) {
            alert(`즐겨찾는 메뉴는 최대 ${MAX_CHECK}개까지만 선택할 수 있습니다.`);
            $(this).prop('checked', false);
        }
    });

    $('.btn_save').on('click', function() {
        const checkedIds = [];
        $('input[name="favorite"]:checked').each(function() {
            checkedIds.push($(this).attr('id'));
        });
        
        setCookie(COOKIE_NAME, checkedIds.join(','), 7);
        alert('즐겨찾는 메뉴가 저장되었습니다.');
    });

    $('.btn_reset').on('click', function() {
        if (confirm('선택된 즐겨찾기를 모두 초기화하시겠습니까?')) {
            $('input[name="favorite"]').prop('checked', false);
            setCookie(COOKIE_NAME, '', -1);
            alert('초기화되었습니다.');
        }
    });

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});
</script>
@endpush