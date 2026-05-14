<?php

/**
 * 주치의 상세: 기능의학 검사·치료 가능 영역 체크박스(프로토타입 순서).
 * 값은 local_doctors.functional_tests_selected / treatment_areas_selected JSON 배열에 id로 저장.
 */
return [
    'functional_tests' => [
        ['id' => 'organic_acids', 'label' => '유기산 검사 (Organic Acids Test)'],
        ['id' => 'heavy_metal', 'label' => '중금속 검사'],
        ['id' => 'microbiome', 'label' => '마이크로바이옴 검사'],
        ['id' => 'nutrient_panel', 'label' => '영양소 결핍 패널'],
        ['id' => 'food_igg', 'label' => '식품 과민반응 검사 (IgG)'],
        ['id' => 'hormone_balance', 'label' => '호르몬 균형 검사'],
        ['id' => 'mitochondrial', 'label' => '미토콘드리아 기능 검사'],
        ['id' => 'oxidative_stress', 'label' => '산화 스트레스 검사'],
        ['id' => 'genetic_snp', 'label' => '유전자 검사 (SNP 분석)'],
        ['id' => 'adrenal_stress', 'label' => '부신 스트레스 검사'],
    ],
    'treatment_areas' => [
        ['id' => 'chronic_fatigue', 'label' => '만성 피로'],
        ['id' => 'digestive', 'label' => '소화기 질환'],
        ['id' => 'autoimmune', 'label' => '자가면역 질환'],
        ['id' => 'metabolic_syndrome', 'label' => '대사 증후군'],
        ['id' => 'thyroid', 'label' => '갑상선 기능 이상'],
        ['id' => 'hormone_imbalance', 'label' => '호르몬 불균형'],
        ['id' => 'allergy_atopy', 'label' => '알레르기 / 아토피'],
        ['id' => 'weight', 'label' => '비만 / 체중 관리'],
        ['id' => 'sleep', 'label' => '수면 장애'],
        ['id' => 'cognitive', 'label' => '인지 기능 저하'],
        ['id' => 'cardiovascular', 'label' => '심혈관 건강'],
        ['id' => 'antiaging', 'label' => '항노화 / 노화 예방'],
    ],
];
