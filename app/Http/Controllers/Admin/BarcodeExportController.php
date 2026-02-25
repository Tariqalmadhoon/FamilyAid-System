<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BarcodeExportController extends Controller
{
    /**
     * Show barcode export printable page.
     */
    public function index(): View
    {
        $registrationUrl = 'https://alaedoon-camps.org';
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=900x900&margin=12&data=' . urlencode($registrationUrl);

        return view('admin.barcode-export', [
            'registrationUrl' => $registrationUrl,
            'qrImageUrl' => $qrImageUrl,
        ]);
    }
}

