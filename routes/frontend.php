<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController as FrontendHomeController;
use App\Http\Controllers\Frontend\IntroductionController;
use App\Http\Controllers\Frontend\AcademicEventController;
use App\Http\Controllers\Frontend\SubcommitteeController;
use App\Http\Controllers\Frontend\ArchivesController;
use App\Http\Controllers\Frontend\MemberPlazaController;
use App\Http\Controllers\Frontend\OurNeighborhoodDoctorController;
use App\Http\Controllers\Frontend\OnlineAcademyController;
use App\Http\Controllers\Frontend\TermsController;
use App\Http\Controllers\Frontend\MemberController as FrontendMemberController;
use App\Http\Controllers\Frontend\Mypage\ProfileController as MypageProfileController;
use App\Http\Controllers\Frontend\Mypage\AnnualFeeController as MypageAnnualFeeController;
use App\Http\Controllers\Frontend\Mypage\HistoryController as MypageHistoryController;
use App\Http\Controllers\Frontend\Mypage\InquiryController as MypageInquiryController;
use App\Http\Controllers\Frontend\Mypage\PrintController as MypagePrintController;
use App\Http\Controllers\Frontend\TotalSearchController;
use App\Http\Controllers\Frontend\MailformController;
use App\Http\Controllers\Frontend\AcademicConference\MainController as AcademicConferenceMainController;
use App\Http\Controllers\Frontend\AcademicConference\KifmController as AcademicConferenceKifmController;
use App\Http\Controllers\Frontend\AcademicConference\ProgramController as AcademicConferenceProgramController;
use App\Http\Controllers\Frontend\AcademicConference\RegistrationController as AcademicConferenceRegistrationController;
use App\Http\Controllers\Frontend\AcademicConference\AbstractController as AcademicConferenceAbstractController;
use App\Http\Controllers\Frontend\AcademicConference\NoticeController as AcademicConferenceNoticeController;
use App\Http\Controllers\Frontend\AcademicConference\EtcController as AcademicConferenceEtcController;
use App\Http\Controllers\Frontend\AcademicConference\OnsiteController as AcademicConferenceOnsiteController;

// =============================================================================
// 사용자(프론트) 라우트 파일
// 백오피스 영향 차단을 위해 prefix/네임스페이스 격리
// =============================================================================

// 인트로 페이지
Route::get('/intro', [FrontendHomeController::class, 'intro'])->name('intro');
// 전문인 메인 페이지 (퍼블리싱 별칭 /home)
Route::get('/home', [FrontendHomeController::class, 'index'])->name('home.alt');

// 학회소개
Route::prefix('introduction')->name('introduction.')->group(function () {
    Route::get('/overview', [IntroductionController::class, 'overview'])->name('overview');
    Route::get('/greeting', [IntroductionController::class, 'greeting'])->name('greeting');
    Route::get('/history', [IntroductionController::class, 'history'])->name('history');
    Route::get('/bylaws', [IntroductionController::class, 'bylaws'])->name('bylaws');
    Route::get('/bylaws_operation', [IntroductionController::class, 'bylawsOperation'])->name('bylaws_operation');
    Route::get('/bylaws_protocol', [IntroductionController::class, 'bylawsProtocol'])->name('bylaws_protocol');
    Route::get('/officers', [IntroductionController::class, 'officers'])->name('officers');
    Route::get('/location', [IntroductionController::class, 'location'])->name('location');
});

// 학술행사
Route::prefix('academic_event')->name('academic_event.')->group(function () {
    Route::get('/conference', [AcademicEventController::class, 'conference'])->name('conference');
    Route::get('/conference/view', [AcademicEventController::class, 'conferenceView'])->name('conference_view');
    Route::get('/training_course', [AcademicEventController::class, 'trainingCourse'])->name('training_course');
    Route::get('/training_course/view', [AcademicEventController::class, 'trainingCourseView'])->name('training_course_view');
    Route::get('/training_course/end', [AcademicEventController::class, 'trainingCourseEnd'])->name('training_course_end');
});

