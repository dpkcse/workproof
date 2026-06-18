<?php
namespace App\Models;use App\Models\Concerns\BelongsToWorkspace;use Illuminate\Database\Eloquent\Model;
class StorageUsageLog extends Model{use BelongsToWorkspace;protected $fillable=['workspace_id','used_mb','calculated_at','metadata'];protected function casts():array{return ['used_mb'=>'decimal:2','calculated_at'=>'datetime','metadata'=>'array'];}}
