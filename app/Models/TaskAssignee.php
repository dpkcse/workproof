<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class TaskAssignee extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','task_id','user_id','assigned_by','assigned_at','role'];protected function casts():array{return ['assigned_at'=>'datetime'];}public function task(){return $this->belongsTo(Task::class);}public function user(){return $this->belongsTo(User::class,'user_id');}}
