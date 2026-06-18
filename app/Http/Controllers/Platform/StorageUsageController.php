<?php
namespace App\Http\Controllers\Platform;use App\Http\Controllers\Controller;use App\Models\Workspace;use Illuminate\Support\Facades\Storage;
class StorageUsageController extends Controller{public function __invoke(){return view('platform.storage-usage',['workspaces'=>Workspace::query()->withCount('users')->paginate(50)]);} }
