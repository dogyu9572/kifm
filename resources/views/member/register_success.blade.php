@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon academic_event_view_end" aria-labelledby="register-success-heading">
    <div class="inner">
		<div class="inbox">
		
			<div class="sub_title">{{ $sName }} 완료</div>
		
			<div class="title_area mb24">
				<h1 id="register-success-heading" class="title">회원가입이 완료되었습니다.</h1>
			</div>
			
			<div class="gbox tac">
				현재 관리자가 신청 내용을 검토 중입니다.<br/>
				승인 완료 후, 등록하신 이메일로 안내를 드릴 예정입니다.<br/>
				조금만 기다려주세요!
			</div>
			
			<div class="btns_btm mt48">
				<a href="{{ route('home') }}" class="btn btn_wbb w100p">메인 페이지로</a>
			</div>
			
		</div>
	</div>
</section>
</main>

@endsection