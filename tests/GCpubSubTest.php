<?php

namespace Websight\GcsProvider\Tests;

use PHPUnit\Framework\TestCase;
use Laravel\Lumen\Application;
use Illuminate\Config\Repository;
use thecubicle\GCPubSub\GCPubSubServiceProvider;

class GCpubSubTest extends TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var GCPubSubServiceProvider
     */
    protected $provider;

    public function setUp()
    {
        if (!class_exists(Application::class)) {
            $this->markTestSkipped();
        }

        $this->app = $this->setupApplication();
        $this->provider = $this->setupServiceProvider($this->app);

        parent::setUp();
    }

    protected function setupApplication()
    {
        $app = new Application(sys_get_temp_dir());
        $app->instance('config', new Repository());

        return $app;
    }

    protected function setupServiceProvider(Application $app)
    {
        /** @var \Illuminate\Contracts\Foundation\Application $app */
        $provider = new GCPubSubServiceProvider($app);
        $app->register($provider);
        $provider->boot();

        return $provider;
    }

    public function testQueuePush()
    {

    }
}