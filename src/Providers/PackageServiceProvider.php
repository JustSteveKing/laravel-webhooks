<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Providers;

use Illuminate\Support\ServiceProvider;
use JustSteveKing\Webhooks\Contracts\Signing\SigningContract;
use JustSteveKing\Webhooks\Signing\WebhookSigner;

final class PackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            paths: [
                __DIR__.'/../../config/webhooks.php' => config_path('webhooks.php'),
            ],
            groups: 'webhooks',
        );
    }

    public function register(): void
    {
        $this->app->singleton(
            abstract: SigningContract::class,
            concrete: fn () => new WebhookSigner(
                key: strval(config('webhooks.signing.key')),
            ),
        );
    }
}
