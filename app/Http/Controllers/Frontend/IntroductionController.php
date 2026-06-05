<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CommunityCommittee;
use App\Models\SocietyExecutive;
use App\Services\Frontend\PublicBoardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IntroductionController extends Controller
{
    public function __construct(private readonly PublicBoardService $publicBoardService) {}

    public function overview(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '01';
        $gName = '학회소개';
        $sName = '학회개요';
        $geName = 'introduction';
        $gSlug = 'introduction_overview';

        return view('introduction.overview', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }

    public function greeting(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '02';
        $gName = '학회소개';
        $sName = '인사말';
        $geName = 'introduction';
        $gSlug = 'introduction_greeting';

        $post = $this->publicBoardService->findSingle('greetings');

        return view('introduction.greeting', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'post'));
    }

    public function history(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '03';
        $gName = '학회소개';
        $sName = '학회 연혁';
        $geName = 'introduction';
        $gSlug = 'introduction_history';

        $historyGroups = $this->buildHistoryGroups();

        return view('introduction.history', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug', 'historyGroups'));
    }

    /**
     * 학회 연혁 데이터를 4개 그룹(2023~현재 / 2019~2022 / 2015~2018 / 2013~2014)으로 분류한다.
     * 각 그룹 내부는 연도 DESC → 월 DESC 정렬한다.
     * 4개 그룹 범위 밖 데이터는 노출하지 않는다.
     *
     * @return array{
     *     history1: array<int, array{year:int, month:int, contents: array<int, string>}>,
     *     history2: array<int, array{year:int, month:int, contents: array<int, string>}>,
     *     history3: array<int, array{year:int, month:int, contents: array<int, string>}>,
     *     history4: array<int, array{year:int, month:int, contents: array<int, string>}>
     * }
     */
    private function buildHistoryGroups(): array
    {
        $groups = [
            'history1' => ['min' => 2023, 'max' => 9999, 'items' => []],
            'history2' => ['min' => 2019, 'max' => 2022, 'items' => []],
            'history3' => ['min' => 2015, 'max' => 2018, 'items' => []],
            'history4' => ['min' => 2013, 'max' => 2014, 'items' => []],
        ];

        $posts = $this->publicBoardService->listAll('society_history');

        foreach ($posts as $post) {
            $custom = $this->decodeCustomFields($post->custom_fields ?? null);
            $year = (int) ($custom['history_year'] ?? 0);
            $month = (int) ($custom['history_month'] ?? 0);
            if ($year <= 0 || $month <= 0) {
                continue;
            }

            foreach ($groups as $key => &$group) {
                if ($year >= $group['min'] && $year <= $group['max']) {
                    $group['items'][] = [
                        'year' => $year,
                        'month' => $month,
                        'content' => (string) ($post->content ?? ''),
                        'id' => (int) ($post->id ?? 0),
                    ];
                    break;
                }
            }
            unset($group);
        }

        foreach ($groups as &$group) {
            usort($group['items'], static function (array $a, array $b): int {
                return $b['year'] <=> $a['year']
                    ?: $b['month'] <=> $a['month']
                    ?: $b['id'] <=> $a['id'];
            });
            $group['items'] = $this->mergeHistoryItemsByYearMonth($group['items']);
        }
        unset($group);

        return [
            'history1' => $groups['history1']['items'],
            'history2' => $groups['history2']['items'],
            'history3' => $groups['history3']['items'],
            'history4' => $groups['history4']['items'],
        ];
    }

    /**
     * 동일 연·월 행을 하나의 dt 아래 여러 본문으로 묶는다.
     * 정렬은 연도 DESC → 월 DESC → id DESC 이므로 같은 연·월은 인접한다.
     *
     * @param  array<int, array{year:int, month:int, content:string, id:int}>  $items
     * @return array<int, array{year:int, month:int, contents: array<int, string>}>
     */
    private function mergeHistoryItemsByYearMonth(array $items): array
    {
        $merged = [];
        foreach ($items as $item) {
            $lastIdx = count($merged) - 1;
            if ($lastIdx >= 0
                && $merged[$lastIdx]['year'] === $item['year']
                && $merged[$lastIdx]['month'] === $item['month']
            ) {
                $merged[$lastIdx]['contents'][] = $item['content'];
            } else {
                $merged[] = [
                    'year' => $item['year'],
                    'month' => $item['month'],
                    'contents' => [$item['content']],
                ];
            }
        }

        return $merged;
    }

    /**
     * custom_fields(JSON) 디코드.
     */
    private function decodeCustomFields(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (! is_string($raw) || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function bylaws(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '04';
        $dNum = '01';
        $gName = '학회소개';
        $sName = '회칙';
        $dName = '대한기능의학회 회칙';
        $geName = 'introduction';
        $gSlug = 'introduction_bylaws';

        $post = $this->publicBoardService->findSingle('society_rules', 1);

        return view('introduction.bylaws', compact('page_type', 'gNum', 'sNum', 'dNum', 'gName', 'sName', 'dName', 'geName', 'gSlug', 'post'));
    }

    public function bylawsOperation(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '04';
        $dNum = '02';
        $gName = '학회소개';
        $sName = '회칙';
        $dName = '업무 및 운영 내규';
        $geName = 'introduction';
        $gSlug = 'introduction_bylaws_operation';

        $committees = CommunityCommittee::query()
            ->where('visibility_yn', 'Y')
            ->orderBy('name', 'asc')
            ->orderByDesc('id')
            ->get(['id', 'name', 'regulation']);

        $requestedCommitteeId = (int) $request->query('committee_id', 0);
        $selectedCommittee = $requestedCommitteeId > 0
            ? $committees->firstWhere('id', $requestedCommitteeId)
            : $committees->first();
        $selectedCommitteeId = (int) ($selectedCommittee?->id ?? 0);

        return view('introduction.bylaws_operation', compact(
            'page_type',
            'gNum',
            'sNum',
            'dNum',
            'gName',
            'sName',
            'dName',
            'geName',
            'gSlug',
            'committees',
            'selectedCommittee',
            'selectedCommitteeId'
        ));
    }

    public function bylawsProtocol(Request $request): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '04';
        $dNum = '03';
        $gName = '학회소개';
        $sName = '회칙';
        $dName = '업무 프로토콜';
        $geName = 'introduction';
        $gSlug = 'introduction_bylaws_protocol';

        $committees = CommunityCommittee::query()
            ->where('visibility_yn', 'Y')
            ->orderBy('name', 'asc')
            ->orderByDesc('id')
            ->get(['id', 'name', 'protocol']);

        $requestedCommitteeId = (int) $request->query('committee_id', 0);
        $selectedCommittee = $requestedCommitteeId > 0
            ? $committees->firstWhere('id', $requestedCommitteeId)
            : $committees->first();
        $selectedCommitteeId = (int) ($selectedCommittee?->id ?? 0);

        return view('introduction.bylaws_protocol', compact(
            'page_type',
            'gNum',
            'sNum',
            'dNum',
            'gName',
            'sName',
            'dName',
            'geName',
            'gSlug',
            'committees',
            'selectedCommittee',
            'selectedCommitteeId'
        ));
    }

    public function officers(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '05';
        $gName = '학회소개';
        $sName = '임원진';
        $geName = 'introduction';
        $gSlug = 'introduction_officers';

        $executives = SocietyExecutive::query()
            ->where('is_active', true)
            ->orderBy('group_no')
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->get();

        $headExecutiveIds = $executives
            ->where('group_no', 1)
            ->take(2)
            ->pluck('id')
            ->all();

        $headFallbackImages = [
            asset('images/img_officers02.png'),
            asset('images/img_officers01.png'),
        ];

        $headExecutives = $executives
            ->whereIn('id', $headExecutiveIds)
            ->values()
            ->map(fn (SocietyExecutive $executive, int $index): array => $this->buildExecutiveViewData(
                $executive,
                $headFallbackImages[$index] ?? asset('images/img_officers01.png')
            ));

        $bodyExecutives = $executives
            ->reject(fn (SocietyExecutive $executive): bool => in_array($executive->id, $headExecutiveIds, true))
            ->values()
            ->map(fn (SocietyExecutive $executive): array => $this->buildExecutiveViewData($executive));

        return view('introduction.officers', compact(
            'page_type',
            'gNum',
            'sNum',
            'gName',
            'sName',
            'geName',
            'gSlug',
            'headExecutives',
            'bodyExecutives'
        ));
    }

    private function buildExecutiveViewData(SocietyExecutive $executive, ?string $fallbackPhotoUrl = null): array
    {
        return [
            'name' => $executive->name,
            'position' => $executive->position,
            'organization' => $executive->organization,
            'email' => $executive->email,
            'photo_url' => $executive->photo_path
                ? Storage::disk('public')->url($executive->photo_path)
                : $fallbackPhotoUrl,
        ];
    }

    public function location(): View
    {
        $page_type = 'professional';
        $gNum = '01';
        $sNum = '06';
        $gName = '학회소개';
        $sName = '오시는 길';
        $geName = 'introduction';
        $gSlug = 'introduction_location';

        return view('introduction.location', compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'));
    }
}
