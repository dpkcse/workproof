<?php
namespace App\Models\Concerns;
use App\Models\Workspace;use Illuminate\Database\Eloquent\Builder;
trait BelongsToWorkspace{protected static function bootBelongsToWorkspace():void{static::creating(function($model){if(empty($model->workspace_id)&&app()->bound('CurrentWorkspace')&&app('CurrentWorkspace')){$model->workspace_id=app('CurrentWorkspace')->id;}});}public function workspace(){return $this->belongsTo(Workspace::class);}public function scopeForWorkspace(Builder $query,int|string $workspaceId):Builder{return $query->where($query->getModel()->getTable().'.workspace_id',$workspaceId);}}
