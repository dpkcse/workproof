<?php
namespace App\Models;
use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class AiSummary extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','user_id','summary_type','subject_type','subject_id','source_ids','title','content','status','provider','model','generated_by','generated_at','metadata'];protected function casts():array{return ['source_ids'=>'array','metadata'=>'array','generated_at'=>'datetime'];}public function user(){return $this->belongsTo(User::class);}public function generator(){return $this->belongsTo(User::class,'generated_by');}}
