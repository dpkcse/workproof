<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class TaskActivityLog extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','task_id','user_id','action','old_values','new_values','description'];protected function casts():array{return ['old_values'=>'array','new_values'=>'array'];}public function task(){return $this->belongsTo(Task::class);}public function user(){return $this->belongsTo(User::class,'user_id');}}
