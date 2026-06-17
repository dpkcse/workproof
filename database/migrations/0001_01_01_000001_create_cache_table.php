<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{if(!Schema::hasTable('cache')){Schema::create('cache',fn(Blueprint $t)=>[$t->string('key')->primary(),$t->mediumText('value'),$t->integer('expiration')]);}if(!Schema::hasTable('cache_locks')){Schema::create('cache_locks',fn(Blueprint $t)=>[$t->string('key')->primary(),$t->string('owner'),$t->integer('expiration')]);}}public function down():void{}};
