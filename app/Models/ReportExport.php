<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class ReportExport extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','user_id','report_key','filters','format','status','file_path','file_disk','error_message','started_at','completed_at'];protected function casts():array{return['filters'=>'array','started_at'=>'datetime','completed_at'=>'datetime'];}public function user(){return $this->belongsTo(User::class);} }
