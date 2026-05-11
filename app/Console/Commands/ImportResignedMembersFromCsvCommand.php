<?php

namespace App\Console\Commands;

use App\Services\Backoffice\ResignedMemberCsvImporter;
use Illuminate\Console\Command;

class ImportResignedMembersFromCsvCommand extends Command
{
    protected $signature = 'members:import-resigned-csv
        {path? : CSV 경로 (기본: docs/data-migration/탈퇴회원.csv)}
        {--dry-run : DB 반영 없이 건수만 집계}
        {--limit= : 최대 처리 행 수}
        {--on-missing=skip : skip 또는 error}';

    protected $description = '탈퇴회원.csv(CP949)를 users 탈퇴 상태로 이관합니다.';

    public function handle(ResignedMemberCsvImporter $importer): int
    {
        $path = $this->argument('path')
            ?: base_path('docs/data-migration/탈퇴회원.csv');

        $dryRun = (bool) $this->option('dry-run');
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;
        $onMissing = (string) $this->option('on-missing');
        if (! in_array($onMissing, ['skip', 'error'], true)) {
            $this->error('--on-missing 값은 skip 또는 error 만 허용됩니다.');

            return self::FAILURE;
        }

        $this->info('파일: ' . $path);
        $this->info('dry-run: ' . ($dryRun ? 'yes' : 'no'));
        $this->info('on-missing: ' . $onMissing);

        try {
            $stats = $importer->import($path, $dryRun, $limit, $onMissing);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(['항목', '건수'], [
            ['탈퇴 처리', $stats['updated']],
            ['기존 탈퇴/스킵', $stats['skipped']],
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

