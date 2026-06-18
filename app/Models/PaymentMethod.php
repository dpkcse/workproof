<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class PaymentMethod extends Model{protected $fillable=['workspace_id','gateway','display_name','config','is_active','is_default'];protected function casts():array{return ['config'=>'array','is_active'=>'boolean','is_default'=>'boolean'];}public function workspace(){return $this->belongsTo(Workspace::class);}}
