<?php

namespace thecubicle\GCPubSub;

use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Queue\Connectors\ConnectorInterface;

class GCPubSubConnector implements ConnectorInterface
{
    /**
     * @param array $config
     * @return GCPubSubQueue
     */
    public function connect(array $config)
    {
        return new GCPubSubQueue(
            new PubSubClient($config),
            $config
        );
    }
}
