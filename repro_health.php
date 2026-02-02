<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Citizen\OnboardingController;

$user = User::whereNull('household_id')->where('is_staff', false)->first();
if (!$user) { echo "no user"; exit; }
Auth::login($user);

$data = [
    'region_id' => 2,
    'address_text' => 'Test address',
    'housing_type' => 'owned',
    'primary_phone' => '0591111111',
    'members' => [
        [
            'full_name' => 'Test Person',
            'relation_to_head' => 'son',
            'national_id' => '700000001',
            'gender' => 'male',
            'birth_date' => '2020-01-16',
            'has_war_injury' => 1,
            'has_chronic_disease' => 0,
            'has_disability' => 0,
            'condition_type' => '?????',
            'health_notes' => '??????',
        ]
    ]
];

$request = Request::create('/citizen/onboarding', 'POST', $data);
$request->setUserResolver(fn () => $user);

try {
    $resp = app(OnboardingController::class)->store($request);
    echo "redirect:" . $resp->getTargetUrl();
} catch (\Throwable $e) {
    echo get_class($e) . ':' . $e->getMessage();
}
