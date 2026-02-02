<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Citizen\OnboardingController;

$user = User::where('national_id','49458148')->first();
if(!$user){ die('no user'); }
Auth::login($user);

$data = [
    'region_id' => 2,
    'address_text' => 'Test address',
    'housing_type' => 'owned',
    'primary_phone' => '0592324490',
    'members' => [
        [
            'full_name' => '????? ??? ???',
            'relation_to_head' => 'son',
            'national_id' => '400000008',
            'gender' => 'male',
            'birth_date' => '2026-01-16',
        ],
    ],
];

$request = Request::create('/citizen/onboarding','POST',$data);
$request->setUserResolver(fn()=> $user);

try {
    $resp = app(OnboardingController::class)->store($request);
    echo 'redirect:' . $resp->getTargetUrl();
} catch (\Illuminate\Validation\ValidationException $e) {
    var_export($e->errors());
}
