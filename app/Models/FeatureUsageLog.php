<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class FeatureUsageLog extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','feature_key','usage_count','usage_date','metadata'];protected function casts():array{return ['usage_date'=>'date','metadata'=>'array'];}}
