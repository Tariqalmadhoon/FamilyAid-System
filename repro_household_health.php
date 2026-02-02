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
    'address_text' => 'Addr',
    'housing_type' => 'family_hosted',
    'primary_phone' => '4444444444',
    'secondary_phone' => '4444444444',
    'has_war_injury' => 'on',
    'has_chronic_disease' => '0',
    'has_disability' => 'on',
    'condition_type' => 'Ipsum reprehenderit',
    'condition_notes' => 'Magni voluptas repre',
];

$request = Request::create('/citizen/onboarding', 'POST', $data);
$request->setUserResolver(fn () => $user);

try {
    app(OnboardingController::class)->store($request);
    echo "ok";
} catch (\Illuminate\Validation\ValidationException $e) {
    var_export($e->errors());
}
