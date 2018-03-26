<?php

namespace thecubicle\GCPubSub;

use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class GCPubSubQueue extends Queue implements QueueContract
{
    /**
     * The Google PubSub Instance
     *
     * @var \Google\Cloud\PubSub\PubSubClient;
     */
    protected $pubSub;

    /**
     * The Google PubSub Topic
     *
     * @var string;
     */
    protected $defaultTopic;

    protected $defaultSubscription;

    protected $defaultTTL;

    /**
     * GCPubSubQueue constructor.
     *
     * @param \Google\Cloud\PubSub\PubSubClient $pubSub
     * @param $config
     */
    public function __construct(PubSubClient $pubSub, $config)
    {
        $this->pubSub = $pubSub;
        $this->defaultTopic = $config['default_topic'];
        $this->defaultSubscription = $config['default_subscription'];
        $this->defaultTTL = $config['default_ttl'];
    }

    public function size($queue = null)
    {
        // TODO: Implement size() method
        $subscription = $this->getSubscription($queue);
        return count($subscription->pull());
    }

    private function getSubscription($queue = null)
    {
        $topic = !empty($queue) ? $queue : $this->defaultTopic;
        return $this->pubSub->subscription($this->defaultSubscription, $topic);
    }

    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $topic = $this->pubSub->topic(!empty($queue) ? $queue : $this->defaultTopic);
        $response = $topic->publish([
            'data' => base64_encode($payload),
            'attributes' => null
        ]);

        return $response['messageIds'][0];
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @param  mixed $data
     * @param  string $queue
     * @return string
     */
    protected function createPayload($job, $data = '', $queue = null)
    {
        $payload = parent::createPayload($job, $data);
        return $payload = $this->setMeta($payload, 'attempts', 1);
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $subscription = $this->getSubscription($queue);

        $pullOptions = [
            'returnImmediately' => true,
            'maxMessages' => 1
        ];

        $messages = $subscription->pull($pullOptions);

        if (count($messages) > 0) {
            $subscription->modifyAckDeadline($messages[0], $this->defaultTTL);
            return new GCPubSubJob(
                $this->container,
                $this->pubSub,
                !empty($queue) ? $queue : $this->defaultTopic,
                $this->defaultSubscription,
                $messages[0]
            );
        }

        return null;
    }
}
