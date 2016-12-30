# laravel-queue-gc-pub-sub

Google Cloud PubSub for Laravel queues.

## Installation
1. Install using composer:

    `composer require thecubicle/laravel-queue-gc-pub-sub`

2. Add GCPubSubServiceProvider to `providers` array in `config/app.php`:

    `thecubicle\GCPubSub\GCPubSubServiceProvider::class,`
 
3. Create the necessary subscriptions and topics in PubSub using the Google CLI or web frontend.  Configure subscription and topics to the configuration below.

## Configuration
1. Add the keys below to `.env` and replace with the proper values:

    GC_PROJECT_ID=project-id
    GC_AUTH_JSON=path-to-creds-file
    GC_PUBSUB_TOPIC=topic-to-use-as-queue
    GC_PUBSUB_SUBSCRIPTION=subscription-name
    
    
## Queues / Topics
This component uses PubSub topics for queues.

## Using the PubSub emulator
To use the PubSub emulator add the key / value pair: `PUBSUB_EMULATOR_HOST=http://localhost:{port}` to `.env`. Replace {port} with the port the PubSub emulator is running on.

## Notes
Tested with Laravel 5.2
