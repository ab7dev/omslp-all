<?php

FLBuilder::register_module_deprecations( 'accordion', [
	// Register module version (v1) to deprecate old HTML markup & label_tag default value.
	'v1' => [
		'defaults' => [
			'label_tag' => 'a',
		],
	],
	// Register module version (v2) to deprecate old HTML markup for restructuring.
	'v2' => [],
] );
