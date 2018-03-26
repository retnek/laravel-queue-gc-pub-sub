<?php

namespace Websight\GcsProvider\Tests;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Laravel\Lumen\Application;
use Illuminate\Config\Repository;
use Psr\Log\NullLogger;
use thecubicle\GCPubSub\GCPubSubConnector;
use thecubicle\GCPubSub\GCPubSubJob;
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
        $connector = new GCPubSubConnector();
        $queue = $connector->connect([
            'projectId' => $_ENV['projectId'],
            'keyFilePath' => __DIR__ . '/../' . $_ENV['keyFilePath'],
            'defaultTopic' => $_ENV['defaultTopic'],
            'defaultSubscription' => $_ENV['defaultSubscription'],
            'default_ttl' => 0
        ]);
        $queue->setContainer($this->createDummyContainer());

        $queue->pushRaw('anydata');

        $job = $queue->pop();
        $this->assertInstanceOf(GCPubSubJob::class, $job);

        $job->delete();
    }

    protected function createDummyContainer()
    {
        $container = new Container();
        $container['log'] = new NullLogger();

        return $container;
    }
}
