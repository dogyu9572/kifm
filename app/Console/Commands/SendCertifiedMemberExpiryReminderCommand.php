<?php

namespace App\Console\Commands;

use App\Models\CertifiedMember;
use App\Services\Frontend\MailformNotificationService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class SendCertifiedMemberExpiryReminderCommand extends Command
{
    protected $signature = 'certified-members:send-expiry-reminders
        {--days=30 : 만료일까지 남은 일수}
        {--dry-run : 대상만 확인하고 메일을 발송하지 않음}
        {--force : 중복 발송 방지 캐시를 무시하고 발송}';

    protected $description = '인정의 유효기간 만료 예정 안내 메일을 발송';

    public function handle(MailformNotificationService $mailNotifier): int
    {
        $days = max(1, (int) $this->option('days'));
        $targetDate = now()->addDays($days)->toDateString();
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $sent = 0;
        $skipped = 0;

        $this->info("인정의 만료 예정일 {$targetDate} 대상 조회");

        $query = CertifiedMember::query()
            ->with('member')
            ->whereDate('validity_end_date', $targetDate)
            ->whereHas('member', function (Builder $query): void {
                $query->where('role', 'user')
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->whereNull('withdrawn_at');
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('certified_members as newer')
                    ->whereColumn('newer.member_id', 'certified_members.member_id')
                    ->whereColumn('newer.validity_end_date', '>', 'certified_members.validity_end_date');
            })
            ->orderBy('id');

        foreach ($query->cursor() as $certifiedMember) {
            $cacheKey = $this->cacheKey($certifiedMember);
            $member = $certifiedMember->member;
            $label = "#{$certifiedMember->id} {$member?->name} {$member?->email}";

            if (! $force && Cache::has($cacheKey)) {
                $skipped++;
                $this->line("SKIP {$label} 이미 발송됨");
                continue;
            }

            if ($dryRun) {
                $sent++;
                $this->line("DRY {$label}");
                continue;
            }

            $mailNotifier->sendCertifiedMemberExpiryReminder($certifiedMember);
            Cache::forever($cacheKey, now()->toDateTimeString());
            $sent++;
            $this->line("SEND {$label}");
        }

        $this->info("완료: 대상 {$sent}건, 중복 제외 {$skipped}건");

        return self::SUCCESS;
    }

    private function cacheKey(CertifiedMember $certifiedMember): string
    {
        return sprintf(
            'mail:certified-member-expiry:%d:%s',
            $certifiedMember->id,
            optional($certifiedMember->validity_end_date)->format('Ymd') ?: 'none'
        );
    }
}
