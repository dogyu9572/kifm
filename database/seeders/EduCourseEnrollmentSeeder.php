<?php

namespace Database\Seeders;

use App\Models\EduCourse;
use App\Models\EduCourseEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EduCourseEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = EduCourse::query()->orderBy('id')->get();
        if ($courses->isEmpty()) {
            $this->command?->warn('edu_courses 데이터가 없어 수강 신청 샘플을 생성하지 못했습니다.');
            return;
        }

        $members = User::query()->where('role', 'member')->orderBy('id')->limit(10)->get();
        if ($members->isEmpty()) {
            $members = User::query()->orderBy('id')->limit(10)->get();
        }

        if ($members->isEmpty()) {
            $this->command?->warn('users 데이터가 없어 수강 신청 샘플을 생성하지 못했습니다.');
            return;
        }

        $statusPool = ['in_progress', 'completed', 'payment_pending', 'expired'];
        $paymentStatusPool = ['pending', 'paid', 'cancelled', 'expired', 'failed'];
        $examStatusPool = ['not_attempted', 'attempted', 'passed', 'failed'];

        for ($i = 1; $i <= 10; $i++) {
            $course = $courses->get(($i - 1) % $courses->count());
            $member = $members->get(($i - 1) % $members->count());

            $progress = match ($i % 4) {
                0 => 100,
                1 => 72,
                2 => 15,
                default => 0,
            };

            $paymentStatus = $paymentStatusPool[($i - 1) % count($paymentStatusPool)];
            $enrollmentStatus = $statusPool[($i - 1) % count($statusPool)];
            $certificateStatus = $enrollmentStatus === 'completed' ? 'issued' : 'not_issued';
            $examStatus = $examStatusPool[($i - 1) % count($examStatusPool)];
            $examScore = $examStatus === 'passed' ? 100 : ($examStatus === 'failed' ? 55 : null);

            EduCourseEnrollment::query()->create([
                'edu_course_id' => $course->id,
                'member_id' => $member->id,
                'member_name' => $member->name,
                'member_grade_at' => $member->member_level ?? '정회원',
                'enrollment_status' => $enrollmentStatus,
                'progress_rate' => $progress,
                'exam_status' => $examStatus,
                'exam_score' => $examScore,
                'total_study_min' => $progress * 2,
                'last_studied_at' => now()->subDays(11 - $i),
                'certificate_status' => $certificateStatus,
                'certificate_issued_at' => $certificateStatus === 'issued' ? now()->subDays(5) : null,
                'payment_no' => sprintf('PAY-2026-%06d', 9000 + $i),
                'payment_status' => $paymentStatus,
                'payment_method' => $i % 2 === 0 ? 'card' : 'bank_transfer',
                'payment_item_name' => ($course->title ?? '강좌') . ' 수강권',
                'payment_amount' => 50000 + ($i * 1000),
                'paid_at' => $paymentStatus === 'paid' ? now()->subDays(12 - $i) : null,
                'bank_depositor' => $i % 2 === 0 ? null : $member->name,
                'bank_deposit_date' => $i % 2 === 0 ? null : now()->subDays(12 - $i)->toDateString(),
                'receipt_issue' => $i % 3 === 0 ? 'YES' : 'NO',
                'receipt_type' => $i % 3 === 0 ? 'personal' : null,
                'receipt_number' => $i % 3 === 0 ? '0101234' . sprintf('%04d', $i) : null,
                'refund_bank' => $paymentStatus === 'cancelled' ? '국민은행' : null,
                'refund_account' => $paymentStatus === 'cancelled' ? '123-456-7890' : null,
                'refund_holder' => $paymentStatus === 'cancelled' ? $member->name : null,
                'admin_memo' => '상세 확인용 샘플 데이터 #' . $i,
                'applied_at' => now()->subDays(20 - $i),
                'expire_at' => now()->addDays(30 - $i),
            ]);
        }
    }
}

