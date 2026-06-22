<?php
/*******************************************************************************
 * Copyright (c) 2018, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class PUM_STP_Triggers
 */
class PUM_STP_Triggers {

	/**
	 *
	 */
	public static function init() {
		add_filter( 'pum_registered_triggers', array( __CLASS__, 'register_triggers' ) );
	}

	/**
	 * @param array $triggers
	 *
	 * @return array
	 */
	public static function register_triggers( $triggers = array() ) {
		$current_post_id = isset( $_GET['post'] ) ? $_GET['post'] : get_the_ID();

		return array_merge( $triggers, array(
			'scroll' => array(
				'name'            => __( 'Scroll', 'popup-maker-scroll-triggered-popups' ),
				'modal_title'     => __( 'Scroll Trigger Settings', 'popup-maker-scroll-triggered-popups' ),
				'settings_column' => sprintf( '<strong>%1$s</strong>: %2$s', __( 'Trigger', 'popup-maker-scroll-triggered-popups' ), '{{data.trigger_type.charAt(0).toUpperCase() + data.trigger_type.slice(1)}}' ),
				'fields'          => array(
					'general' => array(
						'trigger_type'      => array(
							'label'   => __( 'What type of scroll trigger do you want to use?', 'popup-maker-scroll-triggered-popups' ),
							'type'    => 'select',
							'std'     => 'distance',
							'options' => array(
								'distance' => __( "Distance", 'popup-maker-scroll-triggered-popups' ),
								'element'  => __( "Element", 'popup-maker-scroll-triggered-popups' ),
							),
						),
						'distance'          => array(
							'type'         => 'measure',
							'label'        => __( 'Distance', 'popup-maker-scroll-triggered-popups' ),
							'desc'         => __( 'Choose how far users scroll before popup opens.', 'popup-maker-scroll-triggered-popups' ),
							'std'          => '75%',
							'units'        => array(
								'px'  => 'px',
								'%'   => '%',
								'rem' => 'rem',
							),
							'dependencies' => array(
								'trigger_type' => 'distance',
							),
						),
						'element_point'     => array(
							'label'        => __( 'When should the popup trigger?', 'popup-maker-scroll-triggered-popups' ),
							'type'         => 'radio',
							'options'      => array(
								'e_top-s_bottom'    => __( 'When the element first comes on screen.', 'popup-maker-scroll-triggered-popups' ),
								'e_bottom-s_bottom' => __( 'When the element has been completely revealed.', 'popup-maker-scroll-triggered-popups' ),
								'e_top-s_top'       => __( 'When the element begins to scroll off screen.', 'popup-maker-scroll-triggered-popups' ),
								'e_bottom-s_top'    => __( 'When the element has completely scrolled off screen.', 'popup-maker-scroll-triggered-popups' ),
							),
							'std'          => 'e_top-s_bottom',
							'dependencies' => array(
								'trigger_type' => 'element',
							),
						),
						'element_type'      => array(
							'label'        => __( 'What type of element do you want to use as a trigger point?', 'popup-maker-scroll-triggered-popups' ),
							'type'         => 'select',
							'options'      => array(
								'shortcode'    => __( 'Shortcode', 'popup-maker-scroll-triggered-popups' ),
								'css_selector' => __( 'CSS Selector', 'popup-maker-scroll-triggered-popups' ),
							),
							'dependencies' => array(
								'trigger_type' => 'element',
							),
						),
						'element_selector'  => array(
							'label'        => __( 'Trigger Element Selector', 'popup-maker-scroll-triggered-popups' ),
							'desc'         => __( 'CSS / jQuery Selector that will be used as a trigger point.', 'popup-maker-scroll-triggered-popups' ),
							'dependencies' => array(
								'trigger_type' => 'element',
								'element_type' => 'css_selector',
							),
						),
						'element_shortcode' => array(
							'label'        => __( 'Use this shortcode:', 'popup-maker-scroll-triggered-popups' ),
							'content'      => '<pre><code>[pum_scroll_trigger popup="' . $current_post_id . '"]</code></pre>',
							'type'         => 'html',
							'dependencies' => array(
								'trigger_type' => 'element',
								'element_type' => 'shortcode',
							),
						),
						'close_on_up'       => array(
							'label' => __( 'Close When Scrolling Back Up', 'popup-maker-scroll-triggered-popups' ),
							'desc'  => __( 'Checking this will cause popup to close when scrolling up past the trigger point.', 'popup-maker-scroll-triggered-popups' ),
							'type'  => 'checkbox',
						),
					),
				),
			),
		) );
	}
}
