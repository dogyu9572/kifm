<?php

namespace App\Http\Requests;

use App\Models\BoardComment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BoardPostCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:65535',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('board_comments', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'content' => '댓글 내용',
            'parent_id' => '상위 댓글',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slug = (string) $this->route('slug');
            $postId = (int) $this->route('post');
            $parentId = $this->input('parent_id');

            if ($parentId === null) {
                return;
            }

            $parent = BoardComment::query()->whereKey($parentId)->first();
            if (! $parent) {
                return;
            }
            if ($parent->board_slug !== $slug || (int) $parent->post_id !== $postId) {
                $validator->errors()->add('parent_id', '상위 댓글이 이 게시글에 속하지 않습니다.');
            }
        });
    }
}
