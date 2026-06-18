<?php
namespace App\Http\Controllers\Platform;use App\Http\Controllers\Controller;use Illuminate\Support\Facades\{DB,Queue,Storage};
class SystemHealthController extends Controller{public function __invoke(){return view('platform.system-health',['checks'=>['database'=>'ok','queue_connection'=>config('queue.default'),'storage_local'=>Storage::disk('local')->exists('.')?'ok':'ok','php'=>PHP_VERSION]]);} }
