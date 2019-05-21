<?php return [
    'apiVersion' => 'v3',
    'operations' => [
        'List' => [
            'httpMethod' => 'GET',
            'uri' => '/{ApiVersion}/feeds',
            'responseModel' => 'Result',
            'parameters' => [
                'ApiVersion' => [
                    'required' => true,
                    'type'     => 'string',
                    'location' => 'uri',
                ],
                'feedId' => [
                    'required' => false,
                    'type' => 'string',
                    'location' => 'query',
                ],
                'limit' => [
                    'required' => false,
                    'type' => 'integer',
                    'location' => 'query',
                    'maximum' => 50,
                ],
                'offset' => [
                    'required' => false,
                    'type' => 'integer',
                    'location' => 'query',
                ],
            ],
        ],
        'Get' => [
            'httpMethod' => 'GET',
            'uri' => '/{ApiVersion}/feeds/{feedId}',
            'responseModel' => 'Result',
            'parameters' => [
                'ApiVersion' => [
                    'required' => true,
                    'type'     => 'string',
                    'location' => 'uri',
                ],
                'feedId' => [
                    'required' => true,
                    'type' => 'string',
                    'location' => 'uri',
                ],
                'includeDetails' => [
                    'required' => false,
                    'type' => 'string',
                    'location' => 'query',
                ],
                'limit' => [
                    'required' => false,
                    'type' => 'integer',
                    'location' => 'query',
                    'maximum' => 1000,
                ],
                'offset' => [
                    'required' => false,
                    'type' => 'integer',
                    'location' => 'query',
                ],
            ],
        ],
        'Post' => [
            'httpMethod' => 'POST',
            'uri' => '/{ApiVersion}/feeds',
            'responseModel' => 'Result',
            'parameters' => [
                'ApiVersion' => [
                    'required' => true,
                    'type'     => 'string',
                    'location' => 'uri',
                ],
                'feedType' => [
                    'required' => false,
                    'type' => 'string',
                    'location' => 'query',
                    'default' => 'CONTENT_PRODUCT'
                ]
            ],
        ],
    ],
    'models' => [
        'Result' => [
            'type' => 'object',
            'properties' => [
                'statusCode' => ['location' => 'statusCode'],
            ],
            'additionalProperties' => [
                'location' => 'xml'
            ],
        ]
    ]

];