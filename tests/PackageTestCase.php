<?php

declare(strict_types=1);

namespace JustSteveKing\Webhooks\Tests;

use Illuminate\Foundation\Application;
use JustSteveKing\Webhooks\Factories\WebhookFactory;
use JustSteveKing\Webhooks\Providers\PackageServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class PackageTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->configure(
            app: $this->app,
        );
    }

    protected function configure(Application $app): void
    {
        $app['config']['webhooks.signing.header'] = 'Signature';
        $app['config']['webhooks.signing.key'] = '123456';
        $app['config']['webhooks.user_agent.name'] = 'Laravel_Webhooks_Test';
        $app['config']['webhooks.request.timeout'] = 15;
    }

    /**
     * @param Application $app
     * @return array<int,class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     * @return array<string,class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Webhook' => WebhookFactory::class,
        ];
    }
}
