<?php

namespace Database\Seeders;

use App\Models\CommunityCommittee;
use App\Models\CommunityCommitteeApplication;
use App\Models\CommunityCommitteeMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunityCommitteeApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $committees = CommunityCommittee::query()->orderBy('id', 'asc')->get();
        if ($committees->isEmpty()) {
            $this->command?->warn('community_committees 데이터가 없어 신청 샘플을 생성하지 못했습니다.');
            return;
        }

        $members = User::query()->where('role', 'member')->orderBy('id', 'asc')->limit(20)->get();
        if ($members->isEmpty()) {
            $members = User::query()->orderBy('id', 'asc')->limit(20)->get();
        }
        if ($members->isEmpty()) {
            $this->command?->warn('users 데이터가 없어 신청 샘플을 생성하지 못했습니다.');
            return;
        }

        CommunityCommitteeApplication::query()->delete();

        $statusPool = ['PENDING', 'APPROVED', 'REJECTED', 'APPROVED', 'PENDING', 'REJECTED', 'APPROVED', 'PENDING', 'APPROVED', 'REJECTED'];

        for ($i = 1; $i <= 10; $i++) {
            $committee = $committees->get(($i - 1) % $committees->count());
            $member = $members->get(($i - 1) % $members->count());
            $status = $statusPool[$i - 1];
            $processedAt = $status === 'PENDING' ? null : now()->subDays(11 - $i);
            $rejectReason = $status === 'REJECTED' ? '샘플 반려 사유 #' . $i : null;

            $application = CommunityCommitteeApplication::query()->create([
                'community_committee_id' => $committee->id,
                'user_id' => $member->id,
                'applicant_name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone_number,
                'status' => $status,
                'reject_reason' => $rejectReason,
                'applied_at' => now()->subDays(20 - $i),
                'processed_at' => $processedAt,
                'processed_by' => null,
            ]);

            if ($status === 'APPROVED' && $application->user_id) {
                CommunityCommitteeMember::query()->firstOrCreate([
                    'community_committee_id' => $committee->id,
                    'user_id' => $application->user_id,
                ], [
                    'role' => 'member',
                ]);
            }
        }

        foreach ($committees as $committee) {
            $committee->pending_count = CommunityCommitteeApplication::query()
                ->where('community_committee_id', $committee->id)
                ->where('status', 'PENDING')
                ->count();
            $committee->member_count = CommunityCommitteeMember::query()
                ->where('community_committee_id', $committee->id)
                ->count();
            $committee->save();
        }
    }
}
