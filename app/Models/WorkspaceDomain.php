<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class WorkspaceDomain extends Model{protected $fillable=['workspace_id','domain','type','is_primary','is_verified'];protected function casts():array{return ['is_primary'=>'boolean','is_verified'=>'boolean'];}public function workspace(){return $this->belongsTo(Workspace::class);}}
