<?php
$EM_CONF['social_data_youtube'] = [
    'title'            => 'Social Data | Youtube Connector',
    'description'      => 'Connector for Youtube.',
    'category'         => 'fe',
    'state'            => 'stable',
    'uploadfolder'     => 0,
    'clearCacheOnLoad' => 0,
    'author'           => 'DACHCOM.DIGITAL AG Ben Walch',
    'author_company'   => 'DACHCOM.DIGITAL AG',
    'author_email'     => 'bwalch@dachcom.ch',
    'version'          => '1.0.0',
    'constraints'      => [
        'depends'   => [
            'typo3' => '11.4.0-11.9.99',
            'social_data' => '1.0.0-1.9.99'
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
];
