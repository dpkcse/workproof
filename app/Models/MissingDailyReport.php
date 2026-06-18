<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class MissingDailyReport extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','user_id','report_date','expected_deadline','status','marked_at','resolved_at','resolved_by','note'];protected function casts():array{return['report_date'=>'date','expected_deadline'=>'datetime','marked_at'=>'datetime','resolved_at'=>'datetime'];}public function user(){return $this->belongsTo(User::class);}public function resolver(){return $this->belongsTo(User::class,'resolved_by');}}
