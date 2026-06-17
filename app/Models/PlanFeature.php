<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class PlanFeature extends Model{protected $fillable=['plan_id','feature_key','feature_name','value','enabled'];protected function casts():array{return ['enabled'=>'boolean'];}public function plan(){return $this->belongsTo(Plan::class);}}
