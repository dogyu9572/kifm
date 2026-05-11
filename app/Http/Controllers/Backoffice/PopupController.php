<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\CommunityCommittee;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PopupController extends Controller
{
    private function resolveMenuScope(Request $request): string
    {
        $name = $request->route()?->getName() ?? '';

        return str_starts_with((string) $name, 'backoffice.committee-popups')
            ? Popup::MENU_SCOPE_COMMITTEE
            : Popup::MENU_SCOPE_SITE;
    }

    /**
     * @return array<string, string>
     */
    private function popupRoutes(string $menuScope): array
    {
        if ($menuScope === Popup::MENU_SCOPE_COMMITTEE) {
            return [
                'index' => route('backoffice.committee-popups.index'),
                'create' => route('backoffice.committee-popups.create'),
                'store' => route('backoffice.committee-popups.store'),
                'update_order' => route('backoffice.committee-popups.update-order'),
            ];
        }

        return [
            'index' => route('backoffice.popups.index'),
            'create' => route('backoffice.popups.create'),
            'store' => route('backoffice.popups.store'),
            'update_order' => route('backoffice.popups.update-order'),
        ];
    }

    private function assertPopupScope(Popup $popup, string $expectedScope): void
    {
        if ($popup->menu_scope !== $expectedScope) {
            abort(404);
        }
    }

    /**
     * 팝업 목록
     */
    public function index(Request $request)
    {
        $menuScope = $this->resolveMenuScope($request);
        $routes = $this->popupRoutes($menuScope);

        $query = Popup::query()->forMenuScope($menuScope);

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->where('use_period', true)
                ->where('start_date', '>=', $request->start_date)
                ->where('end_date', '<=', $request->end_date);
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%'.$request->title.'%');
        }

        if ($request->filled('popup_type')) {
            $query->where('popup_type', $request->popup_type);
        }

        if ($request->filled('popup_display_type')) {
            $query->where('popup_display_type', $request->popup_display_type);
        }

        if ($menuScope === Popup::MENU_SCOPE_COMMITTEE && $request->filled('community_committee_id')) {
            $query->where('community_committee_id', (int) $request->community_committee_id);
        }

        if ($menuScope === Popup::MENU_SCOPE_COMMITTEE && $request->filled('target_board_slug')) {
            $query->where('target_board_slug', $request->target_board_slug);
        }

        $perPage = $request->get('per_page', 10);
        $perPage = in_array((int) $perPage, [10, 20, 50, 100], true) ? (int) $perPage : 10;

        $popups = $query->with('communityCommittee')->ordered()->paginate($perPage);

        $committeeFilterOptions = $menuScope === Popup::MENU_SCOPE_COMMITTEE
            ? CommunityCommittee::query()->orderBy('sort_order')->orderBy('name')->get()
            : collect();

        $pageTitle = $menuScope === Popup::MENU_SCOPE_COMMITTEE ? '위원회 팝업 관리' : '팝업 관리';

        return view('backoffice.popups.index', compact(
            'popups',
            'menuScope',
            'routes',
            'committeeFilterOptions',
            'pageTitle'
        ));
    }

    /**
     * 팝업 생성 폼
     */
    public function create(Request $request)
    {
        $menuScope = $this->resolveMenuScope($request);
        $routes = $this->popupRoutes($menuScope);
        $committeeOptions = $menuScope === Popup::MENU_SCOPE_COMMITTEE
            ? CommunityCommittee::query()->orderBy('sort_order')->orderBy('name')->get()
            : collect();
        $committeeTargetBoards = Popup::COMMITTEE_TARGET_BOARDS;
        $pageTitle = $menuScope === Popup::MENU_SCOPE_COMMITTEE ? '위원회 팝업 등록' : '팝업 추가';

        return view('backoffice.popups.create', compact(
            'menuScope',
            'routes',
            'committeeOptions',
            'committeeTargetBoards',
            'pageTitle'
        ));
    }

    /**
     * 팝업 저장
     */
    public function store(Request $request)
    {
        $menuScope = $this->resolveMenuScope($request);
        $routes = $this->popupRoutes($menuScope);

        $validated = $this->validatePopupPayload($request, $menuScope);

        $data = $this->mergePopupDefaults($request, $validated);
        $data['menu_scope'] = $menuScope;
        $data['use_period'] = $request->boolean('use_period');

        if ($menuScope === Popup::MENU_SCOPE_SITE) {
            $data['community_committee_id'] = null;
            $data['target_board_slug'] = null;
        }

        if ($request->hasFile('popup_image')) {
            $data['popup_image'] = $request->file('popup_image')->store('popups', 'public');
        }

        $data['width'] = $data['width'] ?: 400;
        $data['height'] = $data['height'] ?: 300;
        $data['position_top'] = $data['position_top'] ?: 100;
        $data['position_left'] = $data['position_left'] ?: 100;

        if (! $data['use_period']) {
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        Popup::create($data);

        return redirect()->to($routes['index'])
            ->with('success', '팝업이 성공적으로 생성되었습니다.');
    }

    /**
     * 팝업 수정 폼
     */
    public function edit(Request $request, Popup $popup)
    {
        $menuScope = $this->resolveMenuScope($request);
        $this->assertPopupScope($popup, $menuScope);
        $routes = $this->popupRoutes($menuScope);
        $routes['update'] = $menuScope === Popup::MENU_SCOPE_COMMITTEE
            ? route('backoffice.committee-popups.update', $popup)
            : route('backoffice.popups.update', $popup);
        $popup->load('communityCommittee');
        $committeeOptions = $menuScope === Popup::MENU_SCOPE_COMMITTEE
            ? CommunityCommittee::query()->orderBy('sort_order')->orderBy('name')->get()
            : collect();
        $committeeTargetBoards = Popup::COMMITTEE_TARGET_BOARDS;
        $pageTitle = $menuScope === Popup::MENU_SCOPE_COMMITTEE ? '위원회 팝업 수정' : '팝업 수정';

        return view('backoffice.popups.edit', compact(
            'popup',
            'menuScope',
            'routes',
            'committeeOptions',
            'committeeTargetBoards',
            'pageTitle'
        ));
    }

    /**
     * 팝업 업데이트
     */
    public function update(Request $request, Popup $popup)
    {
        $menuScope = $this->resolveMenuScope($request);
        $this->assertPopupScope($popup, $menuScope);
        $routes = $this->popupRoutes($menuScope);

        $validated = $this->validatePopupPayload($request, $menuScope, $popup);

        $data = $this->mergePopupDefaults($request, $validated);
        $data['menu_scope'] = $popup->menu_scope;
        $data['use_period'] = $request->boolean('use_period');

        if ($menuScope === Popup::MENU_SCOPE_SITE) {
            $data['community_committee_id'] = null;
            $data['target_board_slug'] = null;
        }

        if ($request->hasFile('popup_image')) {
            if ($popup->popup_image) {
                Storage::disk('public')->delete($popup->popup_image);
            }
            $data['popup_image'] = $request->file('popup_image')->store('popups', 'public');
        }

        if ($request->has('remove_popup_image') && $request->remove_popup_image) {
            if ($popup->popup_image) {
                Storage::disk('public')->delete($popup->popup_image);
            }
            $data['popup_image'] = null;
        }

        $data['width'] = $data['width'] ?: 400;
        $data['height'] = $data['height'] ?: 300;
        $data['position_top'] = $data['position_top'] ?: 100;
        $data['position_left'] = $data['position_left'] ?: 100;

        if (! $data['use_period']) {
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        $popup->update($data);

        return redirect()->to($routes['index'])
            ->with('success', '팝업이 성공적으로 수정되었습니다.');
    }

    /**
     * 팝업 삭제
     */
    public function destroy(Request $request, Popup $popup)
    {
        $menuScope = $this->resolveMenuScope($request);
        $this->assertPopupScope($popup, $menuScope);
        $routes = $this->popupRoutes($menuScope);

        if ($popup->popup_image) {
            Storage::disk('public')->delete($popup->popup_image);
        }

        $popup->delete();

        return redirect()->to($routes['index'])
            ->with('success', '팝업이 성공적으로 삭제되었습니다.');
    }

    /**
     * 팝업 순서 업데이트
     */
    public function updateOrder(Request $request)
    {
        $menuScope = $this->resolveMenuScope($request);
        $popupOrder = $request->input('popupOrder', []);

        foreach ($popupOrder as $item) {
            if (! isset($item['id'], $item['order'])) {
                continue;
            }
            $popup = Popup::query()
                ->where('menu_scope', $menuScope)
                ->whereKey((int) $item['id'])
                ->first();
            if ($popup) {
                $popup->sort_order = (int) $item['order'];
                $popup->save();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * 팝업 표시 (일반 팝업용)
     */
    public function showPopup(Popup $popup)
    {
        return view('popup.show', compact('popup'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePopupPayload(Request $request, string $menuScope, ?Popup $forUpdate = null): array
    {
        $boardSlugs = array_keys(Popup::COMMITTEE_TARGET_BOARDS);

        $rules = [
            'title' => 'required|string|max:255',
            'use_period' => 'nullable|boolean',
            'start_date' => 'nullable|required_if:use_period,1|date',
            'end_date' => 'nullable|required_if:use_period,1|date|after_or_equal:start_date',
            'width' => 'nullable|numeric|min:100|max:2000',
            'height' => 'nullable|numeric|min:100|max:2000',
            'position_top' => 'nullable|numeric|min:0',
            'position_left' => 'nullable|numeric|min:0',
            'url' => 'nullable|url',
            'url_target' => 'nullable|in:_self,_blank',
            'popup_type' => 'nullable|in:image,html',
            'popup_display_type' => 'nullable|in:normal,layer',
            'popup_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'popup_content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];

        if ($menuScope === Popup::MENU_SCOPE_COMMITTEE) {
            $rules['community_committee_id'] = ['required', 'integer', 'exists:community_committees,id'];
            $rules['target_board_slug'] = ['required', 'string', 'max:80', Rule::in($boardSlugs)];
        } else {
            $rules['community_committee_id'] = 'prohibited';
            $rules['target_board_slug'] = 'prohibited';
        }

        if ($menuScope === Popup::MENU_SCOPE_COMMITTEE && $request->input('popup_type', $forUpdate?->popup_type) === 'image') {
            if ($forUpdate === null || ! $forUpdate->popup_image) {
                $rules['popup_image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:5120';
            }
        }

        return $request->validate($rules);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function mergePopupDefaults(Request $request, array $validated): array
    {
        $data = $validated;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['popup_type'] = $data['popup_type'] ?? 'image';
        $data['popup_display_type'] = $data['popup_display_type'] ?? 'normal';
        $data['url_target'] = $data['url_target'] ?? '_blank';

        return $data;
    }
}
