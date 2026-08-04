<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class MarketingGuideController extends Controller
{
    public function download(): Response
    {
        return Pdf::loadView('marketing.customer-conversion-guide')
            ->setPaper('a4')
            ->download('oresamsub-customer-conversion-guide.pdf');
    }
}
