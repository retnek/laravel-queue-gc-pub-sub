<?php

namespace thecubicle\GCPubSub;

use Illuminate\Support\ServiceProvider;

class GCPubSubServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/gcpubsub.php', 'queue.connections.gcpubsub');
    }

    /**
     * Register the application's event listeners.
     * Publish config file
     *
     * @return void
     */
    public function boot()
    {
        app('queue')->addConnector('gcpubsub', function () {
            return new GCPubSubConnector();
        });

        $this->publishes([
            __DIR__.'../../gcpusub.php' => app()->basePath() . '/config/gcpubsub.php'
        ]);
    }

    /**
     * Decides wheter the current app is lumen.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen');
    }
}
