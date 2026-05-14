<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OneOnOneInquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'one_on_one_inquiries';

    protected $fillable = [
        'user_id',
        'member_name',
        'member_email',
        'title',
        'content',
        'content_format',
        'answer_status',
        'answer_content',
        'answered_at',
        'answered_by',
        'client_ip',
        'is_locked',
        'attachments',
        'answer_attachments',
        'legacy_post_id',
        'legacy_thread_id',
    ];

    protected $casts = [
        'attachments' => 'array',
        'answer_attachments' => 'array',
        'answered_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if ($status === null || $status === '' || $status === 'all') {
            return $query;
        }

        return $query->where('answer_status', $status);
    }

    public function scopeDateBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from !== null && $from !== '') {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to !== null && $to !== '') {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    public function scopeKeyword(Builder $query, ?string $field, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return $query;
        }

        $like = '%'.$keyword.'%';
        $field = $field ?: 'all';

        return match ($field) {
            'name' => $query->where(function (Builder $q) use ($like) {
                $q->where('member_name', 'like', $like)
                    ->orWhereHas('user', function (Builder $u) use ($like) {
                        $u->where('name', 'like', $like);
                    });
            }),
            'title' => $query->where('title', 'like', $like),
            'content' => $query->where(function (Builder $q) use ($like) {
                $q->where('content', 'like', $like)
                    ->orWhere('answer_content', 'like', $like);
            }),
            default => $query->where(function (Builder $q) use ($like) {
                $q->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like)
                    ->orWhere('answer_content', 'like', $like)
                    ->orWhere('member_name', 'like', $like)
                    ->orWhereHas('user', function (Builder $u) use ($like) {
                        $u->where('name', 'like', $like);
                    });
            }),
        };
    }

    public function displayMemberName(): string
    {
        $userName = $this->user?->name;
        if (is_string($userName) && $userName !== '') {
            return $userName;
        }

        return (string) ($this->member_name ?? '');
    }

    public function displayMemberEmail(): string
    {
        $userEmail = $this->user?->email;
        if (is_string($userEmail) && $userEmail !== '') {
            return $userEmail;
        }

        return (string) ($this->member_email ?? '');
    }
}
