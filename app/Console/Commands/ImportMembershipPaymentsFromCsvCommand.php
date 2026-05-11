<?php

namespace App\Console\Commands;

use App\Services\Backoffice\MembershipPaymentCsvImporter;
use Illuminate\Console\Command;

class ImportMembershipPaymentsFromCsvCommand extends Command
{
    protected $signature = 'memberships:import-csv
        {path? : CSV 경로 (기본: docs/data-migration/회원정보_회비납부내역.csv)}
        {--dry-run : DB 반영 없이 건수만 집계}
        {--limit= : 최대 처리 행 수}
        {--on-duplicate=skip : skip 또는 update}
        {--on-missing=skip : skip 또는 error}';

    protected $description = '회원정보_회비납부내역.csv(CP949)를 membership_payments 테이블로 이관합니다.';

    public function handle(MembershipPaymentCsvImporter $importer): int
    {
        $path = $this->argument('path')
            ?: base_path('docs/data-migration/회원정보_회비납부내역.csv');

        $dryRun = (bool) $this->option('dry-run');
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;
        $onDuplicate = (string) $this->option('on-duplicate');
        $onMissing = (string) $this->option('on-missing');

        if (! in_array($onDuplicate, ['skip', 'update'], true)) {
            $this->error('--on-duplicate 값은 skip 또는 update 만 허용됩니다.');

            return self::FAILURE;
        }
        if (! in_array($onMissing, ['skip', 'error'], true)) {
            $this->error('--on-missing 값은 skip 또는 error 만 허용됩니다.');

            return self::FAILURE;
        }

        $this->info('파일: ' . $path);
        $this->info('dry-run: ' . ($dryRun ? 'yes' : 'no'));
        $this->info('on-duplicate: ' . $onDuplicate);
        $this->info('on-missing: ' . $onMissing);

        try {
            $stats = $importer->import($path, $dryRun, $limit, $onDuplicate, $onMissing);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(['항목', '건수'], [
            ['신규', $stats['created']],
            ['갱신', $stats['updated']],
            ['스킵', $stats['skipped']],
            ['회원 미존재', $stats['missing']],
        ]);

        foreach (array_slice($stats['errors'], 0, 50) as $err) {
            $this->warn($err);
        }
        if (count($stats['errors']) > 50) {
            $this->warn('… 외 ' . (count($stats['errors']) - 50) . '건 오류 생략');
        }

        return self::SUCCESS;
    }
}

