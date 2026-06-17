<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __invoke(): View
    {
        return view('platform.support.index');
    }
}
