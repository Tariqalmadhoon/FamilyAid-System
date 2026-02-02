<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('households')->orderBy('id','desc')->limit(5)->get();
echo 'Total households: '.DB::table('households')->count()."\n";
foreach ($rows as $r) {
    echo $r->id.' | '.$r->head_name.' | '.$r->status."\n";
}
