<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'KeSearch Indexer for Shortcut Elements',
    'description' => 'Indexer for shortcut elements, can handle mask records as well',
    'category' => 'be',
    'version' => '1.1.0',
    'state' => 'stable',
    'author' => 'Henrik Ziegenhain',
    'author_email' => 'henrik@hziegenhain.de',
    'author_company' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.5.99',
            'ke_search' => '3.1.0-3.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
];

