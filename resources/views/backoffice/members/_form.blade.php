{{-- 변수: 컨트롤러에서 $isEdit, $member, $memberLevelLabels, $jobTypeLabels, $committeesForForm, $medicalDepartmentOptions, $medicalDepartmentValue, $selectedCommitteeCodes --}}
{{-- 프로토타입 member-detail.ejs·명세와 동일한 섹션·순서. 가입 구분은 화면 없음(등록 시 hidden email + Request 기본값). 비밀번호는 기본 정보 내 아이디/이름 행 바로 아래(프로토타입 없음·시스템용). --}}

<div class="bo-form-section">
    <h3 class="bo-section-title">기본 정보</h3>
    <div class="bo-form-list">
        @if (! $isEdit)
            <input type="hidden" name="join_type" value="email">
        @endif
        <div class="bo-form-row bo-form-row--stacked">
            <label class="bo-form-label">회원 등급 <span class="required">*</span></label>
            <div class="bo-form-field">
                <div class="board-radio-group bo-radio-group-wrap">
                    @foreach ($memberLevelLabels as $code => $label)
                        <div class="board-radio-item">
                            <input type="radio" id="member_level_{{ $code }}" name="member_level" value="{{ $code }}" class="board-radio-input"
                                @checked(old('member_level', $member->member_level ?? 'pending') == $code)>
                            <label for="member_level_{{ $code }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                @error('member_level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="bo-member-level-help">
                    <strong>정회원 · 평생회원 자격 조건</strong>
                    — 의사면허소지자 중
                    <span class="bo-member-level-help-accent">정회원비(연간)</span> 또는
                    <span class="bo-member-level-help-accent">평생회비(1회)</span>를 납부한 자.
                    평생회비는 한 번만 납부하면 자격이 영구 유지됩니다.
                </div>
            </div>
        </div>

		<div class="bo-form-row">
			<label class="bo-form-label">아이디(ID)</label>
			<div class="bo-form-field">
				@if ($isEdit)
					<input type="text" class="board-form-control bo-readonly-box" id="login_id" value="{{ $member->login_id }}" readonly>
				@else
					<input type="text" class="board-form-control @error('login_id') is-invalid @enderror" id="login_id" name="login_id" value="{{ old('login_id') }}" required>
					@error('login_id')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				@endif
			</div>
		</div>
		<div class="bo-form-row">
			<label class="bo-form-label">이름 <span class="required">*</span></label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $member->name ?? '') }}" maxlength="20" required>
				@error('name')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>

        @if (! $isEdit)
            <div class="bo-form-row" id="passwordGroup">
                <label class="bo-form-label">비밀번호 <span class="required">*</span></label>
                <div class="bo-form-field">
                    <input type="password" class="board-form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="영문/숫자/특수문자 조합 두가지 이상(8~10자 이내 입력)" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="bo-form-row" id="passwordConfirmationGroup">
                <label class="bo-form-label">비밀번호 확인 <span class="required">*</span></label>
                <div class="bo-form-field">
                    <input type="password" class="board-form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="비밀번호를 한 번 더 입력해주세요." required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @else
            <div class="bo-form-row">
                <label class="bo-form-label">비밀번호</label>
                <div class="bo-form-field">
                    <input type="password" class="board-form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="변경 시에만 입력해주세요.">
                    <small class="form-text text-muted bo-password-help">비밀번호를 변경하지 않으려면 비워두세요.</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="bo-form-row">
                <label class="bo-form-label">비밀번호 확인</label>
                <div class="bo-form-field">
                    <input type="password" class="board-form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="변경 시에만 입력해주세요.">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif

        <div class="bo-member-dual-row">
            <div class="bo-member-dual-col bo-member-dual-col--full">
                <div class="bo-form-row">
                    <label class="bo-form-label">영문명</label>
                    <div class="bo-form-field">
                        <input type="text" class="board-form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en', $member->name_en ?? '') }}" maxlength="100" placeholder="영문이름">
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">연락처 및 네트워크 정보</h3>
    <div class="bo-form-list">
		<div class="bo-form-row">
			<label class="bo-form-label">핸드폰</label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $member->phone_number ?? '') }}" placeholder="010-0000-0000">
				@error('phone_number')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>

        <div class="bo-form-row">
            <label class="bo-form-label">이메일</label>
            <div class="bo-form-field">
                <div class="input-with-button bo-gap-bottom">
                    <input type="email" class="board-form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $member->email ?? '') }}" placeholder="example@domain.com">
                    <button type="button" class="btn btn-secondary btn-sm" id="btnCheckEmail" @if($isEdit) data-exclude-id="{{ $member->id }}" @endif>중복확인</button>
                </div>
                <div id="emailCheckResult" class="check-result"></div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">전문가/직장 정보</h3>
    <div class="bo-form-list">
		<div class="bo-form-row">
			<label class="bo-form-label">의사면허번호</label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('license_number') is-invalid @enderror" name="license_number" value="{{ old('license_number', $member->license_number ?? '') }}" maxlength="80" placeholder="의사면허번호">
				@error('license_number')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>
		<div class="bo-form-row">
			<label class="bo-form-label">
				전문의번호
				<span class="bo-form-label-note">관리자 전용</span>
				<span class="bo-form-label-sub">데이터 이관용</span>
			</label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('specialist_number') is-invalid @enderror" name="specialist_number" value="{{ old('specialist_number', $member->specialist_number ?? '') }}" maxlength="80" placeholder="전문의번호">
				@error('specialist_number')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>
        <!-- <div class="bo-member-dual-row">
            <div class="bo-member-dual-col">
            </div>
            <div class="bo-member-dual-col">
            </div>
        </div> -->
		<div class="bo-form-row">
			<label class="bo-form-label">
				전문과
				<span class="bo-form-label-note">관리자 전용</span>
				<span class="bo-form-label-sub">데이터 이관용</span>
			</label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('specialty') is-invalid @enderror" name="specialty" value="{{ old('specialty', $member->specialty ?? '') }}" maxlength="120" placeholder="전문과">
				@error('specialty')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>
		<div class="bo-form-row">
			<label class="bo-form-label">직장명</label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('workplace_name') is-invalid @enderror" name="workplace_name" value="{{ old('workplace_name', $member->workplace_name ?? '') }}" maxlength="200" placeholder="직장명">
				@error('workplace_name')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>

        <div class="bo-form-row">
            <label class="bo-form-label">진료 과목</label>
            <div class="bo-form-field">
                <select name="medical_department" id="medical_department" class="board-form-control board-form-control--max-md @error('medical_department') is-invalid @enderror">
                    <option value="">선택하세요</option>
                    @foreach ($medicalDepartmentOptions as $code => $label)
                        <option value="{{ $code }}" @selected($medicalDepartmentValue === (string) $code)>{{ $label }}</option>
                    @endforeach
                    @if ($medicalDepartmentValue !== '' && ! array_key_exists($medicalDepartmentValue, $medicalDepartmentOptions))
                        <option value="{{ $medicalDepartmentValue }}" selected>{{ $medicalDepartmentValue }}</option>
                    @endif
                </select>
                @error('medical_department')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">직장전화</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('workplace_phone') is-invalid @enderror" name="workplace_phone" value="{{ old('workplace_phone', $member->workplace_phone ?? '') }}" maxlength="40" placeholder="02-0000-0000">
                @error('workplace_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="bo-form-row bo-form-row--stacked">
            <label class="bo-form-label">직장주소</label>
            <div class="bo-form-field">
                <div class="input-with-button bo-gap-bottom">
                    <input type="text" class="board-form-control" id="workplace_zipcode" name="workplace_zipcode" value="{{ old('workplace_zipcode', $member->workplace_zipcode ?? '') }}" placeholder="우편번호" readonly maxlength="20">
                    <button type="button" class="btn btn-secondary btn-sm" id="btnSearchWorkplaceAddress">우편번호 찾기</button>
                </div>
                <input type="text" class="board-form-control bo-gap-bottom" id="workplace_address" name="workplace_address" value="{{ old('workplace_address', $member->workplace_address ?? '') }}" placeholder="기본주소" readonly>
                <input type="text" class="board-form-control" id="workplace_address_detail" name="workplace_address_detail" value="{{ old('workplace_address_detail', $member->workplace_address_detail ?? '') }}" placeholder="상세주소">
                @error('workplace_zipcode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('workplace_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('workplace_address_detail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">학력 정보</h3>
    <div class="bo-form-list">
		<div class="bo-form-row">
			<label class="bo-form-label">출신대학</label>
			<div class="bo-form-field">
				<input type="text" class="board-form-control @error('school_name') is-invalid @enderror" id="school_name" name="school_name" value="{{ old('school_name', $member->school_name ?? '') }}" placeholder="출신대학교 입력">
				@error('school_name')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>
		<div class="bo-form-row">
			<label class="bo-form-label">학교 졸업년도</label>
			<div class="bo-form-field">
				<input type="number" class="board-form-control board-form-control--max-xs @error('graduate_year') is-invalid @enderror" name="graduate_year" value="{{ old('graduate_year', $member->graduate_year ?? '') }}" min="1950" max="2100" step="1" placeholder="졸업년도">
				@error('graduate_year')
					<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>
		</div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">기타 정보</h3>
    <div class="bo-form-list">
        <div class="bo-form-row bo-form-row--stacked">
            <label class="bo-form-label">구분 <span class="required">*</span></label>
            <div class="bo-form-field">
                <div class="board-radio-group bo-radio-group-wrap">
                    @foreach ($jobTypeLabels as $code => $label)
                        <div class="board-radio-item">
                            <input type="radio" id="job_type_{{ $code }}" name="job_type" value="{{ $code }}" class="board-radio-input"
                                @checked(old('job_type', $member->job_type ?: 'specialist') == $code)>
                            <label for="job_type_{{ $code }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                @error('job_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">소속 위원회 정보</h3>
    <div class="bo-form-list">
        <div class="bo-form-row bo-form-row--stacked">
            <div class="bo-form-field">
                <div class="checkbox-group bo-member-committee-group">
                    @foreach ($committeesForForm as $committeeOption)
                        <label class="checkbox-label">
                            <input type="checkbox" name="committee_codes[]" value="{{ (string) $committeeOption->id }}" @checked(in_array((string) $committeeOption->id, $selectedCommitteeCodes, true))>
                            <span>{{ $committeeOption->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('committee_codes')
                    <div class="invalid-feedback bo-inline-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
