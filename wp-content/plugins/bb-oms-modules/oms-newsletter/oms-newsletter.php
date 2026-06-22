<?php
class OMSNewsletterClass extends FLBuilderModule {

  public function __construct()
  {
    parent::__construct(array(
      'name'            => __( 'Newsletter', 'fl-builder' ),
      'description'     => __( 'A configuration wrapper for Mailster newsletter forms on LPs.', 'fl-builder' ),
      'group'           => __( 'OMS Modules', 'fl-builder' ),
      'category'        => __( 'OMS Modules', 'fl-builder' ),
      'dir'             => OMS_MODULES_DIR . 'oms-newsletter/',
      'url'             => OMS_MODULES_URL . 'oms-newsletter/',
      'icon'            => 'button.svg',
      'editor_export'   => true, // Defaults to true and can be omitted.
      'enabled'         => true, // Defaults to true and can be omitted.
      'partial_refresh' => false, // Defaults to false and can be omitted.
    ));
  }
}

FLBuilder::register_module( 'OMSNewsletterClass', array(
  'general'      => array(
    'title'         => __( 'General', 'fl-builder' ),
    'sections'      => array(
      'form'  => array(
        'title'         => __( 'Form', 'fl-builder' ),
        'fields'        => array(
          'form_id'     => array(
            'type'          => 'text',
            'label'         => __( 'Mailster Form ID', 'fl-builder' ),
            'help'          => __( 'Klavier Gratiskurs (#1)
Gitarre Gratiskurs (#2)
Bass Gratiskurs (#3)
Homepage Signup (#4)
Schlagzeug Gratiskurs (#5)
Singen lernen Newsletter (#6)
E-Gitarre Gratiskurs (#7)
003 A-Gitarre GK (#8)
001 Klavier GK (#9)
002 Keyboard GK (#10)
003 A-Gitarre GK (#11)
006 Schlagzeug GK (#12)
004 E-Gitarre GK (#13)
000 Homepage Signup GK (#15)
005 Bass GK (#16)
007 Ukulele GK (#17)
001 Klavier Notenkurs (#18)
003 A-Gitarre GK (bottom) (#19)', 'fl-builder' ),
          )
        )
      ),
      'button'  => array(
        'title'         => __( 'Button', 'fl-builder' ),
        'fields'        => array(
          'btn_color' => array(
            'type'          => 'color',
            'label'         => __( 'Button Color', 'fl-builder' ),
            'default'       => '333333',
            'show_reset'    => true,
            'show_alpha'    => true,
            'preview' => array(
              'type'     => 'css',
              'selector' => 'input.submit-button',
              'property' => 'background-color',
            ),
          ),
          'width'        => array(
            'type'    => 'select',
            'label'   => __( 'Width', 'fl-builder' ),
            'default' => 'auto',
            'options' => array(
              'auto'   => _x( 'Auto', 'Width.', 'fl-builder' ),
              'full'   => __( 'Full Width', 'fl-builder' ),
              'custom' => __( 'Custom', 'fl-builder' ),
            ),
            'toggle'  => array(
              'auto'   => array(
                'fields' => array( 'align' ),
              ),
              'full'   => array(),
              'custom' => array(
                'fields' => array( 'align', 'custom_width' ),
              ),
            ),
          ),
          'custom_width' => array(
            'type'    => 'unit',
            'label'   => __( 'Custom Width', 'fl-builder' ),
            'default' => '200',
            'slider'  => array(
              'px' => array(
                'min'  => 0,
                'max'  => 1000,
                'step' => 10,
              ),
            ),
            'units'   => array(
              'px',
              'vw',
              '%',
            ),
            'preview' => array(
              'type'     => 'css',
              'selector' => 'input.submit-button',
              'property' => 'width',
            ),
          ),
          'align'        => array(
            'type'       => 'align',
            'label'      => __( 'Align', 'fl-builder' ),
            'default'    => 'left',
            'responsive' => true,
            'preview'    => array(
              'type'     => 'css',
              'selector' => 'input.submit-button',
              'property' => 'text-align',
            ),
          ),
          'padding'      => array(
            'type'       => 'dimension',
            'label'      => __( 'Padding', 'fl-builder' ),
            'responsive' => true,
            'slider'     => true,
            'units'      => array( 'px' ),
            'preview'    => array(
              'type'     => 'css',
              'selector' => 'input.submit-button',
              'property' => 'padding',
            ),
          ),
          'text_color'       => array(
            'type'        => 'color',
            'connections' => array( 'color' ),
            'label'       => __( 'Text Color', 'fl-builder' ),
            'default'     => '',
            'show_reset'  => true,
            'show_alpha'  => true,
            'preview'     => array(
              'type'      => 'css',
              'selector'  => 'input.submit-button',
              'property'  => 'color',
              'important' => true,
            ),
          ),
          'typography'       => array(
            'type'       => 'typography',
            'label'      => __( 'Typography', 'fl-builder' ),
            'responsive' => true,
            'preview'    => array(
              'type'     => 'css',
              'selector' => 'input.submit-button',
            ),
          ),
        )
      )
    )
  )
) );