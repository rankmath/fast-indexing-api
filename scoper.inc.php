<?php

use Symfony\Component\Finder\Finder;

return [
	'prefix' => 'Rank_Math_Instant_Indexing',

	'finders' => [
		Finder::create()
			->files()
			->in('vendor')
			->notName('/^composer\..*$/')
			->notName('aliases.php'),
	],

	'output-dir' => 'vendor-prefixed',

	'exclude-namespaces' => [],

	'exclude-classes' => [],

	'exclude-constants' => [],

	'exclude-files' => [
		'vendor/google/apiclient/src/aliases.php',
	],

	'patchers' => [],
];
