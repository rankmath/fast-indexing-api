<?php

use Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Rank_Math_Instant_Indexing',

    'finders' => [
        Finder::create()
            ->files()
            ->in('vendor')
            ->notName('/^composer\..*$/')
            ->notName('aliases.php'), // Exclude the Google aliases file
    ],

    'output-dir' => 'vendor-prefixed',

    'exclude-namespaces' => [],

    'exclude-classes' => [],

    'exclude-constants' => [],

    'exclude-files' => [
        // Explicitly exclude the aliases.php file to prevent conflicts
        'vendor/google/apiclient/src/aliases.php',
    ],

    'patchers' => [],
];
