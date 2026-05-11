<?php

namespace App\Services\Backoffice;

use App\Models\PaymentPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentPlanService
{
    /** @return array<string, string> */
    public static function categoryLabels(): array
    {
        return [
            'conference' => '학술대회 등록비',
            'membership' => '연회비',
            'education' => '연수교육 수강비',
        ];
    }

    /** @return array<string, string> */
    public static function gradeLabels(): array
    {
        return [
            'associate' => '준회원',
            'regular' => '정회원',
            'lifetime' => '평생회원',
            'senior' => '시니어',
        ];
    }

    /** @return array<string, string> */
    public static function memberTypeLabels(): array
    {
        return [
            'none' => '해당 없음',
            'specialist' => '전문의',
            'resident' => '전공의',
            'public' => '공보의',
            'military' => '군의관',
            'nurse' => '간호사',
            'other' => '기타',
        ];
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $query = PaymentPlan::query()->with(['grades', 'types']);

        $this->applyFilters($query, $request);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('member_status')) {
            $query->where('member_status', $request->input('member_status'));
        }

        if ($request->filled('executive')) {
            $query->where('executive', $request->input('executive'));
        }

        if ($request->filled('member_type')) {
            $type = $request->input('member_type');
            $query->whereHas('types', function (Builder $q) use ($type) {
                $q->where('member_type', $type);
            });
        }

        if ($request->filled('use_status')) {
            $query->where('use_status', $request->input('use_status'));
        }

        if ($request->filled('keyword')) {
            $kw = trim((string) $request->input('keyword'));
            if ($kw !== '') {
                $query->where('plan_name', 'like', '%'.$kw.'%');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data  검증 완료 입력
     */
    public function createFromValidated(array $data): PaymentPlan
    {
        return DB::transaction(function () use ($data) {
            $plan = $this->fillPlan(new PaymentPlan, $data);
            $plan->save();
            $this->syncChildRows($plan, $data);

            return $plan->fresh(['grades', 'types']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateFromValidated(PaymentPlan $plan, array $data): PaymentPlan
    {
        return DB::transaction(function () use ($plan, $data) {
            $this->fillPlan($plan, $data);
            $plan->save();
            $this->syncChildRows($plan, $data);

            return $plan->fresh(['grades', 'types']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function fillPlan(PaymentPlan $plan, array $data): PaymentPlan
    {
        $plan->plan_name = $data['plan_name'];
        $plan->category = $data['category'];
        $plan->member_status = $data['member_status'];

        if ($data['category'] === 'conference') {
            $plan->price_early = (int) $data['price_early'];
            $plan->price_site = (int) $data['price_site'];
            $plan->price = null;
        } else {
            $plan->price_early = null;
            $plan->price_site = null;
            $plan->price = isset($data['price']) ? (int) $data['price'] : null;
        }

        if ($data['member_status'] === 'member') {
            $plan->executive = $data['executive'];
        } else {
            $plan->executive = null;
        }

        $plan->use_status = $data['use_status'];

        return $plan;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncChildRows(PaymentPlan $plan, array $data): void
    {
        $plan->grades()->delete();
        $plan->types()->delete();

        if ($data['member_status'] !== 'member') {
            return;
        }

        $grades = $data['grades'] ?? [];
        foreach ($grades as $grade) {
            $plan->grades()->create(['grade' => $grade]);
        }

        $types = $data['member_types'] ?? [];
        foreach ($types as $memberType) {
            $plan->types()->create(['member_type' => $memberType]);
        }
    }

    public function deletePlans(array $ids): int
    {
        return PaymentPlan::whereIn('id', $ids)->delete();
    }
}
