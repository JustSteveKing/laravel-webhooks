<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JustSteveKing\Webhooks\Builder\PendingWebhook;

final class DispatchWebhookRequest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly PendingWebhook $webhook,
    ) {
    }

    public function handle(): void
    {
        $this->webhook->send();
    }
}
