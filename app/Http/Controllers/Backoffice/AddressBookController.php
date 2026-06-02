<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressBookRequest;
use App\Models\AddressBook;
use App\Models\User;
use Illuminate\Http\Request;

class AddressBookController extends Controller
{
    public function index(Request $request)
    {
        $query = AddressBook::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%'.trim((string) $request->input('keyword')).'%');
        }

        $addressBooks = $query->latest()->paginate(20)->withQueryString();

        return view('backoffice.address_books.index', compact('addressBooks'));
    }

    public function create()
    {
        $membersSource = User::query()
            ->whereNull('withdrawn_at')
            ->orderBy('name')
            ->limit(200)
            ->get(['id', 'name', 'login_id', 'email', 'phone_number']);

        return view('backoffice.address_books.create', compact('membersSource'));
    }

    public function store(AddressBookRequest $request)
    {
        $payload = $request->validated();
        $members = $this->decodeMembers((string) ($payload['members'] ?? ''));
        $addressBook = AddressBook::create([
            'name' => $payload['name'],
            'member_count' => count($members),
        ]);

        $addressBook->members()->createMany($members);

        return redirect()
            ->route('backoffice.address-books.index')
            ->with('success', '주소록이 등록되었습니다.');
    }

    public function edit(AddressBook $addressBook)
    {
        $addressBook->load('members');
        $membersSource = User::query()
            ->whereNull('withdrawn_at')
            ->orderBy('name')
            ->limit(200)
            ->get(['id', 'name', 'login_id', 'email', 'phone_number']);

        return view('backoffice.address_books.edit', compact('addressBook', 'membersSource'));
    }

    public function update(AddressBookRequest $request, AddressBook $addressBook)
    {
        $payload = $request->validated();
        $members = $this->decodeMembers((string) ($payload['members'] ?? ''));
        $addressBook->update([
            'name' => $payload['name'],
            'member_count' => count($members),
        ]);

        $addressBook->members()->delete();
        $addressBook->members()->createMany($members);

        return redirect()
            ->route('backoffice.address-books.index')
            ->with('success', '주소록이 수정되었습니다.');
    }

    public function destroy(AddressBook $addressBook)
    {
        $addressBook->delete();

        return redirect()
            ->route('backoffice.address-books.index')
            ->with('success', '주소록이 삭제되었습니다.');
    }

    public function searchMembers(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', ''));
        $field = (string) $request->input('search_field', 'all');
        $query = User::query()->whereNull('withdrawn_at');

        if ($keyword !== '') {
            $query->where(function ($innerQuery) use ($keyword, $field) {
                $like = '%'.$keyword.'%';
                if ($field === 'name') {
                    $innerQuery->where('name', 'like', $like);
                    return;
                }
                if ($field === 'id') {
                    $innerQuery->where('login_id', 'like', $like);
                    return;
                }
                if ($field === 'email') {
                    $innerQuery->where('email', 'like', $like);
                    return;
                }
                if ($field === 'phone') {
                    $innerQuery->where('phone_number', 'like', $like);
                    return;
                }

                $innerQuery->where('name', 'like', $like)
                    ->orWhere('login_id', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like);
            });
        }

        $members = $query
            ->orderBy('name')
            ->paginate(10, ['id', 'name', 'login_id', 'email', 'phone_number'])
            ->withQueryString();

        $data = $members->getCollection()->map(fn (User $user) => [
                'member_id' => $user->id,
                'id' => $user->id,
                'name' => $user->name,
                'login_id' => $user->login_id,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'phone_number' => $user->phone_number,
                'source_type' => 'SEARCH',
            ]);

        return response()->json([
            'data' => $data,
            'members' => $data,
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'total' => $members->total(),
                'per_page' => $members->perPage(),
            ],
        ]);
    }

    private function decodeMembers(string $membersJson): array
    {
        $decoded = json_decode($membersJson, true);
        if (! is_array($decoded)) {
            return [];
        }

        $members = [];
        foreach ($decoded as $member) {
            if (! is_array($member) || empty($member['name'])) {
                continue;
            }

            $members[] = [
                'member_id' => isset($member['member_id']) ? (int) $member['member_id'] : null,
                'name' => (string) $member['name'],
                'login_id' => $member['login_id'] ?? null,
                'email' => $member['email'] ?? null,
                'phone' => $member['phone'] ?? null,
                'source_type' => $member['source_type'] ?? 'SEARCH',
            ];
        }

        return $members;
    }
}
