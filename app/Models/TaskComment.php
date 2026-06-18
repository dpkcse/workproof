<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\SoftDeletes;
class TaskComment extends Model{use BelongsToWorkspace,SoftDeletes;protected $fillable=['workspace_id','task_id','user_id','comment','parent_id'];protected function casts():array{return [];}public function task(){return $this->belongsTo(Task::class);}public function user(){return $this->belongsTo(User::class,'user_id');}}
