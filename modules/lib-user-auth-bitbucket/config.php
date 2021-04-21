<?php

return [
    '__name' => 'lib-user-auth-bitbucket',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/lib-user-auth-bitbucket.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'https://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-user-auth-bitbucket' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'lib-user' => NULL
            ],
            [
                'lib-curl' => NULL
            ],
            [
                'lib-model' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'LibUserAuthBitBucket\\Model' => [
                'type' => 'file',
                'base' => 'modules/lib-user-auth-bitbucket/model'
            ],
            'LibUserAuthBitBucket\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-user-auth-bitbucket/library'
            ]
        ],
        'files' => []
    ],
    '__inject' => [
        [
            'name' => 'libUserAuthBitBucket',
            'children' => [
                [
                    'name' => 'client',
                    'children' => [
                        [
                            'name' => 'id',
                            'question' => 'Bitbucket client id',
                            'rule' => '!^.+$!'
                        ],
                        [
                            'name' => 'secret',
                            'question' => 'Bitbucket client secret',
                            'rule' => '!^.+$!'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libUserAuthBitBucket' => [
        'api' => [
            'host' => 'https://api.bitbucket.org'
        ],
        'client' => [
            'host' => 'https://bitbucket.org'
        ]
    ]
];
