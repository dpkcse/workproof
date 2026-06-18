<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class DailyReportItem extends Model{use BelongsToWorkspace;public const TYPES=['completed','in_progress','pending','blocker','tomorrow_plan'];protected $fillable=['workspace_id','daily_report_id','task_id','item_type','title','description','time_spent_minutes','sort_order'];public function report(){return $this->belongsTo(DailyReport::class,'daily_report_id');}public function task(){return $this->belongsTo(Task::class);}}
