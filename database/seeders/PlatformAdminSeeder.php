<?php
namespace Database\Seeders;use App\Models\User;use Illuminate\Database\Seeder;use Illuminate\Support\Facades\Hash;
class PlatformAdminSeeder extends Seeder{public function run():void{$email=env('PLATFORM_ADMIN_EMAIL','admin@naxas.ai');$user=User::firstOrCreate(['email'=>$email],['name'=>env('PLATFORM_ADMIN_NAME','NAXAS Admin'),'password'=>Hash::make(env('PLATFORM_ADMIN_PASSWORD','change-password'))]);$user->forceFill(['is_platform_user'=>true,'status'=>'active'])->save();}}
