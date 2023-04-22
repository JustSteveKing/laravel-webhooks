<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Channels;

use Illuminate\Http\Client\Response;
use Illuminate\Notifications\Notification;
use JustSteveKing\Webhooks\Builder\PendingWebhook;

final class WebhookChannel
{
    /**
     * @param Notification $notification
     * @return Response|null
     */
    public function send(mixed $notifiable, Notification $notification): null|Response
    {
        /**
         * @var PendingWebhook $webhook
         */
        $webhook = $notification->toWebhook($notifiable);

        return $webhook->send();
    }
}