// 산하위원회 (로그인 필요, 회원은 백오피스에서 지정한 위원회만 접근)
Route::prefix('subcommittee')->name('subcommittee.')->middleware('auth')->group(function () {
    Route::get('/', [SubcommitteeController::class, 'index'])->name('index');
    Route::prefix('{committee}')->whereNumber('committee')->group(function () {
        Route::get('/notice', [SubcommitteeController::class, 'notice'])->name('notice');
        Route::get('/notice/{id}', [SubcommitteeController::class, 'noticeShow'])->whereNumber('id')->name('notice_show');
        Route::get('/discussion', [SubcommitteeController::class, 'discussion'])->name('discussion');
        Route::get('/discussion/write', [SubcommitteeController::class, 'discussionWrite'])->name('discussion_write');
        Route::get('/discussion/{id}', [SubcommitteeController::class, 'discussionShow'])->whereNumber('id')->name('discussion_show');
        Route::get('/archives', [SubcommitteeController::class, 'archives'])->name('archives');
        Route::get('/archives/{id}', [SubcommitteeController::class, 'archivesShow'])->whereNumber('id')->name('archives_show');
    });
});

// 학회 자료실
Route::prefix('archives')->name('archives.')->group(function () {
    Route::get('/general', [ArchivesController::class, 'general'])->name('general');
    Route::get('/general/{id}', [ArchivesController::class, 'generalShow'])->whereNumber('id')->name('general_show');
    Route::get('/academic', [ArchivesController::class, 'academic'])->name('academic');
    Route::get('/academic/{id}', [ArchivesController::class, 'academicShow'])->whereNumber('id')->name('academic_show');
    Route::get('/members', [ArchivesController::class, 'members'])->name('members');
    Route::get('/members/{id}', [ArchivesController::class, 'membersShow'])->whereNumber('id')->name('members_show');
});

// 회원광장
Route::prefix('member_plaza')->name('member_plaza.')->group(function () {
    Route::get('/society_notices', [MemberPlazaController::class, 'societyNotices'])->name('society_notices');
    Route::get('/society_notices/{id}', [MemberPlazaController::class, 'societyNoticesShow'])->whereNumber('id')->name('society_notices_show');
    Route::get('/other_notices', [MemberPlazaController::class, 'otherNotices'])->name('other_notices');
    Route::get('/other_notices/{id}', [MemberPlazaController::class, 'otherNoticesShow'])->whereNumber('id')->name('other_notices_show');
    Route::get('/society_album', [MemberPlazaController::class, 'societyAlbum'])->name('society_album');
    Route::get('/society_album/{id}', [MemberPlazaController::class, 'societyAlbumShow'])->whereNumber('id')->name('society_album_show');
    Route::get('/fee_payment_guide', [MemberPlazaController::class, 'feePaymentGuide'])->name('fee_payment_guide');
});

// 우리동네 주치의
Route::prefix('our_neighborhood_doctor')->name('our_neighborhood_doctor.')->group(function () {
    Route::get('/', [OurNeighborhoodDoctorController::class, 'index'])->name('index');
});

// 온라인 아카데미
Route::prefix('online_academy')->name('online_academy.')->group(function () {
    Route::get('/', [OnlineAcademyController::class, 'index'])->name('index');
    Route::get('/view', [OnlineAcademyController::class, 'view'])->name('view');
    Route::get('/test', [OnlineAcademyController::class, 'test'])->name('test');
    Route::get('/end', [OnlineAcademyController::class, 'end'])->name('end');
});

// 약관
Route::prefix('terms')->name('terms.')->group(function () {
    Route::get('/privacy_policy', [TermsController::class, 'privacyPolicy'])->name('privacy_policy');
    Route::get('/email_collection_refusal', [TermsController::class, 'emailCollectionRefusal'])->name('email_collection_refusal');
    Route::get('/terms_of_use', [TermsController::class, 'termsOfUse'])->name('terms_of_use');
});

