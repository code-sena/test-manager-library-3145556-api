<?php

return [
    'default' => 'local',

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'covers' => [
            'driver' => 'local',
            'root' => storage_path('covers'),
        ],
    ],
];
