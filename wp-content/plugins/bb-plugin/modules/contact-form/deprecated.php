<?php

FLBuilder::register_module_deprecations( 'contact-form', [
	// Register module version (v1) to deprecate both the old HTML structure and the placeholder labels defaults.
	'v1' => [
		'defaults' => [
			'placeholder_labels' => 'placeholder',
		],
	],
] );
