<?php

/**
 * 마이페이지 즐겨찾기 — 퍼블리싱 체크박스 id 와 사이트 메뉴 매핑.
 */
return [
    'max_favorites' => 6,

    'groups' => [
        [
            'title' => '학회소개',
            'items' => [
                ['code' => 'favorite11', 'name' => '학회개요', 'url' => '/introduction/overview'],
                ['code' => 'favorite12', 'name' => '인사말', 'url' => '/introduction/greeting'],
                ['code' => 'favorite13', 'name' => '학회 연혁', 'url' => '/introduction/history'],
                ['code' => 'favorite14', 'name' => '회칙', 'url' => '/introduction/bylaws'],
                ['code' => 'favorite15', 'name' => '임원진', 'url' => '/introduction/officers'],
                ['code' => 'favorite16', 'name' => '오시는 길', 'url' => '/introduction/location'],
            ],
        ],
        [
            'title' => '학술대회',
            'items' => [
                ['code' => 'favorite21', 'name' => '학술대회', 'url' => '/academic_event/conference'],
                ['code' => 'favorite22', 'name' => '연수강좌', 'url' => '/academic_event/training_course'],
            ],
        ],
        [
            'title' => '산하위원회',
            'items' => [],
        ],
        [
            'title' => '학회 자료실',
            'items' => [
                ['code' => 'favorite31', 'name' => '일반 자료실', 'url' => '/archives/general'],
                ['code' => 'favorite32', 'name' => '학술자료', 'url' => '/archives/academic'],
            ],
        ],
        [
            'title' => '회원광장',
            'items' => [
                ['code' => 'favorite41', 'name' => '학회공지', 'url' => '/member_plaza/society_notices'],
                ['code' => 'favorite42', 'name' => '기타공지', 'url' => '/member_plaza/other_notices'],
                ['code' => 'favorite43', 'name' => '학회 앨범', 'url' => '/member_plaza/society_album'],
                ['code' => 'favorite44', 'name' => '회비 납부 안내', 'url' => '/member_plaza/fee_payment_guide'],
            ],
        ],
        [
            'title' => '우리동네 주치의',
            'items' => [],
        ],
        [
            'title' => '온라인 아카데미',
            'items' => [],
        ],
    ],
];
