<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class NotificationService
{
    public function notify(iterable $users, string $type, string $title, string $message, ?string $url = null): void
    {
        foreach ($users as $user) {
            AppNotification::create(compact('type', 'title', 'message', 'url') + ['user_id' => $user->id]);
            $this->sendPush($user, $title, $message, $url);
        }
    }

    private function sendPush(User $user, string $title, string $message, ?string $url): void
    {
        $config = config('services.webpush');
        if (blank($config['public_key']) || blank($config['private_key'])) {
            return;
        }

        try {
            $webPush = new WebPush(['VAPID' => [
                'subject' => $config['subject'],
                'publicKey' => $config['public_key'],
                'privateKey' => $config['private_key'],
            ]]);

            foreach ($user->pushSubscriptions()->where('is_active', true)->get() as $stored) {
                $subscription = Subscription::create([
                    'endpoint' => $stored->endpoint,
                    'publicKey' => $stored->public_key,
                    'authToken' => $stored->auth_token,
                    'contentEncoding' => $stored->content_encoding,
                ]);
                $report = $webPush->sendOneNotification($subscription, json_encode([
                    'title' => $title,
                    'body' => $message,
                    'url' => $url ?: route('notifications.index'),
                    'icon' => '/icons/app-icon.png',
                ], JSON_THROW_ON_ERROR));

                if ($report->isSubscriptionExpired()) {
                    $stored->update(['is_active' => false]);
                } elseif ($report->isSuccess()) {
                    $stored->update(['last_used_at' => now()]);
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Web Push gagal dikirim', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
        }
    }
}
