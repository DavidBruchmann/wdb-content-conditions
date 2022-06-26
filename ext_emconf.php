<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "wdb_content_conditions"
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Content Conditions',
    'description' => 'This extension adds two different TypoScript conditions to check if content elements with special values on a page exist.',
    'category' => 'be',
    'author' => 'David Bruchmann',
    'author_email' => 'david.bruchmann@gmail.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.4',
    'autoload' => [
        'psr-4' => [
            'WDB\\WdbContentConditions\\' => 'Classes',
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.18-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
