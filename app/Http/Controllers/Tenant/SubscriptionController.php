<?php
namespace App\Http\Controllers\Tenant;use App\Http\Controllers\Controller;use App\Models\Plan;use App\Services\Billing\SubscriptionService;use App\Support\CurrentWorkspace;use Illuminate\Http\Request;
class SubscriptionController extends Controller{use Concerns;public function changePlan(Request $r,CurrentWorkspace $cw,SubscriptionService $service){$d=$r->validate(['plan_id'=>'required|exists:plans,id']);$plan=Plan::where('is_active',true)->findOrFail($d['plan_id']);$service->changePlan($this->ws($cw),$plan);return back()->with('status','Plan change saved.');}}
