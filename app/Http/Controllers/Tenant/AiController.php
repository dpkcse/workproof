<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\MissingDailyReport;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Services\AI\AiService;
use App\Services\Billing\PlanLimitService;
use App\Support\CurrentWorkspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private AiService $ai, private PlanLimitService $limits) {}

    public function dailyReportSummary(Request $request, CurrentWorkspace $cw): JsonResponse
    {
        $workspace = $cw->required();if(! $this->limits->canUseAi($workspace)) return response()->json(['success'=>false,'message'=>'AI quota reached. Please upgrade your plan.'],422);
        $data = $request->validate(['date'=>['required','date'],'department_id'=>['nullable','integer'],'team_id'=>['nullable','integer'],'user_id'=>['nullable','integer']]);
        $reports = DailyReport::query()->where('workspace_id',$workspace->id)->whereDate('report_date',$data['date'])->when($data['user_id']??null,fn($q,$v)=>$q->where('user_id',$v))->get(['id','user_id','completed_summary','in_progress_summary','blocker_summary','tomorrow_plan','is_late']);
        $missing = MissingDailyReport::query()->where('workspace_id',$workspace->id)->whereDate('report_date',$data['date'])->count();
        $response = $this->ai->generate($workspace,'daily_report_summary','Summarize daily reports into completed work, in-progress work, blockers, missing reports count, late reports count, and follow-up points. Cite report IDs.', ['date'=>$data['date'],'reports'=>$reports->toArray(),'missing_reports_count'=>$missing,'late_reports_count'=>$reports->where('is_late',true)->count()], ['source_type'=>'daily_report','source_ids'=>$reports->pluck('id')->all()]);
        if ($response->success) $summary = $this->ai->saveSummary($workspace,'daily_report_summary',$response->text, ['user_id'=>auth()->id(),'source_ids'=>$reports->pluck('id')->all(),'title'=>'Daily report summary '.$data['date'],'provider'=>$response->provider,'model'=>$response->model,'generated_by'=>auth()->id()]);
        return response()->json(['success'=>$response->success,'message'=>$response->error_message,'summary_id'=>$summary->id ?? null,'text'=>$response->text]);
    }

    public function taskCleaner(Request $request, CurrentWorkspace $cw): JsonResponse
    {
        $data = $request->validate(['raw_text'=>['required','string','max:5000']]);
        $workspace=$cw->required();if(! $this->limits->canUseAi($workspace)) return response()->json(['success'=>false,'message'=>'AI quota reached. Please upgrade your plan.'],422);$response = $this->ai->generate($workspace,'task_cleaner','Clean this raw task text. Return JSON with cleaned_title, cleaned_description, suggested_priority, suggested_checklist_items. Suggestion only; do not decide automatically.', ['raw_text'=>$data['raw_text']]);
        return response()->json(['success'=>$response->success,'message'=>$response->error_message,'suggestion'=>$response->text]);
    }

    public function checklistGenerator(Request $request, CurrentWorkspace $cw): JsonResponse
    {
        $data = $request->validate(['task_id'=>['nullable','integer'],'title'=>['nullable','string'],'description'=>['nullable','string']]);
        $task = isset($data['task_id']) ? Task::query()->findOrFail($data['task_id']) : null;
        $context = ['task_id'=>$task?->id,'title'=>$task?->title ?? $data['title'] ?? null,'description'=>$task?->description ?? $data['description'] ?? null];
        $workspace=$cw->required();if(! $this->limits->canUseAi($workspace)) return response()->json(['success'=>false,'message'=>'AI quota reached. Please upgrade your plan.'],422);$response = $this->ai->generate($workspace,'checklist_generator','Generate practical checklist items for this task as JSON array. Do not add them automatically. Cite task ID if available.', $context, ['source_type'=>'task','source_ids'=>array_filter([$task?->id])]);
        return response()->json(['success'=>$response->success,'message'=>$response->error_message,'items'=>$response->text]);
    }

    public function acceptChecklist(Request $request, CurrentWorkspace $cw, Task $task): RedirectResponse
    {
        abort_unless($task->workspace_id === $cw->id(), 404);
        $data = $request->validate(['items'=>['required','array','min:1'],'items.*'=>['required','string','max:255']]);
        $max = (int) $task->checklistItems()->max('sort_order');
        foreach ($data['items'] as $i => $item) TaskChecklist::query()->create(['workspace_id'=>$task->workspace_id,'task_id'=>$task->id,'title'=>$item,'sort_order'=>$max+$i+1]);
        return back()->with('status','AI checklist suggestions accepted.');
    }

    public function riskSummary(CurrentWorkspace $cw): JsonResponse
    {
        $w=$cw->required();if(! $this->limits->canUseAi($w)) return response()->json(['success'=>false,'message'=>'AI quota reached. Please upgrade your plan.'],422);$tasks=Task::query()->where('workspace_id',$w->id)->where(fn($q)=>$q->where('due_date','<',now()->toDateString())->whereNot('status','completed')->orWhere('priority','high')->orWhere('status','reopened'))->limit(50)->get(['id','title','priority','status','due_date']);$missing=MissingDailyReport::query()->where('workspace_id',$w->id)->where('status','open')->limit(50)->get(['id','user_id','report_date']);
        $response=$this->ai->generate($w,'risk_summary','Create a manager-friendly overdue/risk summary from tasks and missing reports. Include follow-up points and source IDs.', ['tasks'=>$tasks->toArray(),'missing_reports'=>$missing->toArray()], ['source_type'=>'task','source_ids'=>$tasks->pluck('id')->all()]);
        if($response->success)$this->ai->saveSummary($w,'risk_summary',$response->text,['user_id'=>auth()->id(),'source_ids'=>$tasks->pluck('id')->all(),'title'=>'Risk summary','provider'=>$response->provider,'model'=>$response->model,'generated_by'=>auth()->id()]);
        return response()->json(['success'=>$response->success,'message'=>$response->error_message,'text'=>$response->text]);
    }

    public function followUpSuggestion(CurrentWorkspace $cw): JsonResponse
    {
        $w=$cw->required();if(! $this->limits->canUseAi($w)) return response()->json(['success'=>false,'message'=>'AI quota reached. Please upgrade your plan.'],422);$tasks=Task::query()->where('workspace_id',$w->id)->where(fn($q)=>$q->whereIn('status',['in_progress','reopened','hold'])->orWhere('due_date','<',now()->toDateString()))->limit(50)->get(['id','title','status','priority','due_date']);
        $response=$this->ai->generate($w,'followup_suggestion','Suggest who managers should follow up with, why, related task/report IDs, and a suggested message.', ['tasks'=>$tasks->toArray()], ['source_type'=>'task','source_ids'=>$tasks->pluck('id')->all()]);
        return response()->json(['success'=>$response->success,'message'=>$response->error_message,'text'=>$response->text]);
    }

    public function weeklySummary(Request $request, CurrentWorkspace $cw): JsonResponse
    {
        $data=$request->validate(['week_start'=>['required','date']]);$w=$cw->required();if(! $this->limits->canUseAi($w)) return response()->json(['success'=>false,'message'=>'AI quota reached. Please upgrade your plan.'],422);$start=\Carbon\Carbon::parse($data['week_start'])->startOfWeek();$end=$start->copy()->endOfWeek();
        $tasks=Task::query()->where('workspace_id',$w->id)->whereBetween('updated_at',[$start,$end])->get(['id','title','status','priority','due_date','completed_at']);$reports=DailyReport::query()->where('workspace_id',$w->id)->whereBetween('report_date',[$start,$end])->get(['id','user_id','status','report_date','blocker_summary','is_late']);
        $response=$this->ai->generate($w,'weekly_summary','Summarize weekly performance: completed tasks, pending/overdue, report consistency, blockers, performance notes. Cite source IDs.', ['week_start'=>$start->toDateString(),'week_end'=>$end->toDateString(),'tasks'=>$tasks->toArray(),'reports'=>$reports->toArray()], ['source_type'=>'weekly','source_ids'=>$tasks->pluck('id')->merge($reports->pluck('id'))->all()]);
        if($response->success)$summary=$this->ai->saveSummary($w,'weekly_summary',$response->text,['user_id'=>auth()->id(),'source_ids'=>$tasks->pluck('id')->merge($reports->pluck('id'))->all(),'title'=>'Weekly summary '.$start->toDateString(),'provider'=>$response->provider,'model'=>$response->model,'generated_by'=>auth()->id()]);
        return response()->json(['success'=>$response->success,'message'=>$response->error_message,'summary_id'=>$summary->id ?? null,'text'=>$response->text]);
    }
}
