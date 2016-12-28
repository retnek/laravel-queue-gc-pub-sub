<?php

return [
    'driver' => 'gcpubsub',
    'projectId' => getenv('GC_PROJECT_ID'),
    'default_topic' => getenv('GC_PUBSUB_TOPIC'),
    'default_subscription' => getenv('GC_PUBSUB_SUBSCRIPTION'),
    'keyFilePath' => getenv('GC_AUTH_JSON'),
    'default_ttl' => 100,
];