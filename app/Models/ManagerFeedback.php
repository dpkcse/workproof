<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class ManagerFeedback extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','user_id','given_by','subject_type','subject_id','feedback','feedback_type'];public function user(){return $this->belongsTo(User::class);}public function giver(){return $this->belongsTo(User::class,'given_by');}}
