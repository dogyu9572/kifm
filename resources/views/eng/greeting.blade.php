@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $geName)
@section('content')
@php
    $thumbnailSrc = ! empty($post?->thumbnail)
        ? asset('storage/'.$post->thumbnail)
        : asset('images/img_greeting.jpg');
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="greeting-heading">
    <div class="inner">
        <h1 class="sub_title" id="greeting-heading">{{ $geName }}</h1>

        <div class="greeting_area">
            <div class="imgfit">
                <img src="{{ $thumbnailSrc }}" alt="" aria-hidden="true">
                {{-- <div class="name">대한기능의학회 학회장 <span class="signature"><img src="/images/txt_name_gretting.svg" alt="김범택 서명"></span></div> --}}
            </div>

            <div class="txt">
                <span class="eng_title">KOREAN INSTITUTE FOR FUNCTIONAL MEDICINE</span>
                @if ($post)
                    <h2>{{ $post->title }}</h2>
                    <div class="desc">
                        {!! $post->content !!}
                    </div>
                @else
                   <h2><strong class="c_iden">기능의학의</strong> 새로운 기준을 <br class="pc_vw">만들어가겠습니다.</h2>
                    <div class="desc">
                        <p>인사말이 아직 등록되지 않았습니다.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

</main>

@endsection