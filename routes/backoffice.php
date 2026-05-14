<?php

use App\Http\Controllers\Backoffice\AccessStatisticsController;
use App\Http\Controllers\Backoffice\AddressBookController;
use App\Http\Controllers\Backoffice\AdminController;
use App\Http\Controllers\Backoffice\AdminGroupController;
use App\Http\Controllers\Backoffice\AdminMenuController;
use App\Http\Controllers\Backoffice\AnnualScheduleController;
use App\Http\Controllers\Backoffice\AuthController;
use App\Http\Controllers\Backoffice\BannerController;
use App\Http\Controllers\Backoffice\BoardController;
use App\Http\Controllers\Backoffice\BoardPostCommentController;
use App\Http\Controllers\Backoffice\BoardPostController;
use App\Http\Controllers\Backoffice\BoardSkinController;
use App\Http\Controllers\Backoffice\BoardTemplateController;
use App\Http\Controllers\Backoffice\CategoryController;
use App\Http\Controllers\Backoffice\CertifiedMemberController;
use App\Http\Controllers\Backoffice\CommunityCommitteeController;
use App\Http\Controllers\Backoffice\CouponController;
use App\Http\Controllers\Backoffice\CouponUsageHistoryController;
use App\Http\Controllers\Backoffice\EduTrainingController;
use App\Http\Controllers\Backoffice\EduCourseController;
use App\Http\Controllers\Backoffice\EduCourseEnrollmentController;
use App\Http\Controllers\Backoffice\EduTrainingPaymentController;
use App\Http\Controllers\Backoffice\AcademicEventController;
use App\Http\Controllers\Backoffice\AcademicEventSessionController;
use App\Http\Controllers\Backoffice\AcademicEventAbstractController;
use App\Http\Controllers\Backoffice\AcademicEventRegistrationController;
use App\Http\Controllers\Backoffice\AcademicSponsorMasterController;
use App\Http\Controllers\Backoffice\AcademicHotelController;
use App\Http\Controllers\Backoffice\LocalDoctorController;
use App\Http\Controllers\Backoffice\DoctorCategoryController;
use App\Http\Controllers\Backoffice\LogController;
use App\Http\Controllers\Backoffice\MailController;
use App\Http\Controllers\Backoffice\MemberController;
use App\Http\Controllers\Backoffice\MemberExecutiveController;
use App\Http\Controllers\Backoffice\MembershipPaymentController;
use App\Http\Controllers\Backoffice\OneOnOneInquiryController;
use App\Http\Controllers\Backoffice\PaymentPlanController;
use App\Http\Controllers\Backoffice\PopupController;
use App\Http\Controllers\Backoffice\SettingController;
use App\Http\Controllers\Backoffice\SmsController;
use App\Http\Controllers\Backoffice\SocietyExecutiveController;
use App\Http\Controllers\Backoffice\StatsEventController;
use App\Http\Controllers\Backoffice\StatsMemberController;
use App\Http\Controllers\Backoffice\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =============================================================================
// 백오피스 인증 라우트
// =============================================================================
Route::prefix('backoffice')->name('backoffice.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// =============================================================================
// 백오피스 라우트 (관리자 전용)
// =============================================================================

Route::prefix('backoffice')->middleware(['backoffice'])->group(function () {

    // 대시보드
    Route::get('/', [App\Http\Controllers\Backoffice\DashboardController::class, 'index'])
        ->name('backoffice.dashboard');

    // 대시보드 API
    Route::get('/api/statistics', [App\Http\Controllers\Backoffice\DashboardController::class, 'statistics'])
        ->name('backoffice.api.statistics');

    // -------------------------------------------------------------------------
    // 시스템 관리
    // -------------------------------------------------------------------------

    // 관리자 메뉴 관리
    Route::resource('admin-menus', AdminMenuController::class, [
        'names' => 'backoffice.admin-menus',
    ])->except(['show']);

    // 메뉴 순서 업데이트
    Route::post('admin-menus/update-order', [AdminMenuController::class, 'updateOrder'])
        ->name('backoffice.admin-menus.update-order');

    // 메뉴 부모 업데이트 (드래그로 메뉴 이동)
    Route::post('admin-menus/update-parent', [AdminMenuController::class, 'updateParent'])
        ->name('backoffice.admin-menus.update-parent');

    // 카테고리 관리
    // 카테고리 순서 업데이트 (resource 라우트보다 앞에 위치)
    Route::post('categories/update-order', [CategoryController::class, 'updateOrder'])
        ->name('backoffice.categories.update-order');

    // 활성 카테고리 조회 (AJAX - resource 라우트보다 앞에 위치)
    Route::get('categories/active/{group}', [CategoryController::class, 'getActiveCategories'])
        ->name('backoffice.categories.active');

    // 특정 그룹의 1차 카테고리 조회 (AJAX)
    Route::get('categories/get-by-group/{groupId}', [CategoryController::class, 'getByGroup'])
        ->name('backoffice.categories.get-by-group');

    // 카테고리 수정용 데이터 조회 (AJAX)
    Route::get('categories/{category}/edit-data', [CategoryController::class, 'getEditData'])
        ->name('backoffice.categories.edit-data');

    // 인라인 수정 (AJAX)
    Route::post('categories/{category}/update-inline', [CategoryController::class, 'updateInline'])
        ->name('backoffice.categories.update-inline');

    // 모달 등록 (AJAX)
    Route::post('categories/store-modal', [CategoryController::class, 'storeModal'])
        ->name('backoffice.categories.store-modal');

    // 모달 수정 (AJAX)
    Route::put('categories/update-modal', [CategoryController::class, 'updateModal'])
        ->name('backoffice.categories.update-modal');

    // 미리 생성될 코드 조회 (AJAX)
    Route::post('categories/generate-preview-code', [CategoryController::class, 'generatePreviewCode'])
        ->name('backoffice.categories.generate-preview-code');

    Route::resource('categories', CategoryController::class, [
        'names' => 'backoffice.categories',
    ])->except(['show']);

    // 기본설정 관리
    Route::get('setting', [SettingController::class, 'index'])
        ->name('backoffice.setting.index');
    Route::post('setting', [SettingController::class, 'update'])
        ->name('backoffice.setting.update');

    // 접속 로그 관리
    Route::get('logs/access', [LogController::class, 'access'])
        ->name('backoffice.logs.access');
    Route::get('user-access-logs', [LogController::class, 'userAccessLogs'])
        ->name('backoffice.user-access-logs');
    Route::get('admin-access-logs', [LogController::class, 'adminAccessLogs'])
        ->name('backoffice.admin-access-logs');

    // 통계 관리
    Route::get('stats/members', [StatsMemberController::class, 'index'])
        ->name('backoffice.stats.members.index');
    Route::get('stats/events', [StatsEventController::class, 'index'])
        ->name('backoffice.stats.events.index');
    Route::get('access-statistics', [AccessStatisticsController::class, 'index'])
        ->name('backoffice.access-statistics');
    Route::get('access-statistics/get-statistics', [AccessStatisticsController::class, 'getStatistics'])
        ->name('backoffice.access-statistics.get-statistics');

    // 관리자 계정 관리
    Route::post('admins/bulk-destroy', [AdminController::class, 'bulkDestroy'])
        ->name('backoffice.admins.bulk-destroy');
    Route::post('admins/check-login-id', [AdminController::class, 'checkLoginId'])
        ->name('backoffice.admins.check-login-id');
    Route::resource('admins', AdminController::class, [
        'names' => 'backoffice.admins',
    ]);

    // 관리자 권한 그룹 관리
    Route::resource('admin-groups', AdminGroupController::class, [
        'names' => 'backoffice.admin-groups',
    ])->except(['show']);

    // 권한 그룹 권한 설정
    Route::get('admin-groups/{admin_group}/permissions', [AdminGroupController::class, 'editPermissions'])
        ->name('backoffice.admin-groups.permissions.edit');
    Route::post('admin-groups/{admin_group}/permissions', [AdminGroupController::class, 'updatePermissions'])
        ->name('backoffice.admin-groups.permissions.update');

    // -------------------------------------------------------------------------
    // 콘텐츠 관리
    // -------------------------------------------------------------------------

    // 이미지 업로드
    Route::post('upload-image', function (Request $request) {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/editor', 'public');

            return response()->json([
                'uploaded' => true,
                'url' => asset('storage/'.$path),
            ]);
        }

        return response()->json([
            'uploaded' => false,
            'error' => ['message' => '이미지 업로드에 실패했습니다.'],
        ]);
    });

    // 정렬 순서 업데이트
    Route::post('board-posts/update-sort-order', [BoardPostController::class, 'updateSortOrder'])->name('backoffice.board-posts.update-sort-order');

    // 연간 일정 관리 (전용 MVC)
    Route::get('annual_schedule', [AnnualScheduleController::class, 'index'])->name('backoffice.annual_schedule.index');
    Route::get('annual_schedule/create', [AnnualScheduleController::class, 'create'])->name('backoffice.annual_schedule.create');
    Route::post('annual_schedule', [AnnualScheduleController::class, 'store'])->name('backoffice.annual_schedule.store');
    Route::post('annual_schedule/bulk-destroy', [AnnualScheduleController::class, 'bulkDestroy'])->name('backoffice.annual_schedule.bulk-destroy');
    Route::get('annual_schedule/{annualSchedule}/edit', [AnnualScheduleController::class, 'edit'])->name('backoffice.annual_schedule.edit');
    Route::put('annual_schedule/{annualSchedule}', [AnnualScheduleController::class, 'update'])->name('backoffice.annual_schedule.update');
    Route::delete('annual_schedule/{annualSchedule}', [AnnualScheduleController::class, 'destroy'])->name('backoffice.annual_schedule.destroy');
    Route::redirect('board-posts/annual_schedule', 'annual_schedule', 301);

    // 임원진 관리 (전용 MVC)
    Route::get('society-executives', [SocietyExecutiveController::class, 'index'])->name('backoffice.society-executives.index');
    Route::get('society-executives/create', [SocietyExecutiveController::class, 'create'])->name('backoffice.society-executives.create');
    Route::post('society-executives', [SocietyExecutiveController::class, 'store'])->name('backoffice.society-executives.store');
    Route::post('society-executives/bulk-destroy', [SocietyExecutiveController::class, 'bulkDestroy'])->name('backoffice.society-executives.bulk-destroy');
    Route::post('society-executives/update-sort-order', [SocietyExecutiveController::class, 'updateSortOrder'])->name('backoffice.society-executives.update-sort-order');
    Route::get('society-executives/{societyExecutive}/edit', [SocietyExecutiveController::class, 'edit'])->name('backoffice.society-executives.edit');
    Route::put('society-executives/{societyExecutive}', [SocietyExecutiveController::class, 'update'])->name('backoffice.society-executives.update');
    Route::delete('society-executives/{societyExecutive}', [SocietyExecutiveController::class, 'destroy'])->name('backoffice.society-executives.destroy');

    // 메일 발송 관리 (전용 MVC)
    Route::get('mails', [MailController::class, 'index'])->name('backoffice.mails.index');
    Route::get('mails/create', [MailController::class, 'create'])->name('backoffice.mails.create');
    Route::post('mails', [MailController::class, 'store'])->name('backoffice.mails.store');
    Route::get('mails/{mail}/edit', [MailController::class, 'edit'])->name('backoffice.mails.edit');
    Route::put('mails/{mail}', [MailController::class, 'update'])->name('backoffice.mails.update');
    Route::delete('mails/{mail}', [MailController::class, 'destroy'])->name('backoffice.mails.destroy');
    Route::post('mails/{mail}/copy', [MailController::class, 'copy'])->name('backoffice.mails.copy');
    Route::post('mails/{mail}/cancel-schedule', [MailController::class, 'cancelSchedule'])->name('backoffice.mails.cancel-schedule');

    // 문자 발송 관리 (전용 MVC)
    Route::get('sms', [SmsController::class, 'index'])->name('backoffice.sms.index');
    Route::get('sms/create', [SmsController::class, 'create'])->name('backoffice.sms.create');
    Route::post('sms', [SmsController::class, 'store'])->name('backoffice.sms.store');
    Route::get('sms/{sms}/edit', [SmsController::class, 'edit'])->name('backoffice.sms.edit');
    Route::put('sms/{sms}', [SmsController::class, 'update'])->name('backoffice.sms.update');
    Route::delete('sms/{sms}', [SmsController::class, 'destroy'])->name('backoffice.sms.destroy');
    Route::post('sms/{sms}/copy', [SmsController::class, 'copy'])->name('backoffice.sms.copy');
    Route::post('sms/{sms}/cancel-schedule', [SmsController::class, 'cancelSchedule'])->name('backoffice.sms.cancel-schedule');

    // 결제 항목 관리
    Route::post('payment-plans/bulk-destroy', [PaymentPlanController::class, 'bulkDestroy'])
        ->name('backoffice.payment-plans.bulk-destroy');
    Route::resource('payment-plans', PaymentPlanController::class, [
        'names' => 'backoffice.payment-plans',
    ])->except(['show']);
    Route::post('payment-memberships/{payment}/confirm-deposit', [MembershipPaymentController::class, 'confirmDeposit'])
        ->name('backoffice.payment-memberships.confirm-deposit');
    Route::resource('payment-memberships', MembershipPaymentController::class, [
        'names' => 'backoffice.payment-memberships',
        'parameters' => ['payment-memberships' => 'payment'],
    ])->only(['index', 'edit', 'update', 'destroy']);

    // 쿠폰 관리
    Route::post('coupons/generate-code', [CouponController::class, 'generateCode'])
        ->name('backoffice.coupons.generate-code');
    Route::post('coupons/bulk-destroy', [CouponController::class, 'bulkDestroy'])
        ->name('backoffice.coupons.bulk-destroy');
    Route::resource('coupons', CouponController::class, [
        'names' => 'backoffice.coupons',
    ])->except(['show']);
    Route::get('coupon-usage-history', [CouponUsageHistoryController::class, 'index'])
        ->name('backoffice.coupon-usage-history.index');

    // 연수교육 관리
    Route::post('edu-trainings/bulk-destroy', [EduTrainingController::class, 'bulkDestroy'])
        ->name('backoffice.edu-trainings.bulk-destroy');
    Route::resource('edu-trainings', EduTrainingController::class, [
        'names' => 'backoffice.edu-trainings',
    ])->except(['show']);
    Route::post('edu-courses/bulk-destroy', [EduCourseController::class, 'bulkDestroy'])
        ->name('backoffice.edu-courses.bulk-destroy');
    Route::get('edu-courses/search-members', [EduCourseController::class, 'searchMembers'])
        ->name('backoffice.edu-courses.search-members');
    Route::resource('edu-courses', EduCourseController::class, [
        'names' => 'backoffice.edu-courses',
        'parameters' => ['edu-courses' => 'edu_course'],
    ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('edu-course-enrollments/bulk-destroy', [EduCourseEnrollmentController::class, 'bulkDestroy'])
        ->name('backoffice.edu-course-enrollments.bulk-destroy');
    Route::get('edu-course-enrollments/export', [EduCourseEnrollmentController::class, 'export'])
        ->name('backoffice.edu-course-enrollments.export');
    Route::resource('edu-course-enrollments', EduCourseEnrollmentController::class, [
        'names' => 'backoffice.edu-course-enrollments',
        'parameters' => ['edu-course-enrollments' => 'edu_course_enrollment'],
    ])->only(['index', 'show', 'update']);
    Route::post('edu-training-payments/bulk-cancel', [EduTrainingPaymentController::class, 'bulkCancel'])
        ->name('backoffice.edu-training-payments.bulk-cancel');
    Route::get('edu-training-payments/export', [EduTrainingPaymentController::class, 'export'])
        ->name('backoffice.edu-training-payments.export');
    Route::get('edu-training-payments/search-members', [EduTrainingPaymentController::class, 'searchMembers'])
        ->name('backoffice.edu-training-payments.search-members');
    Route::get('edu-training-payments/search-payment-plans', [EduTrainingPaymentController::class, 'searchPaymentPlans'])
        ->name('backoffice.edu-training-payments.search-payment-plans');
    Route::post('edu-training-payments/{edu_training_payment}/bank-confirm', [EduTrainingPaymentController::class, 'confirmDeposit'])
        ->name('backoffice.edu-training-payments.confirm-deposit');
    Route::post('edu-training-payments/{edu_training_payment}/cancel', [EduTrainingPaymentController::class, 'cancel'])
        ->name('backoffice.edu-training-payments.cancel');
    Route::resource('edu-training-payments', EduTrainingPaymentController::class, [
        'names' => 'backoffice.edu-training-payments',
        'parameters' => ['edu-training-payments' => 'edu_training_payment'],
    ])->only(['index', 'create', 'store', 'edit', 'update']);

    // 학술 행사
    Route::post('academic-events/bulk-destroy', [AcademicEventController::class, 'bulkDestroy'])
        ->name('backoffice.academic-events.bulk-destroy');
    Route::get('academic-events/search-members', [AcademicEventController::class, 'searchMembers'])
        ->name('backoffice.academic-events.search-members');
    Route::get('academic-events/search-abstracts', [AcademicEventController::class, 'searchAbstracts'])
        ->name('backoffice.academic-events.search-abstracts');
    Route::get('academic-sponsor-masters/search', [AcademicSponsorMasterController::class, 'search'])
        ->name('backoffice.academic-sponsor-masters.search');
    Route::post('academic-sponsor-masters/quick-store', [AcademicSponsorMasterController::class, 'quickStore'])
        ->name('backoffice.academic-sponsor-masters.quick-store');
    Route::post('academic-sponsor-masters/bulk-destroy', [AcademicSponsorMasterController::class, 'bulkDestroy'])
        ->name('backoffice.academic-sponsor-masters.bulk-destroy');
    Route::post('academic-sponsor-masters/update-sort-order', [AcademicSponsorMasterController::class, 'updateSortOrder'])
        ->name('backoffice.academic-sponsor-masters.update-sort-order');
    Route::resource('academic-sponsor-masters', AcademicSponsorMasterController::class, [
        'names' => 'backoffice.academic-sponsor-masters',
        'parameters' => ['academic-sponsor-masters' => 'academic_sponsor_master'],
    ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::post('academic-hotels/bulk-destroy', [AcademicHotelController::class, 'bulkDestroy'])
        ->name('backoffice.academic-hotels.bulk-destroy');
    Route::resource('academic-hotels', AcademicHotelController::class, [
        'names' => 'backoffice.academic-hotels',
        'parameters' => ['academic-hotels' => 'academic_hotel'],
    ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('academic-events', AcademicEventController::class, [
        'names' => 'backoffice.academic-events',
        'parameters' => ['academic-events' => 'academic_event'],
    ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('academic-events/{academic_event}/sessions/create', [AcademicEventSessionController::class, 'create'])
        ->name('backoffice.academic-events.sessions.create');
    Route::post('academic-events/{academic_event}/sessions', [AcademicEventSessionController::class, 'store'])
        ->name('backoffice.academic-events.sessions.store');
    Route::get('academic-events/{academic_event}/sessions/{academic_event_session}/edit', [AcademicEventSessionController::class, 'edit'])
        ->name('backoffice.academic-events.sessions.edit');
    Route::put('academic-events/{academic_event}/sessions/{academic_event_session}', [AcademicEventSessionController::class, 'update'])
        ->name('backoffice.academic-events.sessions.update');
    Route::delete('academic-events/{academic_event}/sessions/{academic_event_session}', [AcademicEventSessionController::class, 'destroy'])
        ->name('backoffice.academic-events.sessions.destroy');

    Route::post('academic-event-registrations/bulk-destroy', [AcademicEventRegistrationController::class, 'bulkDestroy'])
        ->name('backoffice.academic-event-registrations.bulk-destroy');
    Route::get('academic-event-registrations/export', [AcademicEventRegistrationController::class, 'export'])
        ->name('backoffice.academic-event-registrations.export');
    Route::get('academic-event-registrations/search-members', [AcademicEventRegistrationController::class, 'searchMembers'])
        ->name('backoffice.academic-event-registrations.search-members');
    Route::get('academic-event-registrations/search-payment-plans', [AcademicEventRegistrationController::class, 'searchPaymentPlans'])
        ->name('backoffice.academic-event-registrations.search-payment-plans');
    Route::resource('academic-event-registrations', AcademicEventRegistrationController::class, [
        'names' => 'backoffice.academic-event-registrations',
        'parameters' => ['academic-event-registrations' => 'academic_event_registration'],
    ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::post('academic-event-abstracts/bulk-destroy', [AcademicEventAbstractController::class, 'bulkDestroy'])
        ->name('backoffice.academic-event-abstracts.bulk-destroy');
    Route::get('academic-event-abstracts/search-members', [AcademicEventAbstractController::class, 'searchMembers'])
        ->name('backoffice.academic-event-abstracts.search-members');
    Route::resource('academic-event-abstracts', AcademicEventAbstractController::class, [
        'names' => 'backoffice.academic-event-abstracts',
        'parameters' => ['academic-event-abstracts' => 'academic_event_abstract'],
    ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // 우리동네주치의
    Route::get('local-doctors/search-members', [LocalDoctorController::class, 'searchMembers'])
        ->name('backoffice.local-doctors.search-members');
    Route::post('local-doctors/bulk-destroy', [LocalDoctorController::class, 'bulkDestroy'])
        ->name('backoffice.local-doctors.bulk-destroy');
    Route::resource('local-doctors', LocalDoctorController::class, [
        'names' => 'backoffice.local-doctors',
        'parameters' => ['local-doctors' => 'local_doctor'],
    ])->except(['show']);
    Route::resource('doctor-categories', DoctorCategoryController::class, [
        'names' => 'backoffice.doctor-categories',
        'parameters' => ['doctor-categories' => 'doctor_category'],
    ])->except(['show']);

    // 주소록 관리 (전용 MVC)
    Route::get('address-books', [AddressBookController::class, 'index'])->name('backoffice.address-books.index');
    Route::get('address-books/create', [AddressBookController::class, 'create'])->name('backoffice.address-books.create');
    Route::post('address-books', [AddressBookController::class, 'store'])->name('backoffice.address-books.store');
    Route::get('address-books/{addressBook}/edit', [AddressBookController::class, 'edit'])->name('backoffice.address-books.edit');
    Route::put('address-books/{addressBook}', [AddressBookController::class, 'update'])->name('backoffice.address-books.update');
    Route::delete('address-books/{addressBook}', [AddressBookController::class, 'destroy'])->name('backoffice.address-books.destroy');
    Route::get('address-books/search-members', [AddressBookController::class, 'searchMembers'])->name('backoffice.address-books.search-members');

    // 게시글 관리 (특정 게시판)
    Route::prefix('board-posts/{slug}')->name('backoffice.board-posts.')->group(function () {
        Route::get('/', [BoardPostController::class, 'index'])->name('index');
        Route::get('/create', [BoardPostController::class, 'create'])->name('create');
        Route::post('/', [BoardPostController::class, 'store'])->name('store');
        Route::get('/{post}', [BoardPostController::class, 'show'])->name('show');
        Route::get('/{post}/edit', [BoardPostController::class, 'edit'])->name('edit');
        Route::post('/{post}/comments', [BoardPostCommentController::class, 'store'])->name('comments.store');
        Route::put('/{post}/comments/{comment}', [BoardPostCommentController::class, 'update'])->name('comments.update');
        Route::delete('/{post}/comments/{comment}', [BoardPostCommentController::class, 'destroy'])->name('comments.destroy');
        Route::put('/{post}', [BoardPostController::class, 'update'])->name('update');
        Route::delete('/{post}', [BoardPostController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [BoardPostController::class, 'bulkDestroy'])->name('bulk_destroy');
    });

    // 게시판 관리
    Route::resource('boards', BoardController::class, [
        'names' => 'backoffice.boards',
    ])->except(['show']); // show는 제외 (게시글 목록과 충돌)

    // 게시판 템플릿 관리
    Route::resource('board-templates', BoardTemplateController::class, [
        'names' => 'backoffice.board-templates',
        'parameters' => ['board-templates' => 'boardTemplate'],
    ]);

    // 게시판 템플릿 추가 기능
    Route::post('board-templates/{boardTemplate}/duplicate', [BoardTemplateController::class, 'duplicate'])
        ->name('backoffice.board-templates.duplicate');
    Route::get('board-templates/{boardTemplate}/data', [BoardTemplateController::class, 'getTemplateData'])
        ->name('backoffice.board-templates.data');

    // 게시판 스킨 관리
    Route::resource('board-skins', BoardSkinController::class, [
        'names' => 'backoffice.board-skins',
        'parameters' => ['board-skins' => 'boardSkin'],
    ]);

    // 게시판 스킨 템플릿 편집
    Route::prefix('board-skins/{boardSkin}')->name('backoffice.board-skins.')->group(function () {
        Route::get('template', [BoardSkinController::class, 'editTemplate'])
            ->name('edit_template');
        Route::post('template', [BoardSkinController::class, 'updateTemplate'])
            ->name('update_template');
    });

    // 게시글 관리
    Route::resource('posts', BoardPostController::class, [
        'names' => 'backoffice.posts',
    ]);

    // 회원 관리
    Route::resource('users', UserController::class, [
        'names' => 'backoffice.users',
    ]);
    Route::get('withdrawn', [MemberController::class, 'withdrawn'])->name('backoffice.withdrawn');
    Route::post('withdrawn/{id}/restore', [MemberController::class, 'restore'])->name('backoffice.withdrawn.restore');
    Route::post('withdrawn/{id}/force-delete', [MemberController::class, 'forceDelete'])->name('backoffice.withdrawn.force-delete');
    Route::post('withdrawn/force-delete-multiple', [MemberController::class, 'forceDeleteMultiple'])->name('backoffice.withdrawn.force-delete-multiple');

    Route::resource('members', MemberController::class, [
        'names' => 'backoffice.members',
        'parameters' => ['members' => 'user'],
    ]);
    Route::post('members/check-email', [MemberController::class, 'checkDuplicateEmail'])->name('backoffice.members.check-email');
    Route::post('members/check-phone', [MemberController::class, 'checkDuplicatePhone'])->name('backoffice.members.check-phone');
    Route::get('members/search-school', [MemberController::class, 'searchSchool'])->name('backoffice.members.search-school');
    Route::post('members/delete-multiple', [MemberController::class, 'destroyMultiple'])->name('backoffice.members.delete-multiple');
    Route::get('members/export', [MemberController::class, 'export'])->name('backoffice.members.export');
    Route::resource('member-executives', MemberExecutiveController::class, [
        'names' => 'backoffice.member-executives',
    ])->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('member-executives/search-members', [MemberExecutiveController::class, 'searchMembers'])
        ->name('backoffice.member-executives.search-members');
    Route::resource('certified-members', CertifiedMemberController::class, [
        'names' => 'backoffice.certified-members',
    ])->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('certified-members/search-members', [CertifiedMemberController::class, 'searchMembers'])
        ->name('backoffice.certified-members.search-members');
    Route::get('community-committee-applicants', [CommunityCommitteeController::class, 'applicants'])
        ->name('backoffice.community-committee-applicants.index');
    Route::get('community-committee-applicants/export', [CommunityCommitteeController::class, 'exportApplicants'])
        ->name('backoffice.community-committee-applicants.export');
    Route::post('community-committee-applicants/{application}/approve', [CommunityCommitteeController::class, 'approveApplicant'])
        ->name('backoffice.community-committee-applicants.approve');
    Route::post('community-committee-applicants/{application}/reject', [CommunityCommitteeController::class, 'rejectApplicant'])
        ->name('backoffice.community-committee-applicants.reject');
    Route::post('community-committee-applicants/{application}/cancel-approval', [CommunityCommitteeController::class, 'cancelApproval'])
        ->name('backoffice.community-committee-applicants.cancel-approval');
    Route::get('community-committees/search-members', [CommunityCommitteeController::class, 'searchMembers'])
        ->name('backoffice.community-committees.search-members');
    Route::resource('community-committees', CommunityCommitteeController::class, [
        'names' => 'backoffice.community-committees',
        'parameters' => ['community-committees' => 'communityCommittee'],
    ])->except(['show']);

    // 배너 관리
    Route::resource('banners', BannerController::class, [
        'names' => 'backoffice.banners',
    ]);
    Route::post('banners/update-order', [BannerController::class, 'updateOrder'])->name('backoffice.banners.update-order');

    // 팝업 관리 (배너/팝업 등 전역)
    Route::post('popups/update-order', [PopupController::class, 'updateOrder'])->name('backoffice.popups.update-order');
    Route::resource('popups', PopupController::class, [
        'names' => 'backoffice.popups',
    ])->except(['show']);

    // 위원회 관리 > 팝업 (동일 컨트롤러, menu_scope=committee)
    Route::post('committee-popups/update-order', [PopupController::class, 'updateOrder'])->name('backoffice.committee-popups.update-order');
    Route::prefix('committee-popups')->name('backoffice.committee-popups.')->group(function () {
        Route::get('/', [PopupController::class, 'index'])->name('index');
        Route::get('/create', [PopupController::class, 'create'])->name('create');
        Route::post('/', [PopupController::class, 'store'])->name('store');
        Route::get('/{popup}/edit', [PopupController::class, 'edit'])->name('edit');
        Route::put('/{popup}', [PopupController::class, 'update'])->name('update');
        Route::delete('/{popup}', [PopupController::class, 'destroy'])->name('destroy');
    });

    // 문의사항 관리 > 1:1 문의 관리 (URL 단일 세그먼트)
    Route::post('one-on-one-inquiries/bulk-destroy', [OneOnOneInquiryController::class, 'bulkDestroy'])
        ->name('backoffice.one-on-one-inquiries.bulk-destroy');
    Route::prefix('one-on-one-inquiries')->name('backoffice.one-on-one-inquiries.')->group(function () {
        Route::get('/', [OneOnOneInquiryController::class, 'index'])->name('index');
        Route::get('/{one_on_one_inquiry}/edit', [OneOnOneInquiryController::class, 'edit'])->name('edit');
        Route::put('/{one_on_one_inquiry}', [OneOnOneInquiryController::class, 'update'])->name('update');
        Route::delete('/{one_on_one_inquiry}', [OneOnOneInquiryController::class, 'destroy'])->name('destroy');
    });

    // 세션 연장
    Route::post('session/extend', [App\Http\Controllers\Backoffice\SessionController::class, 'extend'])
        ->name('backoffice.session.extend');
});
