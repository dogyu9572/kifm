@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
@php
    $doc = $doctor;
    $selectedCats = old('category_ids', $doc ? $doc->doctorCategories->pluck('id')->all() : []);
    if (! is_array($selectedCats)) {
        $selectedCats = [];
    }
    $funcSel = old('functional_tests_selected', $doc && is_array($doc->functional_tests_selected) ? $doc->functional_tests_selected : []);
    if (! is_array($funcSel)) {
        $funcSel = [];
    }
    $treatSel = old('treatment_areas_selected', $doc && is_array($doc->treatment_areas_selected) ? $doc->treatment_areas_selected : []);
    if (! is_array($treatSel)) {
        $treatSel = [];
    }
    $formSido = (string) old('sido', (string) ($doc?->sido ?? ''));
    $formSigungu = (string) old('sigungu', (string) ($doc?->sigungu ?? ''));
    $sigunguList = $formSido !== '' && isset($sigungu_by_sido[$formSido]) ? $sigungu_by_sido[$formSido] : [];
@endphp
<main class="sub_area">

<section class="scon" aria-labelledby="hospital-information-heading">
    <div class="inner">
        <h1 class="sub_title" id="hospital-information-heading">{{ $sName }}</h1>

        @if (session('success'))
            <p class="c_blue" role="status">{{ session('success') }}</p>
        @endif

        @include('mypage.mypage_tab')

        @if ($doc === null)
            <div class="hospital_information_area">
                <p class="text no_result">등록된 우리동네 주치의 정보가 없습니다. <br/>병원 등록 문의는 학회로 연락주세요.</p>
            </div>
        @elseif (! $canEdit)
            <div class="hospital_information_area">
                <p class="text no_result">관리자에 의해 병원 정보 직접 수정이 허용되지 않았습니다. <br/>변경이 필요하시면 학회로 문의해 주세요.</p>
            </div>
        @else
        <form
            action="{{ route('mypage.hospital_information.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="hospital_information_area"
            id="mypage-hospital-information-form"
            data-sigungu-json="{{ json_encode($sigungu_by_sido, JSON_UNESCAPED_UNICODE) }}"
        >
            @csrf
            @method('PUT')

            <fieldset>
                <legend class="btit">기본정보</legend>
                <div class="profile_area float">
                    <div class="img_area">
                        <label for="doctor_photo_file" class="imgfit">
                            <img src="{{ $photo_url }}" alt="" id="mypage-doctor-photo-preview">
                        </label>
                        <input type="file" name="photo" id="doctor_photo_file" class="sound_only" accept=".jpg,.jpeg,.png,.gif">
                        <p class="c_iden">*사진을 클릭하여 이미지를 변경해 주세요.</p>
                        @error('photo')
                            <p class="c_red">{{ $message }}</p>
                        @enderror
                    </div>
                    <dl class="txt_area">
                        <div>
                            <dt><label for="doctor_name">선생님 성함</label></dt>
                            <dd>
                                <input type="text" name="doctor_name" id="doctor_name" class="text" maxlength="100" required
                                    value="{{ old('doctor_name', $doc->doctor_name) }}">
                                @error('doctor_name')
                                    <p class="c_red">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>
                        <div>
                            <dt><label for="license_no">면허번호</label></dt>
                            <dd>
                                <input type="text" name="license_no" id="license_no" class="text" maxlength="50" required
                                    value="{{ old('license_no', $doc->license_no) }}">
                                @error('license_no')
                                    <p class="c_red">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>
                        <div>
                            <dt><label for="introduction">병원소개</label></dt>
                            <dd>
                                <textarea name="introduction" id="introduction" cols="30" rows="10" class="text w100p edit_area">{{ old('introduction', $introduction_text) }}</textarea>
                                @error('introduction')
                                    <p class="c_red">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>
                    </dl>
                </div>
            </fieldset>

            <fieldset class="register_wrap">
                <legend class="btit">병원 정보 관리</legend>
                <div class="flex_inputs float">
                    <ul>
                        <li>
                            <label for="hospital_name">병원명<span class="c_iden">*</span></label>
                            <input type="text" name="hospital_name" id="hospital_name" class="text" maxlength="200" required
                                value="{{ old('hospital_name', $doc->hospital_name) }}">
                            @error('hospital_name')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="mypage-sido">시/도<span class="c_iden">*</span></label>
                            <select name="sido" id="mypage-sido" class="text" required>
                                <option value="">선택</option>
                                @foreach ($sidos as $sidoOption)
                                    <option value="{{ $sidoOption }}" @selected($formSido === $sidoOption)>{{ $sidoOption }}</option>
                                @endforeach
                            </select>
                            @error('sido')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="mypage-sigungu">시/군/구<span class="c_iden">*</span></label>
                            <select name="sigungu" id="mypage-sigungu" class="text" required>
                                <option value="">선택</option>
                                @foreach ($sigunguList as $sg)
                                    <option value="{{ $sg }}" @selected($formSigungu === $sg)>{{ $sg }}</option>
                                @endforeach
                                @if ($formSigungu !== '' && ! in_array($formSigungu, $sigunguList, true))
                                    <option value="{{ $formSigungu }}" selected>{{ $formSigungu }}</option>
                                @endif
                            </select>
                            @error('sigungu')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="address">주소<span class="c_iden">*</span></label>
                            <div class="inbtn">
                                <input type="text" name="address" id="address" class="text" maxlength="500" required
                                    value="{{ old('address', $doc->address) }}">
                                <button type="button" class="btn btn_wkk" id="mypage-hospital-address-search">주소 검색</button>
                            </div>
                            @error('address')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="address_detail">상세주소</label>
                            <input type="text" name="address_detail" id="address_detail" class="text" maxlength="200"
                                value="{{ old('address_detail', $doc->address_detail) }}">
                            @error('address_detail')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="homepage">홈페이지 URL</label>
                            <input type="text" name="homepage" id="homepage" class="text" maxlength="500"
                                value="{{ old('homepage', $doc->homepage) }}" placeholder="https://">
                            @error('homepage')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="phone">전화번호<span class="c_iden">*</span></label>
                            <input type="text" name="phone" id="phone" class="text" maxlength="80" required
                                value="{{ old('phone', $doc->phone) }}">
                            @error('phone')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                        <li>
                            <label for="status">운영 상태<span class="c_iden">*</span></label>
                            <select name="status" id="status" class="text" required>
                                @foreach ($status_labels as $code => $label)
                                    <option value="{{ $code }}" @selected(old('status', $doc->status) === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="c_red">{{ $message }}</p>
                            @enderror
                        </li>
                    </ul>
                </div>
            </fieldset>

            <fieldset>
                <legend class="btit">진료 정보</legend>
                <div class="checkbox_flex float" role="group">
                    @foreach ($categories as $cat)
                        <div class="checkbox">
                            <input
                                type="checkbox"
                                name="category_ids[]"
                                id="medicalInformation{{ $cat->id }}"
                                value="{{ $cat->id }}"
                                @checked(in_array((int) $cat->id, array_map('intval', $selectedCats), true))
                            >
                            <label for="medicalInformation{{ $cat->id }}"><i></i>{{ $cat->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('category_ids')
                    <p class="c_red">{{ $message }}</p>
                @enderror
            </fieldset>

            <fieldset>
                <legend class="btit">시행하고 있는 기능의학 검사</legend>
                <div class="checkbox_flex float" role="group">
                    @foreach ($functional_tests as $item)
                        @php $fid = $item['id'] ?? ''; @endphp
                        <div class="checkbox">
                            <input type="checkbox" name="functional_tests_selected[]" id="functional{{ $fid }}" value="{{ $fid }}"
                                @checked(in_array($fid, $funcSel, true))>
                            <label for="functional{{ $fid }}"><i></i>{{ $item['label'] ?? $fid }}</label>
                        </div>
                    @endforeach
                </div>
                @error('functional_tests_selected')
                    <p class="c_red">{{ $message }}</p>
                @enderror
            </fieldset>

            <fieldset>
                <legend class="btit">치료 가능 영역</legend>
                <div class="checkbox_flex float" role="group">
                    @foreach ($treatment_areas as $item)
                        @php $tid = $item['id'] ?? ''; @endphp
                        <div class="checkbox">
                            <input type="checkbox" name="treatment_areas_selected[]" id="treatableArea{{ $tid }}" value="{{ $tid }}"
                                @checked(in_array($tid, $treatSel, true))>
                            <label for="treatableArea{{ $tid }}"><i></i>{{ $item['label'] ?? $tid }}</label>
                        </div>
                    @endforeach
                </div>
                @error('treatment_areas_selected')
                    <p class="c_red">{{ $message }}</p>
                @enderror
            </fieldset>

            <fieldset class="about_hospital">
                <legend class="sound_only">기타 병원 정보</legend>

                <h3 class="tit">기타 증상</h3>
                <textarea name="other_symptoms" id="other_symptoms" cols="30" rows="4" class="text w100p" placeholder="항목은 줄바꿈으로 구분하여 입력하세요.">{{ old('other_symptoms', $doc->other_symptoms) }}</textarea>
                @error('other_symptoms')
                    <p class="c_red">{{ $message }}</p>
                @enderror

                <h3 class="tit">질환 및 증후군</h3>
                <textarea name="diseases_text" id="diseases_text" cols="30" rows="4" class="text w100p" placeholder="예: 섬유근육통, 복합부위통증증후군(CRPS), 번아웃 증후군, 과민성 대장 증후군 등&#10;항목은 줄바꿈으로 구분하여 입력하세요.">{{ old('diseases_text', $doc->diseases_text) }}</textarea>
                @error('diseases_text')
                    <p class="c_red">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="btns_btm">
                <button type="submit" class="btn btn_b btn_wbb">저장하기</button>
            </div>
        </form>
        @endif

    </div>
</section>

</main>

@endsection

@push('scripts')
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
@php
    $hospitalInfoJsPath = public_path('js/frontend/mypage-hospital-information.js');
    $hospitalInfoJsVersion = file_exists($hospitalInfoJsPath) ? filemtime($hospitalInfoJsPath) : time();
@endphp
<script src="{{ asset('js/frontend/mypage-hospital-information.js') }}?v={{ $hospitalInfoJsVersion }}"></script>
@endpush
