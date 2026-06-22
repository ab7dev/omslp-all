<?php
/*******************************************************************************
 * Copyright (c) 2018, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PUM_STP_Shortcode_ScrollTrigger
 *
 * Registers the pum_scroll_trigger shortcode.
 */
class PUM_STP_Shortcode_ScrollTrigger extends PUM_Shortcode {

	/**
	 * @var int
	 */
	public $version = 2;

	/**
	 *
	 */
	public function register() {
		// register old shortcode tag.
		add_shortcode( 'scroll_trigger', array( $this, 'handler' ) );
		add_shortcode( 'scroll_pop', array( $this, 'handler' ) );
		parent::register();
	}

	/**
	 * The shortcode tag.
	 *
	 * @return string
	 */
	public function tag() {
		return 'pum_scroll_trigger';
	}

	/**
	 * @return string
	 */
	public function label() {
		return __( 'Scroll Trigger Point', 'popup-maker-scroll-triggered-popups' );
	}

	/**
	 * @return string
	 */
	public function description() {
		return __( 'Inserts a hidden element to trigger a scroll popup.', 'popup-maker-scroll-triggered-popups' );
	}

	/**
	 * @return array
	 */
	public function post_types() {
		return array( 'post', 'page' );
	}

	/**
	 * @return array
	 */
	public function fields() {
		return array(
			'general' => array(
				'main' => array(
					'popup' => array(
						'label'     => __( 'Popup', 'popup-maker-scroll-triggered-popups' ),
						'desc'      => __( 'Which popup is this trigger point for?.', 'popup-maker-scroll-triggered-popups' ),
						'type'      => 'postselect',
						'post_type' => 'popup',
						'multiple'  => false,
						'as_array'  => false,
						'priority'  => 5,
						'required'  => true,
					),
				),
			),
		);
	}

	/**
	 * Process shortcode attributes.
	 *
	 * Also remaps and cleans old ones.
	 *
	 * @param $atts
	 *
	 * @return array
	 */
	public function shortcode_atts( $atts ) {
		$atts = parent::shortcode_atts( $atts );

		// Move old attributes into new keys.
		if ( ! empty( $atts['id'] ) && empty( $atts['popup'] ) ) {
			$atts['popup'] = $atts['id'];
			unset( $atts['id'] );
		}

		return $atts;
	}

	/**
	 * Shortcode handler
	 *
	 * @param  array  $atts    shortcode attributes
	 * @param  string $content shortcode content
	 *
	 * @return string
	 */
	public function handler( $atts, $content = null ) {
		$atts = $this->shortcode_atts( $atts );

		return '<span class="pum-stp-trigger pum-stp-trigger-' . $atts['popup'] . '"></span>';
	}

	/**
	 * @return bool|string
	 */
	public function template_styles() { ?>
			.pum-stp-trigger {
				border: 1px dotted #ccc;
				padding: 5px 10px;
				display: block;
			}

			.pum-stp-trigger i {
				display: inline-block;
				vertical-align: middle;
				line-height: 20px;
				width: 20px;
				height: 20px;
				text-align: center;
				margin: 0;
				padding: 0;
				background: url(<?php echo Popup_Maker::$URL . 'assets/images/admin/popup-maker-icon.png'; ?>) no-repeat center center transparent;
				background-size: contain;
			}
		<?php
	}


	/**
	 * @return bool|string
	 */
	public function template() { ?>
		<div class="pum-stp-trigger pum-stp-trigger-{{attrs.popup || attrs.id}}"><i></i> <small><?php printf( __( 'Scroll Trigger for Popup #%s', 'popup-maker-scroll-triggered-popups' ), "{{attrs.popup || attrs.id}}" ); ?><small>(hidden)</small></small></div><?php
	}

}

