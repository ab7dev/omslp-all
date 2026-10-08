<?php
FLBuilder::register_module_deprecations( 'search', [
	// Register module version (v1) to deprecate the old rendered photo module HTML markup.
	'v1' => [],
	// Register module version (v2) to default hiding input label & deprecate rendered button module position.
	'v2' => [
		'defaults' => [
			'label' => 'hide',
		],
	],
] );
