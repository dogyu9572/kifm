<?php

namespace App\Http\Controllers\Frontend\Mypage\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

trait RendersMypageViews
{
    protected function currentMember(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

  /**
     * @param  array<string, mixed>  $with
     */
    protected function renderMypage(string $view, string $sNum, string $sName, string $slug, array $with = []): View
    {
        $user = $this->currentMember();
        $committeeCodes = is_array($user->committee_codes) ? $user->committee_codes : [];
        $showCommitteeAdminTab = $user->isAdmin();

        return view('mypage.'.$view, array_merge([
            'page_type' => 'professional',
            'gNum' => '99',
            'sNum' => $sNum,
            'gName' => '마이페이지',
            'sName' => $sName,
            'geName' => 'My Page',
            'gSlug' => $slug,
            'showCommitteeAdminTab' => $showCommitteeAdminTab,
            'hasCommitteeAccess' => $committeeCodes !== [],
        ], $with));
    }
}