// 회원 (Phase 4 에서 기존 Auth 와 통합 예정)
Route::prefix('member')->name('member.')->group(function () {
    Route::get('/login', [FrontendMemberController::class, 'login'])->name('login');
    Route::post('/login', [FrontendMemberController::class, 'loginStore'])
        ->middleware('throttle:15,1')
        ->name('login.store');
    Route::post('/logout', [FrontendMemberController::class, 'logout'])
        ->middleware(['auth', 'throttle:30,1'])
        ->name('logout');
    Route::get('/dormant_auth', [FrontendMemberController::class, 'dormantAuth'])->name('dormant_auth');
    Route::get('/password_reset', [FrontendMemberController::class, 'passwordReset'])->name('password_reset');
    Route::get('/find_id', [FrontendMemberController::class, 'findId'])->name('find_id');
    Route::get('/find_id_result', [FrontendMemberController::class, 'findIdResult'])->name('find_id_result');
    Route::get('/find_pw', [FrontendMemberController::class, 'findPw'])->name('find_pw');
    Route::get('/new_password', [FrontendMemberController::class, 'newPassword'])->name('new_password');
    Route::get('/register', [FrontendMemberController::class, 'register'])->name('register');
    Route::post('/register', [FrontendMemberController::class, 'registerStore'])->name('register.store');
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/register/check-login-id', [FrontendMemberController::class, 'registerCheckLoginId'])->name('register.check-login-id');
        Route::post('/register/check-email', [FrontendMemberController::class, 'registerCheckEmail'])->name('register.check-email');
        Route::post('/register/check-phone', [FrontendMemberController::class, 'registerCheckPhone'])->name('register.check-phone');
        Route::post('/register/check-license', [FrontendMemberController::class, 'registerCheckLicense'])->name('register.check-license');
    });
    Route::get('/register_success', [FrontendMemberController::class, 'registerSuccess'])->name('register_success');
});

// 마이페이지 (Phase 5 분리: Profile / AnnualFee / History / Inquiry / Print)
Route::prefix('mypage')->name('mypage.')->middleware(['auth', 'frontend.member'])->group(function () {
    // 개인정보 관리
    Route::get('/profile_edit', [MypageProfileController::class, 'edit'])->name('profile_edit');
    Route::put('/profile_edit', [MypageProfileController::class, 'update'])->name('profile_edit.update');
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/profile_edit/check-email', [MypageProfileController::class, 'checkEmail'])->name('profile_edit.check-email');
        Route::post('/profile_edit/check-phone', [MypageProfileController::class, 'checkPhone'])->name('profile_edit.check-phone');
        Route::post('/profile_edit/check-license', [MypageProfileController::class, 'checkLicense'])->name('profile_edit.check-license');
    });
    Route::post('/membership-payment/cancel', [MypageAnnualFeeController::class, 'cancelPending'])->name('membership_payment.cancel');
    Route::get('/secession', [MypageProfileController::class, 'secession'])->name('secession');
    Route::get('/hospital_information', [MypageProfileController::class, 'hospitalInformation'])->name('hospital_information');
    Route::get('/executive_activities', [MypageProfileController::class, 'executiveActivities'])->name('executive_activities');
    Route::get('/committee_participation', [MypageProfileController::class, 'committeeParticipation'])->name('committee_participation');
    Route::get('/committee_participation_admin', [MypageProfileController::class, 'committeeParticipationAdmin'])->name('committee_participation_admin');

    // 연회비 납부
    Route::get('/annual_fee', [MypageAnnualFeeController::class, 'index'])->name('annual_fee');
    Route::post('/annual_fee', [MypageAnnualFeeController::class, 'store'])->name('annual_fee.store');
    Route::get('/annual_fee/end', [MypageAnnualFeeController::class, 'end'])->name('annual_fee_end');

    // 참가내역·수강·즐겨찾기·북마크
    Route::get('/participation_history', [MypageHistoryController::class, 'participation'])->name('participation_history');
    Route::get('/participation_history/view', [MypageHistoryController::class, 'participationView'])->name('participation_history_view');
    Route::get('/online_training', [MypageHistoryController::class, 'onlineTraining'])->name('online_training');
    Route::get('/online_training/view', [MypageHistoryController::class, 'onlineTrainingView'])->name('online_training_view');
    Route::get('/favorite', [MypageHistoryController::class, 'favoriteMenu'])->name('favorite_menu');
    Route::post('/favorite', [MypageHistoryController::class, 'favoriteMenuStore'])->name('favorite_menu.store');
    Route::get('/bookmark', [MypageHistoryController::class, 'bookmark'])->name('bookmark');
    Route::post('/bookmark/destroy', [MypageHistoryController::class, 'bookmarkDestroy'])->name('bookmark.destroy');

    // 1:1 문의
    Route::get('/inquiry', [MypageInquiryController::class, 'index'])->name('inquiry');
    Route::get('/inquiry/view', [MypageInquiryController::class, 'show'])->name('inquiry_view');
    Route::get('/inquiry/write', [MypageInquiryController::class, 'create'])->name('inquiry_write');
    Route::get('/inquiry/edit', [MypageInquiryController::class, 'edit'])->name('inquiry_edit');
    Route::post('/inquiry', [MypageInquiryController::class, 'store'])->name('inquiry.store');
    Route::put('/inquiry/{id}', [MypageInquiryController::class, 'update'])->name('inquiry.update');
    Route::delete('/inquiry/{id}', [MypageInquiryController::class, 'destroy'])->name('inquiry.destroy');

    // 출력(PDF 저장)
    Route::get('/print_receipt', [MypagePrintController::class, 'receipt'])->name('print_receipt');
    Route::get('/print_receipt_save', [MypagePrintController::class, 'receiptSave'])->name('print_receipt_save');
    Route::get('/print_participation', [MypagePrintController::class, 'participation'])->name('print_participation');
    Route::get('/print_completion', [MypagePrintController::class, 'completion'])->name('print_completion');
    Route::get('/print_letter_appointment', [MypagePrintController::class, 'letterAppointment'])->name('print_letter_appointment');
});

