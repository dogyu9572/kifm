@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<div class="sub_title">{{ $sName }}</div>

		<div class="glbox board_write inquiry_write_area">
			<form action="{{ isset($post) ? route('mypage.inquiry.update', $post->id) : route('mypage.inquiry.store') }}" method="POST">
				@csrf
				@if (isset($post))
				@method('PUT')
				@endif
				<table>
					<tbody>
						<tr>
							<th scope="row">제목<span class="c_red">*</span></th>
							<td>
								<input type="text" name="title" class="text w100p" value="{{ old('title', $post->title ?? '') }}" required>
								@error('title')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</td>
						</tr>
						<tr>
							<th scope="row">내용</th>
							<td>
								<textarea name="content" cols="30" rows="10" class="text w100p" required>{{ old('content', $post->content ?? '') }}</textarea>
								@error('content')
								<p class="c_red" role="alert">{{ $message }}</p>
								@enderror
							</td>
						</tr>
					</tbody>
				</table>
				<div class="btns_btm flex_center">
					@if (isset($post))
					<a href="{{ route('mypage.inquiry_view', ['id' => $post->id]) }}" class="btn btn_kwg">취소</a>
					@else
					<a href="{{ route('mypage.inquiry') }}" class="btn btn_kwg">목록</a>
					@endif
					<button type="submit" class="btn btn_wbb">{{ isset($post) ? '수정' : '등록' }}</button>
				</div>
			</form>
		</div>
	</div>
</section>

</main>
@endsection
