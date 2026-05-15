@php
    $bannerUrl = ! empty($committee->banner_path)
        ? asset('storage/'.$committee->banner_path)
        : asset('images/bg_sample_subcommittee_list_top.jpg');
    $showTabs = $showCommitteeTabs ?? true;
    $useH1 = $useCommitteeH1 ?? true;
@endphp
@if ($useH1)
		<h1 class="sub_title" id="subcommittee-heading">{{ $committee->name }}</h1>
@else
		<div class="sub_title" id="subcommittee-heading">{{ $committee->name }}</div>
@endif

		<div class="subcommittee_cont_top"><a href="{{ route('subcommittee.index') }}" class="btn_back_box">돌아가기</a></div>

		<div class="subcommittee_list_top">
			<div class="imgfit"><img src="{{ $bannerUrl }}" alt=""></div>
			<p>
				@auth
					@if (trim((string) (auth()->user()->name ?? '')) !== '')
						안녕하세요. {{ auth()->user()->name }} 선생님
					@elseif (trim((string) (auth()->user()->login_id ?? '')) !== '')
						안녕하세요. {{ auth()->user()->login_id }} 선생님
					@else
						안녕하세요.
					@endif
				@else
					안녕하세요.
				@endauth
			</p>
			<h2><strong class="c_iden">{{ $committee->name }}</strong>를 찾아주셔서 감사합니다.</h2>
		</div>

@if ($showTabs)
		<ul class="tabs full_line mb">
			<li class="{{ ($dNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ route('subcommittee.notice', $committee) }}" @if(($dNum ?? '') == '01') aria-current="page" @endif>공지사항</a></li>
			<li class="{{ ($dNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ route('subcommittee.discussion', $committee) }}" @if(($dNum ?? '') == '02') aria-current="page" @endif>토론장</a></li>
			<li class="{{ ($dNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ route('subcommittee.archives', $committee) }}" @if(($dNum ?? '') == '03') aria-current="page" @endif>자료실</a></li>
		</ul>
@endif
