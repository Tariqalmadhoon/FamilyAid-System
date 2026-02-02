<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
echo 'Users: '.DB::table('users')->count()."\n";
echo 'Households: '.DB::table('households')->count()."\n";
echo 'Citizens without household: '.DB::table('users')->whereNull('household_id')->where('is_staff',false)->count()."\n";
$lastUsers = DB::table('users')->orderBy('id','desc')->limit(5)->get();
foreach($lastUsers as $u){
    echo $u->id.' | '.$u->name.' | household_id='.($u->household_id ?: 'null')."\n";
}
