<?php

namespace App\Console\Commands;

use App\Models\OneOnOneInquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportOneOnOneInquiriesFromCsv extends Command
{
    /**
     * @var string
     */
    protected $signature = 'inquiries:import-one-on-one
        {path : 1대1문의 CSV 경로 (절대 경로 또는 프로젝트 기준 상대 경로)}
        {--encoding=CP949 : 원본 CSV 인코딩}
        {--dry-run : 실제 저장하지 않고 결과만 확인}
        {--admin-login=rp415 : 관리자 답변자 회원ID(로그인 ID)}';

    /**
     * @var string
     */
    protected $description = '레거시 1:1 문의 게시판 CSV를 one_on_one_inquiries 테이블로 이관합니다.';

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        if (! is_file($path)) {
            $absolutePath = base_path($path);
            if (! is_file($absolutePath)) {
                $this->error('CSV 파일을 찾을 수 없습니다: '.$path);

                return self::FAILURE;
            }
            $path = $absolutePath;
        }

        $encoding = strtoupper((string) $this->option('encoding'));
        $dryRun = (bool) $this->option('dry-run');
        $adminLogin = (string) $this->option('admin-login');

        $handle = $this->openCsvHandle($path, $encoding);
        if (! $handle) {
            $this->error('CSV 파일을 열 수 없습니다: '.$path);

            return self::FAILURE;
        }

        $rows = [];
        try {
            $line = 0;
            $headerMap = null;
            while (($cols = fgetcsv($handle)) !== false) {
                $line++;
                if ($line === 1) {
                    continue;
                }
                if ($line === 2) {
                    $headerMap = $this->buildHeaderMap($cols);
                    continue;
                }
                if ($line === 3) {
                    continue;
                }
                if ($cols === [null] || count(array_filter($cols, fn ($v) => $v !== null && $v !== '')) === 0) {
                    continue;
                }
                $row = $this->normalizeRow($cols, $headerMap);
                if (! $row) {
                    continue;
                }
                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        if ($headerMap === null) {
            $this->error('CSV 헤더(2행)를 찾지 못했습니다.');

            return self::FAILURE;
        }

        $threads = $this->groupByThread($rows, $adminLogin);
        $this->info('이관 대상 스레드 수: '.count($threads));

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        $adminUserId = $this->resolveUserIdByLogin($adminLogin);
        $bar = $this->output->createProgressBar(count($threads));
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($threads as $threadId => $group) {
                $questions = $group['questions'];
                $answers = $group['answers'];

                if (empty($questions)) {
                    $stats['skipped']++;
                    $bar->advance();

                    continue;
                }

                $firstQuestion = $questions[0];
                $rest = array_slice($questions, 1);

                try {
                    $this->upsertInquiry($firstQuestion, $answers, (int) $threadId, $adminUserId, $stats);
                    foreach ($rest as $extra) {
                        $this->upsertInquiry($extra, [], (int) $threadId, null, $stats);
                    }
                } catch (Throwable $e) {
                    $stats['failed']++;
                    $this->newLine();
                    $this->warn('실패 (legacy_post_id='.($firstQuestion['legacy_post_id'] ?? '?').'): '.$e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            if ($dryRun) {
                DB::rollBack();
                $this->info('dry-run: 트랜잭션을 롤백했습니다.');
            } else {
                DB::commit();
            }
        } catch (Throwable $e) {
            DB::rollBack();
            $this->newLine();
            $this->error('이관 중 오류로 전체 롤백되었습니다: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info(sprintf(
            '완료 - 신규: %d / 업데이트: %d / 스킵: %d / 실패: %d',
            $stats['created'],
            $stats['updated'],
            $stats['skipped'],
            $stats['failed'],
        ));

        return self::SUCCESS;
    }

    /**
     * @return resource|false
     */
    protected function openCsvHandle(string $path, string $encoding)
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return false;
        }
        if ($encoding === '' || strtoupper($encoding) === 'UTF-8') {
            return $handle;
        }

        $filter = stream_filter_append($handle, 'convert.iconv.'.$encoding.'/UTF-8//TRANSLIT//IGNORE', STREAM_FILTER_READ);
        if ($filter === false) {
            fclose($handle);

            return false;
        }

        return $handle;
    }

    /**
     * @param  list<string>  $cols
     * @return array<string, int>
     */
    protected function buildHeaderMap(array $cols): array
    {
        $map = [];
        foreach ($cols as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $map[$name] = $index;
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $cols
     * @param  array<string, int>  $headerMap
     * @return array<string, mixed>|null
     */
    protected function normalizeRow(array $cols, array $headerMap): ?array
    {
        $get = function (string $name) use ($cols, $headerMap): ?string {
            if (! isset($headerMap[$name])) {
                return null;
            }
            $value = $cols[$headerMap[$name]] ?? null;
            if ($value === null) {
                return null;
            }
            $value = trim((string) $value);

            return $value === '' ? null : $value;
        };

        $legacyPostId = $get('게시글고유번호');
        if ($legacyPostId === null) {
            return null;
        }

        $attachments = [];
        for ($i = 1; $i <= 5; $i++) {
            $name = $get('첨부파일명'.$i);
            if ($name !== null) {
                $attachments[] = ['original_name' => $name];
            }
        }

        $formatCode = $get('text=0 html=2');
        $contentFormat = ($formatCode === '0') ? 'text' : 'html';

        return [
            'legacy_post_id' => (int) $legacyPostId,
            'legacy_thread_id' => $get('질문답글통합번호') !== null ? (int) $get('질문답글통합번호') : (int) $legacyPostId,
            'legacy_comment_id' => $get('댓글고유번호') !== null ? (int) $get('댓글고유번호') : null,
            'member_login' => $get('회원ID'),
            'member_name' => $get('이름'),
            'member_email' => $get('이-메일'),
            'title' => $get('제목'),
            'content' => $get('내용'),
            'content_format' => $contentFormat,
            'created_at_raw' => $get('작성일'),
            'client_ip' => $get('IP'),
            'is_locked' => $get('잠금') === '1',
            'attachments' => $attachments,
            'contact' => $get('작성자 연락처'),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<int, array{questions: list<array<string, mixed>>, answers: list<array<string, mixed>>}>
     */
    protected function groupByThread(array $rows, string $adminLogin): array
    {
        $threads = [];
        foreach ($rows as $row) {
            $threadId = (int) ($row['legacy_thread_id'] ?? $row['legacy_post_id']);
            if (! isset($threads[$threadId])) {
                $threads[$threadId] = ['questions' => [], 'answers' => []];
            }
            $isAdmin = $adminLogin !== '' && ($row['member_login'] ?? '') === $adminLogin;
            $bucket = $isAdmin ? 'answers' : 'questions';
            $threads[$threadId][$bucket][] = $row;
        }

        foreach ($threads as $threadId => $group) {
            $threads[$threadId]['questions'] = $this->sortByCreatedAt($group['questions']);
            $threads[$threadId]['answers'] = $this->sortByCreatedAt($group['answers']);
        }

        return $threads;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    protected function sortByCreatedAt(array $rows): array
    {
        usort($rows, function (array $a, array $b): int {
            $ta = $this->toTimestamp($a['created_at_raw'] ?? null);
            $tb = $this->toTimestamp($b['created_at_raw'] ?? null);
            if ($ta === $tb) {
                return ((int) $a['legacy_post_id']) <=> ((int) $b['legacy_post_id']);
            }

            return $ta <=> $tb;
        });

        return $rows;
    }

    protected function toTimestamp(?string $raw): int
    {
        if ($raw === null || $raw === '') {
            return 0;
        }
        $parsed = $this->parseDatetime($raw);

        return $parsed ? $parsed->getTimestamp() : 0;
    }

    protected function parseDatetime(?string $raw): ?Carbon
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $patterns = ['Y-m-d H:i:s', 'Y-m-d H:i', 'YmdHis', 'YmdHi', 'Y/m/d H:i:s', 'Y/m/d H:i'];
        foreach ($patterns as $pattern) {
            try {
                $dt = Carbon::createFromFormat($pattern, $raw);
                if ($dt) {
                    return $dt;
                }
            } catch (Throwable) {
                continue;
            }
        }
        try {
            return Carbon::parse($raw);
        } catch (Throwable) {
            return null;
        }
    }

    protected function resolveUserIdByLogin(?string $loginId): ?int
    {
        if ($loginId === null || $loginId === '') {
            return null;
        }
        $user = User::query()->where('login_id', $loginId)->first();

        return $user?->id;
    }

    /**
     * @param  array<string, mixed>  $question
     * @param  list<array<string, mixed>>  $answers
     * @param  array<string, int>  $stats
     */
    protected function upsertInquiry(array $question, array $answers, int $threadId, ?int $adminUserId, array &$stats): void
    {
        $createdAt = $this->parseDatetime($question['created_at_raw'] ?? null);
        $userId = $this->resolveUserIdByLogin($question['member_login'] ?? null);

        $answerContent = null;
        $answeredAt = null;
        $status = 'PENDING';
        if (! empty($answers)) {
            $status = 'DONE';
            $combined = [];
            foreach ($answers as $i => $answer) {
                $body = (string) ($answer['content'] ?? '');
                $combined[] = $body;
                if ($i === 0) {
                    $answeredAt = $this->parseDatetime($answer['created_at_raw'] ?? null);
                }
            }
            $answerContent = implode("\n<hr>\n", array_filter($combined, fn ($s) => $s !== ''));
            if ($answerContent === '') {
                $answerContent = null;
            }
        }

        $attributes = [
            'user_id' => $userId,
            'member_name' => $question['member_name'] ?? null,
            'member_email' => $question['member_email'] ?? null,
            'title' => (string) ($question['title'] ?? '(제목 없음)'),
            'content' => $question['content'] ?? null,
            'content_format' => (string) ($question['content_format'] ?? 'html'),
            'answer_status' => $status,
            'answer_content' => $answerContent,
            'answered_at' => $answeredAt,
            'answered_by' => $status === 'DONE' ? $adminUserId : null,
            'client_ip' => $question['client_ip'] ?? null,
            'is_locked' => (bool) ($question['is_locked'] ?? false),
            'attachments' => empty($question['attachments']) ? null : $question['attachments'],
            'answer_attachments' => null,
            'legacy_thread_id' => $threadId,
        ];

        $legacyPostId = (int) $question['legacy_post_id'];
        $existing = OneOnOneInquiry::query()
            ->withTrashed()
            ->where('legacy_post_id', $legacyPostId)
            ->first();

        if ($existing) {
            $existing->fill($attributes);
            if ($createdAt) {
                $existing->created_at = $createdAt;
            }
            $existing->save();
            $stats['updated']++;

            return;
        }

        $inquiry = new OneOnOneInquiry($attributes);
        $inquiry->legacy_post_id = $legacyPostId;
        if ($createdAt) {
            $inquiry->created_at = $createdAt;
            $inquiry->updated_at = $createdAt;
        }
        $inquiry->save();
        $stats['created']++;
    }
}
