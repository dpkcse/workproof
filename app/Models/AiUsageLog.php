<?php
namespace App\Models;
use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class AiUsageLog extends Model{use BelongsToWorkspace;public $timestamps=false;protected $fillable=['workspace_id','user_id','feature','provider','model','input_tokens','output_tokens','estimated_cost','status','error_message','source_type','source_id','metadata','created_at'];protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime','estimated_cost'=>'decimal:6'];}public function user(){return $this->belongsTo(User::class);} }
