<?php
namespace App\Http\Controllers\Tenant;
use App\Http\Controllers\Controller;use App\Models\AiSummary;use App\Support\CurrentWorkspace;use Illuminate\View\View;
class AiSummaryController extends Controller{public function __invoke(CurrentWorkspace $cw):View{$w=$cw->required();return view('tenant.ai-summary.index',['summaries'=>AiSummary::query()->where('workspace_id',$w->id)->latest('generated_at')->paginate(20),'workspace'=>$w]);}}
