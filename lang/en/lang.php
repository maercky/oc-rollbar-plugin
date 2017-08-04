<?php

return [
    'plugin' => [
        'name' => 'Rollbar',
        'description' => 'Rollbar support for October CMS',
    ],
    'tab' => [
        'name' => 'Rollbar'
    ],
    'fields' => [
        'rollbar_enabled' => [
            'label' => 'Enable Rollbar',
        ],
        'rollbar_access_token' => [
            'label' => 'Rollbar Access Token',
            'comment' => 'Be sure to enter your POST_SERVER_ITEM_ACCESS_TOKEN with your project\'s post_server_item access token, which you can find in the Rollbar.com interface.'
        ],
        'rollbar_environment' => [
            'label' => 'Rollbar Environment',
            'comment' => 'Optional environment name. Any string will do.'
        ],
    ]
];
