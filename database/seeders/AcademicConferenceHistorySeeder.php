<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use App\Models\Board;
use App\Models\BoardSkin;
use App\Models\BoardTemplate;
use Illuminate\Database\Seeder;

class AcademicConferenceHistorySeeder extends Seeder
{
    private const BOARD_NAME = '학술대회 연혁';

    private const BOARD_SLUG = 'academic_conference_history';

    private const MENU_ID = 104;

    public function run(): void
    {
        $defaultSkin = BoardSkin::where('name', '기본 스킨')->first() ?? BoardSkin::first();

        $template = BoardTemplate::updateOrCreate(
            ['name' => self::BOARD_NAME],
            [
                'description' => '행사 기간/행사명/자료집 기반 학술대회 연혁 게시판',
                'skin_id' => $defaultSkin?->id,
                'field_config' => $this->fieldConfig(),
                'custom_fields_config' => $this->customFieldsConfig(),
                'enable_notice' => false,
                'enable_sorting' => true,
                'enable_category' => false,
                'category_id' => null,
                'list_count' => 20,
                'permission_read' => 'all',
                'permission_write' => 'admin',
                'permission_comment' => 'member',
                'is_active' => true,
            ]
        );

        $board = Board::withTrashed()->updateOrCreate(
            ['slug' => self::BOARD_SLUG],
            [
                'name' => self::BOARD_NAME,
                'description' => '홈페이지 학술대회 연혁 관리',
                'skin_id' => $defaultSkin?->id,
                'template_id' => $template->id,
                'is_active' => true,
                'table_created' => false,
                'list_count' => 20,
                'enable_notice' => false,
                'is_single_page' => false,
                'enable_sorting' => true,
                'hot_threshold' => 100,
                'permission_read' => 'all',
                'permission_write' => 'admin',
                'permission_comment' => 'member',
                'field_config' => $this->fieldConfig(),
                'custom_fields_config' => $this->customFieldsConfig(),
            ]
        );

        if ($board->trashed()) {
            $board->restore();
        }

        $this->upsertMenu();
    }

    private function upsertMenu(): void
    {
        $homepageMenu = AdminMenu::where('name', '홈페이지 관리')
            ->whereNull('parent_id')
            ->first();

        $parentId = $homepageMenu?->id ?? 22;
        $url = '/backoffice/board-posts/'.self::BOARD_SLUG;
        $existingMenu = AdminMenu::where('url', $url)->first();
        $order = $existingMenu?->order ?? ((int) AdminMenu::where('parent_id', $parentId)->max('order') + 1);

        AdminMenu::updateOrCreate(
            ['id' => $existingMenu?->id ?? self::MENU_ID],
            [
                'parent_id' => $parentId,
                'name' => self::BOARD_NAME,
                'url' => $url,
                'icon' => null,
                'order' => $order,
                'is_active' => 1,
                'permission_key' => null,
            ]
        );
    }

    private function fieldConfig(): array
    {
        return [
            'title' => ['enabled' => true, 'required' => true, 'label' => '행사명'],
            'content' => ['enabled' => false, 'required' => false, 'label' => '내용'],
            'category' => ['enabled' => false, 'required' => false, 'label' => '카테고리'],
            'author_name' => ['enabled' => false, 'required' => false, 'label' => '작성자'],
            'password' => ['enabled' => false, 'required' => false, 'label' => '비밀번호'],
            'attachments' => ['enabled' => true, 'required' => false, 'label' => '행사자료'],
            'thumbnail' => ['enabled' => false, 'required' => false, 'label' => '썸네일'],
            'is_secret' => ['enabled' => false, 'required' => false, 'label' => '비밀글'],
            'is_active' => ['enabled' => true, 'required' => true, 'label' => '공개여부'],
            'created_at' => ['enabled' => false, 'required' => false, 'label' => '등록일'],
        ];
    }

    private function customFieldsConfig(): array
    {
        return [
            [
                'name' => 'event_start_date',
                'type' => 'date',
                'label' => '행사 시작일',
                'options' => null,
                'required' => true,
                'max_length' => null,
                'placeholder' => null,
            ],
            [
                'name' => 'event_end_date',
                'type' => 'date',
                'label' => '행사 종료일',
                'options' => null,
                'required' => true,
                'max_length' => null,
                'placeholder' => null,
            ],
        ];
    }
}
