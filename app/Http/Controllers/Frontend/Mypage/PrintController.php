<?php

namespace App\Http\Controllers\Frontend\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PrintController extends Controller
{
    public function receipt(): View
    {
        return $this->render('print_receipt', '영수증', '영수증', 'print_receipt');
    }

    public function receiptSave(): View
    {
        return $this->render('print_receipt_save', '영수증', '영수증', 'print_receipt_save');
    }

    public function participation(): View
    {
        return $this->render('print_participation', '참가증명서', '참가증명서', 'print_participation');
    }

    public function completion(): View
    {
        return $this->render('print_completion', '이수증', '이수증', 'print_completion');
    }

    public function letterAppointment(): View
    {
        return $this->render('print_letter_appointment', '이수증', '이수증', 'print_letter_appointment');
    }

    private function render(string $view, string $gName, string $sName, string $slug): View
    {
        $page_type = 'professional';
        $gNum = 'print';
        $sNum = '00';
        $gSlug = $slug;

        return view('mypage.' . $view, compact('page_type', 'gNum', 'sNum', 'gName', 'sName', 'gSlug'));
    }
}
