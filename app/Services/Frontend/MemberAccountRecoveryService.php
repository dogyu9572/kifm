<?php

namespace App\Services\Frontend;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberAccountRecoveryService
{
    public function findMemberForId(string $name, string $phoneNumber, string $email): ?User
    {
        return User::query()
            ->where('role', 'user')
            ->whereNull('withdrawn_at')
            ->where('name', $name)
            ->where('phone_number', $phoneNumber)
            ->where('email', $email)
            ->first();
    }

    public function findMemberForPasswordReset(string $loginId, string $email, string $phoneNumber): ?User
    {
        return User::query()
            ->where('role', 'user')
            ->whereNull('withdrawn_at')
            ->where('login_id', $loginId)
            ->where('email', $email)
            ->where('phone_number', $phoneNumber)
            ->first();
    }

    /**
     * @return array{masked_login_id: string, joined_at: string}
     */
    public function buildFindIdResult(User $user): array
    {
        return [
            'masked_login_id' => $this->maskLoginId((string) $user->login_id),
            'joined_at' => optional($user->created_at)->format('Y.m.d') ?? '-',
        ];
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();
    }

    private function maskLoginId(string $loginId): string
    {
        $length = mb_strlen($loginId);
        if ($length <= 2) {
            return mb_substr($loginId, 0, 1).'*';
        }

        $visibleLength = min(4, max(2, (int) floor($length / 2)));

        return mb_substr($loginId, 0, $visibleLength).str_repeat('*', max(3, $length - $visibleLength));
    }
}
