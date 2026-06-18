<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class LoginHistory extends Model{protected $fillable=['user_id','workspace_id','ip_address','user_agent','logged_in_at','successful','failure_reason'];protected function casts():array{return['logged_in_at'=>'datetime','successful'=>'bool'];}public function user(){return $this->belongsTo(User::class);}public function workspace(){return $this->belongsTo(Workspace::class);} }
