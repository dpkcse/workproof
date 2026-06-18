<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class TaskChecklist extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','task_id','title','is_completed','completed_by','completed_at','sort_order'];protected function casts():array{return ['is_completed'=>'boolean','completed_at'=>'datetime'];}public function task(){return $this->belongsTo(Task::class);}public function user(){return $this->belongsTo(User::class,'user_id');}}
