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

		<div class="favorite_wrap" data-mypage-favorite data-max-favorites="{{ $maxFavorites }}" data-save-url="{{ route('mypage.favorite_menu.store') }}">
			<p class="excl c_black" role="alert">즐겨찾는 메뉴는 <strong class="c_red">최대 {{ $maxFavorites }}개까지 저장이 가능합니다.</strong></p>
			<div class="glbox">
				<section class="box">
					@foreach ($menuGroups as $group)
					<fieldset>
						<legend><h2 class="mytit">{{ $group['title'] }}</h2></legend>
						@if (! empty($group['items']))
						<ul class="favorite_list">
							@foreach ($group['items'] as $item)
							<li>
								<div class="checkbox">
									<input type="checkbox" name="favorite" id="{{ $item['code'] }}" @checked(in_array($item['code'], $savedCodes, true))>
									<label for="{{ $item['code'] }}"><i></i><span>{{ $item['name'] }}</span></label>
								</div>
							</li>
							@endforeach
						</ul>
						@endif
					</fieldset>
					@endforeach
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
<script src="{{ asset('js/frontend/mypage-favorite.js') }}"></script>
@endpush
