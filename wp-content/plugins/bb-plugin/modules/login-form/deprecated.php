<?php

FLBuilder::register_module_deprecations( 'login-form', [
	// Register module version (v1) to deprecate old HTML markup & default value for field labels.
	'v1' => [
		'defaults' => [
			'labels' => 'no',
		],
	],
] );
