@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName . ' | ' . $dName)
@section('gName', $gName)
@section('sName', $sName)
@section('dName', $dName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="subcommittee-heading">
	<div class="inner">
		@include('subcommittee.partials.committee_header', ['useCommitteeH1' => true, 'showCommitteeTabs' => true])

		<div class="sub_title">{{ $dName }}</div>

		<div class="discussion_area">
			<div class="discussion_top">
				<h2>심도있는 <strong class="c_iden">학술적 교류를 <br/>위한 토론의 장</strong>입니다.</h2>
				<p>함께 나누고 싶은 토론 주제를 등록해 주세요</p>
			</div>
			
			<div class="discussion_body">
				<dl class="glbox">
					<div>
						<dt>토론주제<span class="c_iden">*</span></dt>
						<dd><input type="text" class="text w100p" placeholder="토론 주제를 입력해 주세요."></dd>
					</div>
					<div>
						<dt>자동등록방지<span class="c_iden">*</span></dt>
						<dd class="captcha_area">
							<div class="obj imgfit"><img src="/images/img_sample_captcha.jpg" alt=""></div>
							<button type="button" class="obj btn_re" aira-label="숫자 이미지 변경"></button>
							<input type="text" class="obj text">
						</dd>
					</div>
				</dl>
				<p class="etc c_iden">* 주제에 어긋나는 게시글이나 광고성 글은 관리자에 의해 제한될 수 있습니다.<br/>
				* 자동등록방지 문자를 정확히 입력해 주셔야 저장이 완료됩니다.</p>
			</div>
			
			<div class="btns_btm flex_center">
				<a href="{{ route('subcommittee.discussion', $committee) }}" class="btn btn_kwg">취소</a>
				<button type="submit" class="btn btn_wbb">저장</button>
			</div>
		</div>
		
	</div>
</section>
	
</main>

@endsection