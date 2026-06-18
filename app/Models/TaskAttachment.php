<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class TaskAttachment extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','task_id','uploaded_by','file_name','original_name','file_path','file_disk','mime_type','file_size','attachment_type'];protected function casts():array{return [];}public function task(){return $this->belongsTo(Task::class);}public function user(){return $this->belongsTo(User::class,'user_id');}}
