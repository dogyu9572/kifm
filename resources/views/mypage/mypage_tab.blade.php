<nav class="tabs_wrap">
	<ul class="tabs full_line">
		<li class="{{ ($sNum ?? '') == '01' ? 'on' : '' }}"><a href="/mypage/profile_edit" @if(($sNum ?? '') == '01') aria-current="page" @endif>개인정보 관리</a></li>
		<li class="{{ ($sNum ?? '') == '02' ? 'on' : '' }}"><a href="/mypage/participation_history" @if(($sNum ?? '') == '02') aria-current="page" @endif>참가내역 관리</a></li>
		<li class="{{ ($sNum ?? '') == '03' ? 'on' : '' }}"><a href="/mypage/online_training" @if(($sNum ?? '') == '03') aria-current="page" @endif>온라인 교육 수강내역</a></li>
		<li class="{{ ($sNum ?? '') == '04' ? 'on' : '' }}"><a href="/mypage/inquiry" @if(($sNum ?? '') == '04') aria-current="page" @endif>1:1 문의</a></li>
		<li class="{{ ($sNum ?? '') == '05' ? 'on' : '' }}"><a href="/mypage/favorite" @if(($sNum ?? '') == '05') aria-current="page" @endif>즐겨찾는 메뉴</a></li>
		<li class="{{ ($sNum ?? '') == '06' ? 'on' : '' }}"><a href="/mypage/bookmark" @if(($sNum ?? '') == '06') aria-current="page" @endif>북마크</a></li>
		<li class="{{ ($sNum ?? '') == '07' ? 'on' : '' }}"><a href="/mypage/hospital_information" @if(($sNum ?? '') == '07') aria-current="page" @endif>병원 정보 관리</a></li>
		<li class="{{ ($sNum ?? '') == '08' ? 'on' : '' }}"><a href="/mypage/executive_activities" @if(($sNum ?? '') == '08') aria-current="page" @endif>회원 활동(임원)</a></li>
		<li class="{{ ($sNum ?? '') == '09' ? 'on' : '' }}"><a href="/mypage/committee_participation" @if(($sNum ?? '') == '09') aria-current="page" @endif>위원회 참여 현황</a></li> <!-- 일반회원 -->
		<li class="{{ ($sNum ?? '') == '10' ? 'on' : '' }}"><a href="/mypage/committee_participation_admin" @if(($sNum ?? '') == '10') aria-current="page" @endif>위원회 참여 현황</a></li> <!-- 관리자 회원 -->
	</ul>
</nav>