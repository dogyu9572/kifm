<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Certified\CertifiedQualificationSyncService;
use Illuminate\Console\Command;

class SyncCertifiedQualificationsCommand extends Command
{
    protected $signature = 'certified-members:sync-qualifications {--member_id= : 특정 회원 ID만 동기화}';

    protected $description = '완료된 학술대회/연수강좌 이력을 기준으로 인정의 취득 및 갱신 데이터를 동기화';

    public function handle(CertifiedQualificationSyncService $syncService): int
    {
        $memberId = (int) ($this->option('member_id') ?: 0);
        $query = User::query()
            ->where('role', 'user')
            ->whereNull('withdrawn_at');

        if ($memberId > 0) {
            $query->whereKey($memberId);
        }

        $count = 0;
        $query->orderBy('id')->chunkById(200, function ($members) use ($syncService, &$count): void {
            foreach ($members as $member) {
                $syncService->syncForMember($member);
                $count++;
            }
        });

        $this->info("인정의 자격 동기화 대상 {$count}명 처리 완료");

        return self::SUCCESS;
    }
}
