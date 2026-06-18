<?php
namespace App\Http\Controllers\Tenant;use App\Http\Controllers\Controller;use App\Models\{PerformanceScore,User};
class PerformanceController extends Controller{use Concerns;public function index(){return view('tenant.reports.employee-performance',['rows'=>PerformanceScore::with('user')->latest('score_date')->paginate(50)]);}public function user(User $user){return view('tenant.reports.employee-performance',['rows'=>PerformanceScore::with('user')->where('user_id',$user->id)->latest('score_date')->paginate(50)]);}}
