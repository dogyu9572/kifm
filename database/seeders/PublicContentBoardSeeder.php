<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use App\Models\Board;
use App\Models\BoardSkin;
use App\Models\BoardTemplate;
use Illuminate\Database\Seeder;

class PublicContentBoardSeeder extends Seeder
{
    private const PARENT_MENU_NAME = '일반인';

    private const BOARDS = [
        [
            'name' => '영상 콘텐츠',
            'slug' => 'public_video_contents',
            'description' => '일반인 영상 콘텐츠 게시판',
            'type' => 'thumbnail',
            'menu_order' => 1,
            'custom_fields' => [
                [
                    'name' => 'youtube_url',
                    'type' => 'text',
                    'label' => '유튜브 링크',
                    'options' => null,
                    'required' => false,
                    'max_length' => 500,
                    'placeholder' => 'https://www.youtube.com/watch?v=...',
                ],
            ],
        ],
        [
            'name' => '환자 이야기',
            'slug' => 'public_patient_stories',
            'description' => '일반인 환자 이야기 게시판',
            'type' => 'general',
            'menu_order' => 2,
            'custom_fields' => null,
        ],
        [
            'name' => '보도자료, 컬럼',
            'slug' => 'public_press_columns',
            'description' => '일반인 보도자료 및 컬럼 게시판',
            'type' => 'thumbnail',
            'menu_order' => 3,
            'custom_fields' => null,
        ],
        [
            'name' => '미디어, 강의 행사',
            'slug' => 'public_media_lecture_events',
            'description' => '일반인 미디어 및 강의 행사 게시판',
            'type' => 'thumbnail',
            'menu_order' => 4,
            'custom_fields' => null,
        ],
    ];

    public function run(): void
    {
        $defaultSkin = BoardSkin::where('name', '기본 스킨')->first() ?? BoardSkin::first();

        $parentMenu = $this->upsertParentMenu();

        foreach (self::BOARDS as $boardConfig) {
            $template = BoardTemplate::updateOrCreate(
                ['name' => $boardConfig['name']],
                [
                    'description' => $boardConfig['description'],
                    'skin_id' => $defaultSkin?->id,
                    'field_config' => $this->fieldConfig($boardConfig['type']),
                    'custom_fields_config' => $boardConfig['custom_fields'],
                    'enable_notice' => $boardConfig['type'] === 'general',
                    'enable_sorting' => false,
                    'enable_category' => false,
                    'category_id' => null,
                    'list_count' => $boardConfig['type'] === 'thumbnail' ? 12 : 20,
                    'permission_read' => 'all',
                    'permission_write' => 'admin',
                    'permission_comment' => 'member',
                    'is_active' => true,
                ]
            );

            $board = Board::withTrashed()->updateOrCreate(
                ['slug' => $boardConfig['slug']],
                [
                    'name' => $boardConfig['name'],
                    'description' => $boardConfig['description'],
                    'skin_id' => $defaultSkin?->id,
                    'template_id' => $template->id,
                    'is_active' => true,
                    'list_count' => $boardConfig['type'] === 'thumbnail' ? 12 : 20,
                    'enable_notice' => $boardConfig['type'] === 'general',
                    'is_single_page' => false,
                    'enable_sorting' => false,
                    'permission_read' => 'all',
                    'permission_write' => 'admin',
                    'permission_comment' => 'member',
                    'field_config' => $this->fieldConfig($boardConfig['type']),
                    'custom_fields_config' => $boardConfig['custom_fields'],
                ]
            );

            if ($board->trashed()) {
                $board->restore();
            }

            $this->upsertChildMenu($parentMenu->id, $boardConfig);
        }
    }

    private function upsertParentMenu(): AdminMenu
    {
        $existingMenu = AdminMenu::where('name', self::PARENT_MENU_NAME)
            ->whereNull('parent_id')
            ->first();

        return AdminMenu::updateOrCreate(
            [
                'name' => self::PARENT_MENU_NAME,
                'parent_id' => null,
            ],
            [
                'url' => null,
                'icon' => 'fa-users',
                'order' => $existingMenu?->order ?? 14,
                'is_active' => 1,
                'permission_key' => null,
            ]
        );
    }

    private function upsertChildMenu(int $parentId, array $boardConfig): void
    {
        $url = '/backoffice/board-posts/'.$boardConfig['slug'];
        $existingMenu = AdminMenu::where('url', $url)->first();

        AdminMenu::updateOrCreate(
            ['url' => $url],
            [
                'parent_id' => $parentId,
                'name' => $boardConfig['name'],
                'icon' => null,
                'order' => $existingMenu?->order ?? $boardConfig['menu_order'],
                'is_active' => 1,
                'permission_key' => null,
            ]
        );
    }

    private function fieldConfig(string $type): array
    {
        $isThumbnail = $type === 'thumbnail';

        return [
            'title' => ['enabled' => true, 'required' => true, 'label' => '제목'],
            'content' => ['enabled' => true, 'required' => true, 'label' => '내용'],
            'category' => ['enabled' => false, 'required' => false, 'label' => '카테고리'],
            'author_name' => ['enabled' => true, 'required' => false, 'label' => '작성자'],
            'password' => ['enabled' => false, 'required' => false, 'label' => '비밀번호'],
            'attachments' => ['enabled' => true, 'required' => false, 'label' => '첨부파일'],
            'thumbnail' => ['enabled' => $isThumbnail, 'required' => false, 'label' => '썸네일'],
            'is_secret' => ['enabled' => false, 'required' => false, 'label' => '비밀글'],
            'is_active' => ['enabled' => true, 'required' => true, 'label' => '공개여부'],
            'created_at' => ['enabled' => true, 'required' => false, 'label' => '등록일'],
        ];
    }
}
