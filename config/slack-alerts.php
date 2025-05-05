<?php

return [
    /*
     * Whether the slack alerts are enabled.
     */
    'enabled' => env('SLACK_ALERT_ENABLED', true),

    /*
     * The webhook URLs that we'll use to send a message to Slack.
     */
    'webhook_urls' => [
        'default' => env('SLACK_ALERT_WEBHOOK'),
    ],

    /*
     * This job will send the message to Slack. You can extend this
     * job to set timeouts, retries, etc...
     */
    'job' => Spatie\SlackAlerts\Jobs\SendToSlackChannelJob::class,
    'queue' => env('SLACK_ALERT_QUEUE', 'default'),
];
