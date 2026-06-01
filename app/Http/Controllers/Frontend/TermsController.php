<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\TermsContentService;
use Illuminate\View\View;

class TermsController extends Controller
{
    public function __construct(
        private readonly TermsContentService $termsContentService,
    ) {}

    public function privacyPolicy(): View
    {
        return $this->renderTerm('privacy', '01', '개인정보처리방침', 'privacy_policy', [
            'privacyPolicyPost' => $this->termsContentService->getSinglePageContent('privacy_policy'),
        ]);
    }

    public function emailCollectionRefusal(): View
    {
        return $this->renderTerm('email_collection_refusal', '02', '이메일무단수집거부', 'email_collection_refusal');
    }

    public function termsOfUse(): View
    {
        return $this->renderTerm('terms_of_use', '03', '이용약관', 'terms_of_use');
    }

    private function renderTerm(string $view, string $sNum, string $sName, string $slug, array $with = []): View
    {
        $page_type = 'professional';
        $gNum = '98';
        $gName = '이용안내';
        $geName = 'Subcommittee';
        $gSlug = $slug;

        return view('terms.' . $view, array_merge(
            compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'geName', 'gSlug'),
            $with
        ));
    }
}
