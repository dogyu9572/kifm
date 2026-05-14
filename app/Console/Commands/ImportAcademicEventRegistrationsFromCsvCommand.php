<?php

namespace App\Console\Commands;

use App\Services\Backoffice\AcademicEventRegistrationCsvImporter;
use Illuminate\Console\Command;

class ImportAcademicEventRegistrationsFromCsvCommand extends Command
{
    protected $signature = 'academic-events:import-registrations-csv
        {path? : CSV 경로 (기본: docs/data-migration/학술행사_학술행사_참가 및 결제 관리.xls.csv)}
        {--academic-event-id= : 대상 학술행사 ID (선택, 미지정 시 academic_event_id 와 registration_no 는 NULL 로 저장)}
        {--dry-run : DB 반영 없이 건수만 집계}
        {--limit= : 최대 처리 데이터 행 수}
        {--on-duplicate=update : skip 또는 update}';

    protected $description = '학술행사 참가·결제 CSV를 academic_event_registrations 로 이관합니다.';

    public function handle(AcademicEventRegistrationCsvImporter $importer): int
    {
        $eventIdRaw = $this->option('academic-event-id');
        $academicEventId = null;
        if ($eventIdRaw !== null && $eventIdRaw !== '') {
            $academicEventId = (int) $eventIdRaw;
            if ($academicEventId < 1) {
                $this->error('--academic-event-id 값이 올바르지 않습니다.');

                return self::FAILURE;
            }
        }

        $path = $this->argument('path')
            ?: base_path('docs/data-migration/학술행사_학술행사_참가 및 결제 관리.xls.csv');

        $dryRun = (bool) $this->option('dry-run');
        $limit = $this->option('limit') !== null && $this->option('limit') !== '' ? (int) $this->option('limit') : null;
        $onDuplicate = (string) $this->option('on-duplicate');
        if (! in_array($onDuplicate, ['skip', 'update'], true)) {
            $this->error('--on-duplicate 값은 skip 또는 update 만 허용됩니다.');

            return self::FAILURE;
        }

        $this->info('파일: ' . $path);
        $this->info('academic-event-id: ' . ($academicEventId !== null ? (string) $academicEventId : '(미지정, NULL 로 이관)'));
        $this->info('dry-run: ' . ($dryRun ? 'yes' : 'no'));
        $this->info('on-duplicate: ' . $onDuplicate);

        try {
            $stats = $importer->import($path, $academicEventId, $dryRun, $limit, $onDuplicate);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(['항목', '건수'], [
            ['데이터 행(고유번호 숫자)', $stats['data_rows']],
            ['신규', $stats['created']],
            ['갱신', $stats['updated']],
            ['스킵', $stats['skipped']],
        ]);

        $sum = $stats['created'] + $stats['updated'] + $stats['skipped'];
        $match = $sum === $stats['data_rows'] ? '일치' : '불일치(경고 참고)';
        $this->info('건수 대사 (data_rows = created+updated+skipped): ' . $match);

        foreach (array_slice($stats['warnings'] ?? [], 0, 80) as $warn) {
            $this->warn($warn);
        }
        foreach (array_slice($stats['errors'] ?? [], 0, 50) as $err) {
            $this->error($err);
        }

        return self::SUCCESS;
    }
}
