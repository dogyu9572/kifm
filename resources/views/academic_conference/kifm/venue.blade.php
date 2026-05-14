@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="venue-title">
	<div class="inner">
		<h1 class="sub_title" id="venue-title">{{ $sName }}</h1>
		
		<div class="venue_area">
			<div class="map_area">
				<div id="daumRoughmapContainer1777006266657" class="root_daum_roughmap root_daum_roughmap_landing"></div>
				<script charset="UTF-8" class="daum_roughmap_loader_script" src="https://ssl.daumcdn.net/dmaps/map_js_init/roughmapLoader.js"></script>
				<script charset="UTF-8">
					new daum.roughmap.Lander({
						"timestamp" : "1777006266657",
						"key" : "2aspkv7dzuzp",
						"mapWidth" : "800",
						"mapHeight" : "480"
					}).render();
				</script>
			</div>
			<div class="txt_area">
				<h2><strong class="c_iden">가톨릭대학교 성모병원</strong><br/>지하 1층 대강당</h2>
				<dl>
					<div class="i1">
						<dt>주소</dt>
						<dd><p class="copy_txt">서울 서초구 반포대로 222</p><button type="button" class="btn_copy c_iden">복사</button></dd>
					</div>
					<div class="i2">
						<dt>도보</dt>
						<dd>고속터미널역 (3, 7, 9호선) 4번 출구</dd>
					</div>
					<div class="i3">
						<dt>셔틀버스</dt>
						<dd>
							고속터미널역 (3, 7 ,9호선) 3번 출구<br/>
							서초역 (2호선) 7번 출구
							<p class="small">서초역에서 내리실 경우 셔틀버스를 이용해주세요. <br/>(서울성모병원까지 거리 1km)</p>
						</dd>
					</div>
				</dl>
			</div>
		</div>
	</div>
</section>

</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn_copy').on('click', function() {
        var textToCopy = $(this).siblings('.copy_txt').text().trim();
        if (!textToCopy) {
            textToCopy = $(this).closest('dd').find('.copy_txt').text().trim();
        }
        var $temp = $("<textarea>");
        $("body").append($temp);
        $temp.val(textToCopy).select();
        try {
            var successful = document.execCommand('copy');
            if (successful) {
                alert('주소가 복사되었습니다.');
            } else {
                alert('복사에 실패했습니다.');
            }
        } catch (err) {
            alert('이 브라우저에서는 복사를 지원하지 않습니다.');
        }
        $temp.remove();
    });
});
</script>
@endpush