// 통합검색
Route::get('/total_search', [TotalSearchController::class, 'index'])->name('total_search');

// 메일폼
Route::prefix('mailform')->name('mailform.')->group(function () {
    Route::get('/mail_welcome_approved', [MailformController::class, 'welcomeApproved'])->name('mail_welcome_approved');
    Route::get('/mail_password_changed', [MailformController::class, 'passwordChanged'])->name('mail_password_changed');
    Route::get('/mail_password_reset', [MailformController::class, 'passwordReset'])->name('mail_password_reset');
    Route::get('/mail_verification_expired', [MailformController::class, 'verificationExpired'])->name('mail_verification_expired');
    Route::get('/mail_dormant_account_recovery', [MailformController::class, 'dormantAccountRecovery'])->name('mail_dormant_account_recovery');
    Route::get('/mail_membership_fee_paid_card', [MailformController::class, 'membershipFeePaidCard'])->name('mail_membership_fee_paid_card');
    Route::get('/mail_membership_fee_paid_online', [MailformController::class, 'membershipFeePaidOnline'])->name('mail_membership_fee_paid_online');
    Route::get('/mail_pre_registration_complete', [MailformController::class, 'preRegistrationComplete'])->name('mail_pre_registration_complete');
    Route::get('/mail_course_application_complete', [MailformController::class, 'courseApplicationComplete'])->name('mail_course_application_complete');
    Route::get('/mail_application_received', [MailformController::class, 'applicationReceived'])->name('mail_application_received');
    Route::get('/mail_application_rejected', [MailformController::class, 'applicationRejected'])->name('mail_application_rejected');
    Route::get('/mail_approval_result', [MailformController::class, 'approvalResult'])->name('mail_approval_result');
});

