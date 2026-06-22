<?php

FLBuilder::register_module_deprecations( 'subscribe-form', [
	// Register module version (v1) to deprecate the old HTML markup and the default visibility of label tags.
	'v1' => [
		'defaults' => [
			'labels' => 'hide',
		],
	],
] );
