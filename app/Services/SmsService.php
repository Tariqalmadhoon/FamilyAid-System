<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SmsService
{
    public function __construct(private string $driver = 'log')
    {
    }

    public function send(string $phone, string $message): void
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($this->driver === 'log') {
            Log::info('SMS (log driver)', [
                'to' => $normalizedPhone,
                'message' => $message,
            ]);
            return;
        }

        if ($this->driver === 'twilio') {
            $this->sendViaTwilio($normalizedPhone, $message);
            return;
        }

        if ($this->driver === 'smsto') {
            $this->sendViaSmsTo($normalizedPhone, $message);
            return;
        }

        throw new RuntimeException("Unsupported SMS driver [{$this->driver}].");
    }

    private function sendViaTwilio(string $phone, string $message): void
    {
        $sid = (string) config('services.sms.twilio.sid');
        $token = (string) config('services.sms.twilio.token');
        $from = (string) config('services.sms.twilio.from');
        $messagingServiceSid = (string) config('services.sms.twilio.messaging_service_sid');

        if ($sid === '' || $token === '' || ($from === '' && $messagingServiceSid === '')) {
            throw new RuntimeException('Twilio SMS is not fully configured.');
        }

        $payload = [
            'To' => $phone,
            'Body' => $message,
        ];

        if ($messagingServiceSid !== '') {
            $payload['MessagingServiceSid'] = $messagingServiceSid;
        } else {
            $payload['From'] = $from;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        try {
            $response = Http::asForm()
                ->withBasicAuth($sid, $token)
                ->connectTimeout((int) config('services.sms.connect_timeout', 5))
                ->timeout((int) config('services.sms.timeout', 10))
                ->retry(2, 250)
                ->post($url, $payload);
        } catch (Throwable $e) {
            Log::error('SMS send failed (transport)', [
                'driver' => 'twilio',
                'to' => $phone,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('SMS transport failure.', previous: $e);
        }

        if (!$response->successful()) {
            Log::error('SMS send failed (provider response)', [
                'driver' => 'twilio',
                'to' => $phone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new RuntimeException('SMS provider rejected the request.');
        }
    }

    private function sendViaSmsTo(string $phone, string $message): void
    {
        $endpoint = (string) config('services.sms.smsto.endpoint', 'https://api.sms.to/sms/send');
        $apiKey = (string) config('services.sms.smsto.api_key');
        $senderId = (string) config('services.sms.smsto.sender_id');
        $callbackUrl = (string) config('services.sms.smsto.callback_url');
        $bypassOptOut = (bool) config('services.sms.smsto.bypass_opt_out', false);

        if ($apiKey === '') {
            throw new RuntimeException('sms.to API key is missing.');
        }

        $payload = [
            'to' => $phone,
            'message' => $message,
        ];

        if ($senderId !== '') {
            $payload['sender_id'] = $senderId;
        }

        if ($callbackUrl !== '') {
            $payload['callback_url'] = $callbackUrl;
        }

        if ($bypassOptOut) {
            $payload['bypass_optout'] = true;
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withToken($apiKey)
                ->connectTimeout((int) config('services.sms.connect_timeout', 5))
                ->timeout((int) config('services.sms.timeout', 10))
                ->retry(2, 250, null, false)
                ->post($endpoint, $payload);
        } catch (Throwable $e) {
            Log::error('SMS send failed (transport)', [
                'driver' => 'smsto',
                'to' => $phone,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('SMS transport failure.', previous: $e);
        }

        if (!$response->successful()) {
            $providerMessage = (string) data_get($response->json(), 'message', $response->body());
            Log::error('SMS send failed (provider response)', [
                'driver' => 'smsto',
                'to' => $phone,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
                'provider_message' => $providerMessage,
            ]);
            throw new RuntimeException('SMS provider rejected the request: ' . $providerMessage);
        }
    }

    private function normalizePhone(string $phone): string
    {
        $raw = trim($phone);
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        $defaultCountryCode = (string) config('services.sms.default_country_code', '+970');
        $defaultCountryDigits = preg_replace('/\D+/', '', $defaultCountryCode) ?? '';

        if ($digits === '') {
            throw new RuntimeException('Invalid phone number.');
        }

        if (str_starts_with($raw, '+')) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '00')) {
            return '+' . substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && $defaultCountryDigits !== '') {
            return '+' . $defaultCountryDigits . substr($digits, 1);
        }

        if (strlen($digits) >= 11) {
            return '+' . $digits;
        }

        if ($defaultCountryDigits !== '') {
            return '+' . $defaultCountryDigits . $digits;
        }

        return '+' . $digits;
    }
}
