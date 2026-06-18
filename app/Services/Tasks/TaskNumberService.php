<?php
namespace App\Services\Tasks;use App\Models\Task;use App\Models\Workspace;use Illuminate\Support\Facades\DB;
class TaskNumberService{public function next(Workspace $workspace):string{return DB::transaction(function()use($workspace){$prefix='WP-'.$workspace->id.'-TASK-'.now()->format('Y').'-';$last=Task::query()->withoutWorkspaceScope()->where('workspace_id',$workspace->id)->where('task_number','like',$prefix.'%')->lockForUpdate()->orderByDesc('id')->value('task_number');$n=$last?(int)substr($last,-6)+1:1;return $prefix.str_pad((string)$n,6,'0',STR_PAD_LEFT);});}}
