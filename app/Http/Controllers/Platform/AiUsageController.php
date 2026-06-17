<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AiUsageController extends Controller
{
    public function __invoke(): View
    {
        return view('platform.ai-usage.index', [
            'aiUsageTableExists' => Schema::hasTable('ai_usage_logs'),
            'usageLogs' => Schema::hasTable('ai_usage_logs') ? DB::table('ai_usage_logs')->latest()->limit(50)->get() : collect(),
        ]);
    }
}
