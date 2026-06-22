<?php

FLBuilder::register_settings_form('node_template', array(
	'tabs' => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'general' => array(
					'title'  => '',
					'fields' => array(
						'name'       => array(
							'type'  => 'text',
							'label' => _x( 'Name', 'Template name.', 'fl-builder' ),
						),
						'type'       => array(
							'type'    => 'select',
							'label'   => _x( 'Type', 'The type of node template to save. Either Template or Component.', 'fl-builder' ),
							'help'    => __( 'Templates are reusable layouts. Globals can be used on multiple pages and edited in one place. Components are similar to globals, but with fields you can edit to override the settings per page.', 'fl-builder' ),
							'default' => 'template',
							'options' => array(
								'template' => __( 'Template', 'fl-builder' ),
								'global'   => __( 'Global', 'fl-builder' ),
								'dynamic'  => __( 'Component', 'fl-builder' ),
							),
						),
						'categories' => array(
							'type'        => 'select',
							'label'       => __( 'Category', 'fl-builder' ),
							'description' => __( 'Single or comma separated list of categories', 'fl-builder' ),
							'options'     => array(
								'' => __( 'Choose a Category', 'fl-builder' ),
							),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'featured'   => array(
							'type'    => 'photo',
							'label'   => __( 'Screenshot', 'fl-builder' ),
							'preview' => array(
								'type' => 'none',
							),
						),
						'notes'      => array(
							'label'   => __( 'Notes', 'fl-builder' ),
							'type'    => 'textarea',
							'rows'    => 6,
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
	),
));
