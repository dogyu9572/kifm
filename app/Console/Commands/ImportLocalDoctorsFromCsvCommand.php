<?php

namespace App\Console\Commands;

use App\Services\Backoffice\LocalDoctorCsvImporter;
use Illuminate\Console\Command;

class ImportLocalDoctorsFromCsvCommand extends Command
{
    protected $signature = 'local-doctors:import-csv
        {path? : CSV 경로 (기본: docs/data-migration/우리동네주치의_주치의 목록.csv)}
        {--dry-run : DB 반영 없이 건수만 집계}
        {--limit= : 최대 처리 행 수}
        {--on-duplicate=update : skip 또는 update}';

    protected $description = '우리동네주치의_주치의 목록.csv(CP949)를 local_doctors 테이블로 이관합니다.';

    public function handle(LocalDoctorCsvImporter $importer): int
    {
        $path = $this->argument('path')
            ?: base_path('docs/data-migration/우리동네주치의_주치의 목록.csv');

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

        foreach (array_slice($stats['warnings'] ?? [], 0, 50) as $warn) {
            $this->warn($warn);
        }
        if (count($stats['warnings'] ?? []) > 50) {
            $this->warn('… 외 ' . (count($stats['warnings']) - 50) . '건 경고 생략');
        }

        foreach (array_slice($stats['errors'], 0, 50) as $err) {
            $this->warn($err);
        }
        if (count($stats['errors']) > 50) {
            $this->warn('… 외 ' . (count($stats['errors']) - 50) . '건 오류 생략');
        }

        return self::SUCCESS;
    }
}
