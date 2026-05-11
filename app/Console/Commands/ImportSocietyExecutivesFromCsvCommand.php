<?php

namespace App\Console\Commands;

use App\Services\Backoffice\SocietyExecutiveCsvImporter;
use Illuminate\Console\Command;

class ImportSocietyExecutivesFromCsvCommand extends Command
{
    protected $signature = 'society-executives:import-csv
        {path? : CSV 경로 (기본: docs/data-migration/홈페이지관리_임원진관리.csv)}
        {--dry-run : DB 반영 없이 건수만 집계}
        {--limit= : 최대 처리 행 수}
        {--on-duplicate=update : skip 또는 update}';

    protected $description = '홈페이지관리_임원진관리.csv(CP949)를 society_executives 테이블로 이관합니다.';

    public function handle(SocietyExecutiveCsvImporter $importer): int
    {
        $path = $this->argument('path')
            ?: base_path('docs/data-migration/홈페이지관리_임원진관리.csv');
        $dryRun = (bool) $this->option('dry-run');
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;
        $onDuplicate = (string) $this->option('on-duplicate');

        if (! in_array($onDuplicate, ['skip', 'update'], true)) {
            $this->error('--on-duplicate 값은 skip 또는 update 만 허용됩니다.');

            return self::FAILURE;
        }

        $this->info('파일: ' . $path);
        $this->info('dry-run: ' . ($dryRun ? 'yes' : 'no'));
        $this->info('on-duplicate: ' . $onDuplicate);

        try {
            $stats = $importer->import($path, $dryRun, $limit, $onDuplicate);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(['항목', '건수'], [
            ['신규', $stats['created']],
            ['갱신', $stats['updated']],
            ['스킵', $stats['skipped']],
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