// 학술대회 (Phase 5 분리: Main / Kifm / Program / Registration / Abstract / Notice / Etc / Onsite)
Route::prefix('academic_conference')->name('academic_conference.')->group(function () {
    // 메인
    Route::get('/', [AcademicConferenceMainController::class, 'index'])->name('index');

    // KIFM (초대의 글·조직위·행사장)
    Route::get('/invitation', [AcademicConferenceKifmController::class, 'invitation'])->name('invitation');
    Route::get('/committee', [AcademicConferenceKifmController::class, 'committee'])->name('committee');
    Route::get('/venue', [AcademicConferenceKifmController::class, 'venue'])->name('venue');

    // Program (프로그램·연자)
    Route::get('/program', [AcademicConferenceProgramController::class, 'program'])->name('program');
    Route::get('/speakers', [AcademicConferenceProgramController::class, 'speakers'])->name('speakers');

    // Registration (사전등록)
    Route::get('/registration', [AcademicConferenceRegistrationController::class, 'info'])->name('info');
    Route::get('/registration/reg', [AcademicConferenceRegistrationController::class, 'reg'])->name('reg');
    Route::get('/registration/form', [AcademicConferenceRegistrationController::class, 'regForm'])->name('reg_form');
    Route::get('/registration/form_non_member', [AcademicConferenceRegistrationController::class, 'regFormNonMember'])->name('reg_form_non_member');
    Route::get('/registration/end', [AcademicConferenceRegistrationController::class, 'regEnd'])->name('reg_end');
    Route::get('/registration/check_member', [AcademicConferenceRegistrationController::class, 'regCheckMember'])->name('reg_check_member');
    Route::get('/registration/check_non_member', [AcademicConferenceRegistrationController::class, 'regCheckNonMember'])->name('reg_check_non_member');
    Route::get('/registration/result', [AcademicConferenceRegistrationController::class, 'regResult'])->name('reg_result');

    // Abstract (초록)
    Route::get('/abstract', [AcademicConferenceAbstractController::class, 'info'])->name('abstract_info');
    Route::get('/abstract/submission', [AcademicConferenceAbstractController::class, 'submit'])->name('abstract_submit');
    Route::get('/abstract/form_member', [AcademicConferenceAbstractController::class, 'formMember'])->name('abstract_form_member');
    Route::get('/abstract/form_non_member', [AcademicConferenceAbstractController::class, 'formNonMember'])->name('abstract_form_non_member');
    Route::get('/abstract/complete', [AcademicConferenceAbstractController::class, 'complete'])->name('abstract_complete');
    Route::get('/abstract/check', [AcademicConferenceAbstractController::class, 'check'])->name('abstract_check');
    Route::get('/abstract/modify', [AcademicConferenceAbstractController::class, 'modify'])->name('abstract_modify');

    // Notice (공지)
    Route::get('/notice', [AcademicConferenceNoticeController::class, 'index'])->name('notice');
    Route::get('/notice/view', [AcademicConferenceNoticeController::class, 'show'])->name('notice_view');

    // Etc (후원·전시)
    Route::get('/sponsors', [AcademicConferenceEtcController::class, 'sponsors'])->name('sponsors');
    Route::get('/exhibition', [AcademicConferenceEtcController::class, 'exhibition'])->name('exhibition');

    // Onsite (현장 등록)
    Route::get('/onsite', [AcademicConferenceOnsiteController::class, 'intro'])->name('onsite');
    Route::get('/onsite_info', [AcademicConferenceOnsiteController::class, 'info'])->name('onsite_info');
    Route::get('/onsite_member_registration', [AcademicConferenceOnsiteController::class, 'memberReg'])->name('onsite_member_reg');
    Route::get('/onsite_non_member_registration', [AcademicConferenceOnsiteController::class, 'nonMemberReg'])->name('onsite_non_member_reg');
    Route::get('/onsite_welcome_approved', [AcademicConferenceOnsiteController::class, 'welcomeApproved'])->name('onsite_welcome_approved');
    Route::get('/onsite_check_registration', [AcademicConferenceOnsiteController::class, 'checkRegistration'])->name('onsite_check_registration');
    Route::get('/onsite_check_non_registration', [AcademicConferenceOnsiteController::class, 'checkNonRegistration'])->name('onsite_check_non_registration');
    Route::get('/onsite_confirmation_complete', [AcademicConferenceOnsiteController::class, 'confirmationComplete'])->name('onsite_confirmation_complete');
});
