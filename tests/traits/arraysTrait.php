<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\tests\traits;

trait arraysTrait
{
    /** @var array|array[]  */
    private array $jsonApiDocumentSimple = [
        'data' => [
            'type' => 'user',
            'id' => '1',
            'attributes' => [
                'name' => 'carlo'
            ]
        ]
    ];

    /** @var array  */
    private array $jsonApiDocumentComplete = [
        'data' => [
            'type' => 'article',
            'id' => '1',
            'attributes' => [
                'title' => 'Article Title'
            ],
            'meta' => [
                'metaOne' => 1,
                'metaTwo' => 2
            ],
            'links' => [
                'self' => 'https://article/1'
            ],
            'relationships' => [
                'author' => [
                    'links' => [
                        'self' => 'https://user/10'
                    ],
                    'data' => [
                        'type' => 'user',
                        'id' => '10'
                    ]
                ],
                'images' => [
                    'data' => [
                        ['type' => 'image', 'id' => '101'],
                        ['type' => 'image', 'id' => '102']
                    ]
                ]
            ]
        ],
        'included' => [
            [
                'type' => 'user',
                'id' => '10',
                'attributes' => [
                    'name' => 'Carlo'
                ],
                'meta' => [
                    'metaOne' => 1,
                    'metaTwo' => 2
                ],
                'links' => [
                    'self' => 'https://user/10'
                ],
                'relationships' => [
                    'avatar' => [
                        'data' => [
                            'type' => 'image', 'id' => '103'
                        ]
                    ]
                ]
            ],
            [
                'type' => 'image',
                'id' => '101',
                'attributes' => [
                    'url' => 'https://image/101.jpg'
                ],
                'meta' => [
                    'metaOne' => 1,
                    'metaTwo' => 2
                ],
                'links' => [
                    'self' => 'https://image/101'
                ]
            ],
            [
                'type' => 'image',
                'id' => '102',
                'attributes' => [
                    'url' => 'https://image/102.jpg'
                ],
                'meta' => [
                    'metaOne' => 1,
                    'metaTwo' => 2
                ],
                'links' => [
                    'self' => 'https://image/102'
                ]
            ],
            [
                'type' => 'image',
                'id' => '103',
                'attributes' => [
                    'url' => 'https://image/103.jpg'
                ],
                'meta' => [
                    'metaOne' => 1,
                    'metaTwo' => 2
                ],
                'links' => [
                    'self' => 'https://image/103'
                ]
            ]
        ]
    ];

    /** @var array  */
    protected array $objectUserWithRelationship = [
        'type' => 'user',
        'id' => '10',
        'attributes' => [
            'name' => 'Carlo'
        ],
        'meta' => [
            'metaOne' => 1,
            'metaTwo' => 2
        ],
        'links' => [
            'self' => 'https://user/10'
        ],
        'relationships' => [
            'avatar' => [
                'data' => [
                    'type' => 'image', 'id' => '103'
                ]
            ]
        ]
    ];

    /** @var array|string[]  */
    protected array $objectUserNonExisting = [
       'type' => 'nonExistingType',
       'id' => '0'
    ];
}