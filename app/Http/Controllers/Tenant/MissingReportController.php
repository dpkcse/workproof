<?php
namespace App\Http\Controllers\Tenant;use App\Http\Controllers\Controller;use App\Models\MissingDailyReport;
class MissingReportController extends Controller{use Concerns;public function index(){ $this->requirePerm('tenant.daily_reports.review');return view('tenant.missing-reports.index',['missingReports'=>MissingDailyReport::with('user')->latest('report_date')->paginate(15)]);}}
