<?php

/**
 * A module that adds a simple login form.
 *
 * @since 2.3
 */
class FLLoginFormModule extends FLBuilderModule {

	/**
	 * @since 1.5.2
	 * @return void
	 */
	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Login Form', 'fl-builder' ),
			'description'     => __( 'Allow users to login/out.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'editor-table.svg',
		));

		if ( ! is_user_logged_in() ) {
			add_action( 'wp_ajax_nopriv_fl_builder_login_form_submit', array( $this, 'login' ) );
		} else {
			add_action( 'wp_ajax_fl_builder_logout_form_submit', array( $this, 'logout' ) );
		}
	}

	/**
	 * Called via AJAX to submit the subscribe form.
	 *
	 * @since 1.5.2
	 * @return string The JSON encoded response.
	 */
	public function login() {
		//  error_log( print_r( $_POST, true ) );
		$name             = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false;
		$password         = isset( $_POST['password'] ) ? $_POST['password'] : false;
		$remember         = isset( $_POST['remember'] ) ? $_POST['remember'] : false;
		$post_id          = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
		$node_id          = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id      = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;
		$nonce            = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : false;
		$result           = array(
			'action'  => false,
			'error'   => false,
			'message' => false,
			'url'     => false,
		);

		if ( $name && $password && $node_id && $nonce ) {

			// Get the module settings.
			if ( $template_id ) {
				$post_id  = FLBuilderModel::get_node_template_post_id( $template_id );
				$data     = FLBuilderModel::get_layout_data( 'published', $post_id );
				$settings = $data[ $template_node_id ]->settings;
			} else {
				$module   = FLBuilderModel::get_module( $node_id );
				$settings = $module->settings;
			}

			if ( ! $result['error'] ) {
				$creds = array(
					'user_login'    => $name,
					'user_password' => $password,
					'remember'      => $remember,
				);

				if ( ! wp_verify_nonce( $nonce, 'fl-login-form' ) ) {
					wp_send_json_error();
				}

				$user = wp_signon( $creds, is_ssl() );

				if ( is_wp_error( $user ) ) {
					wp_send_json_error( $user->get_error_message() );
				}

				$args = array(
					'url' => ( 'url' === $settings->redirect_to ) ? ( empty( $settings->success_url ) ? 'current' : $settings->success_url ) : ( 'message' === $settings->redirect_to ? 'current' : $settings->redirect_to ),
				);

				do_action( 'fl_builder_login_form_submission_complete', $settings, $password, $name, $template_id, $post_id );

				wp_send_json_success( $args );
			}
		} else {
			wp_send_json_error( $result['error'] );
		}
	}

	public static function logout() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'fl-login-form' ) ) {
			wp_logout();
			$args = array(
				'url' => '',
			);
			wp_send_json_success( $args );
		}
	}

	/**
	 * Returns the form tag attributes.
	 *
	 * @since 2.10
	 * @method get_form_attributes
	 * @param string $type The form type.
	 * @return string
	 */
	public function get_form_attributes( $type ) {
		return join( ' ', array(
			'class="fl-login-form fl-login-form-' . sanitize_html_class( $this->settings->layout ) . ' fl-form fl-clearfix ' . $type . '"',
			isset( $this->template_id ) ? 'data-template-id="' . $this->template_id . '" data-template-node-id="' . $this->template_node_id . '"' : '',
		) );
	}

	/**
	 * Returns the input tag attributes.
	 *
	 * @since 2.10
	 * @method get_input_attributes
	 * @param string $type The input type.
	 * @return string
	 */
	public function get_input_attributes( $type ) {
		$label = ( isset( $this->settings->labels ) && 'show' === $this->settings->labels );
		return join( ' ', array(
			'type="' . ( 'name' === $type ? 'text' : $type ) . '"',
			'id="' . $type . '-' . $this->node . '"',
			'name="fl-login-form-' . $type . '"',
			'placeholder="' . esc_attr( $this->settings->{$type . '_field_text'} ) . '"',
			$label ? '' : 'aria-label="' . esc_attr( $this->settings->{$type . '_field_text'} ) . '"',
			'aria-describedby="' . $type . '-error-' . $this->node . '"',
			'required',
		) );
	}

	/**
	 * Returns the error message tag attributes.
	 *
	 * @since 2.10
	 * @method get_error_attributes
	 * @param string $type The error type.
	 * @return string
	 */
	public function get_error_attributes( $type ) {
		return join( ' ', array(
			'id="' . $type . '-error-' . $this->node . '"',
			'class="fl-form-error-message"',
			'role="alert"',
		) );
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_button_settings( $id, $submit = true ) {
		$settings = array(
			'width'        => 'full',
			'click_action' => 'button',
			'button_type'  => $submit ? 'submit' : 'button',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, $id ) ) {
				if ( 0 === strpos( $key, $id ) ) {
					$key              = str_replace( $id, '', $key );
					$settings[ $key ] = $value;
				}
			}
		}

		if ( empty( $settings->text ) ) {
			if ( $submit ) {
				$settings['label_text'] = __( 'Login', 'fl-builder' );
			} else {
				$settings['label_text'] = __( 'Logout', 'fl-builder' );
			}
		}

		return $settings;
	}

	/**
	 *  Returns the relevant deprecated version of the button module if the login form module is deprecated.
	 *  It returns null (current version) if the login form module is not deprecated.
	 *
	 * @since 2.10
	 * @method get_button_version
	 * @return int|null
	 */
	public function get_button_version() {
		switch ( $this->version ) {
			case 1:
				return 2;
			default:
				return null;
		}
	}

	/**
	 * Returns an array of settings used to render a icon module.
	 *
	 * @since 2.5
	 * @return array
	 */
	public function get_icon_settings( $id ) {

		$settings = array(
			'icon_position'   => $this->settings->icon_position,
			'exclude_wrapper' => true,
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, $id ) ) {
				$key              = str_replace( $id, '', $key );
				$settings[ $key ] = $value;
			}
		}
		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'FLLoginFormModule', array(
	'general'       => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'structure' => array(
				'title'  => __( 'Structure', 'fl-builder' ),
				'fields' => array(
					'layout'          => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'stacked',
						'toggle'  => array(
							'stacked' => array(
								'fields' => array( 'labels', 'remember', 'forget', 'remember_text', 'forget_position', 'forget_text' ),
							),
						),
						'set'     => array(
							'inline' => array( 'labels' => 'no' ),
						),
						'options' => array(
							'stacked' => __( 'Stacked', 'fl-builder' ),
							'inline'  => __( 'Inline', 'fl-builder' ),
						),
					),
					'labels'          => array(
						'type'    => 'select',
						'label'   => __( 'Show Input Label', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'sections' => array( 'label_style' ),
							),
						),
					),
					'remember'        => array(
						'type'    => 'select',
						'label'   => __( 'Show Remember Login', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'remember_text' ),
							),
						),
					),
					'remember_text'   => array(
						'type'    => 'text',
						'label'   => __( 'Remember Me Text', 'fl-builder' ),
						'default' => __( 'Remember Me', 'fl-builder' ),
					),
					'forget'          => array(
						'type'    => 'select',
						'label'   => __( 'Show Forget Password Link', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'forget_position', 'forget_text' ),
							),
						),
					),
					'forget_position' => array(
						'type'    => 'select',
						'label'   => __( 'Forget Text Position', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Beside Remember Me', 'fl-builder' ),
							'below'   => __( 'Below Login Button', 'fl-builder' ),
						),
					),
					'forget_text'     => array(
						'type'    => 'text',
						'label'   => __( 'Forgotten Password Text', 'fl-builder' ),
						'default' => __( 'Forgotten Password', 'fl-builder' ),
					),
				),
			),
			'un_icon'   => array(
				'title'  => __( 'Name Field', 'fl-builder' ),
				'fields' => array(
					'name_field_text' => array(
						'type'    => 'text',
						'label'   => __( 'Name Field Text', 'fl-builder' ),
						'default' => __( 'Username', 'fl-builder' ),
					),
					'un_icon'         => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields'   => array( 'un_color' ),
							'sections' => array( 'icon' ),
						),
					),
					'un_color'        => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-form-name-wrap .fl-icon i',
							'property'  => 'color',
							'important' => true,
						),
					),
				),
			),
			'pw_icon'   => array(
				'title'  => __( 'Password Field', 'fl-builder' ),
				'fields' => array(
					'password_field_text' => array(
						'type'    => 'text',
						'label'   => __( 'Password Field Text', 'fl-builder' ),
						'default' => __( 'Password', 'fl-builder' ),
					),
					'pw_icon'             => array(
						'type'        => 'icon',
						'label'       => __( 'Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'pw_color' ),
						),
					),
					'pw_color'            => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '.fl-form-password-wrap .fl-icon i',
							'property'  => 'color',
							'important' => true,
						),
					),
				),
			),
			'icon'      => array(
				'title'  => __( 'Icon', 'fl-builder' ),
				'fields' => array(
					'icon_position' => array(
						'type'    => 'select',
						'label'   => __( 'Icon Position', 'fl-builder' ),
						'options' => array(
							'before' => __( 'Before', 'fl-builder' ),
							'after'  => __( 'After', 'fl-builder' ),
						),
					),
					'icon_size'     => array(
						'type'    => 'unit',
						'label'   => __( 'Size', 'fl-builder' ),
						'default' => '16',
						'units'   => array( 'px', 'em', 'rem' ),
						'slider'  => true,
					),
					'top_spacing'   => array(
						'type'    => 'unit',
						'label'   => __( 'Top Spacing', 'fl-builder' ),
						'slider'  => true,
						'units'   => array( 'px' ),
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-login-form .fl-form-field .fl-icon',
							'property' => 'top',
							'unit'     => 'px',
						),
					),
				),
			),
		),
	),
	'style'         => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'label_style'  => array(
				'title'  => 'Labels',
				'fields' => array(
					'label_padding'    => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-login-form-label:not(:has(input[type="checkbox"]))',
							'property' => 'padding',
						),
					),
					'label_color'      => array(
						'type'        => 'color',
						'label'       => __( 'Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node} .fl-login-form-label',
							'property' => 'color',
						),
					),
					'label_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-login-form-label',
						),
					),
				),
			),
			'input_style'  => array(
				'title'  => 'Inputs',
				'fields' => array(
					'input_padding'        => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-form-field input:not([type=checkbox])',
							'property' => 'padding',
						),
					),
					'input_color'          => array(
						'type'       => 'color',
						'label'      => __( 'Color', 'fl-builder' ),
						'show_reset' => true,
						'show_alpha' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-form-field input:not([type=checkbox]),{node} .fl-form-field input:not([type=checkbox])::placeholder',
							'property' => 'color',
						),
					),
					'input_typography'     => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-form-field input:not([type=checkbox])',
						),
					),
					'input_bg_color'       => array(
						'type'        => 'color',
						'label'       => __( 'Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '{node} .fl-form-field input:not([type=checkbox])',
							'property' => 'background-color',
						),
					),
					'input_bg_hover_color' => array(
						'type'        => 'color',
						'label'       => __( 'Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'connections' => array( 'color' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'input_border'         => array(
						'type'    => 'border',
						'label'   => __( 'Border', 'fl-builder' ),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-form-field input:not([type=checkbox])',
							'important' => true,
						),
					),
					'input_border_hover'   => array(
						'type'    => 'border',
						'label'   => __( 'Border Hover', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'button_style' => array(
				'title'  => 'Buttons',
				'fields' => array(
					'btn_padding'          => array(
						'type'       => 'dimension',
						'label'      => __( 'Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-button:is(a, button)',
							'property' => 'padding',
						),
					),
					'btn_text_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-button:is(a, button),{node} .fl-button:is(a, button) *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_text_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Text Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-button:is(a, button):hover,{node} .fl-button:is(a, button):hover *,{node} .fl-button:is(a, button):focus,{node} .fl-button:is(a, button):focus *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'btn_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '{node} .fl-button:is(a, button)',
						),
					),
				),
			),
		),
	),
	'login_button'  => array(
		'title'    => __( 'Login Button', 'fl-builder' ),
		'sections' => array(
			'btn_general' => array(
				'title'  => '',
				'fields' => array(
					'btn_text'        => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Login', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
					),
					'redirect_to'     => array(
						'type'    => 'select',
						'label'   => __( 'Redirect To', 'fl-builder' ),
						'default' => 'url',
						'options' => array(
							'url'      => __( 'URL', 'fl-builder' ),
							'current'  => __( 'Current URL', 'fl-builder' ),
							'referrer' => __( 'Referrer URL', 'fl-builder' ),
							'message'  => __( 'Show Message', 'fl-builder' ),
						),
						'toggle'  => array(
							'message' => array(
								'fields' => array( 'success_message' ),
							),
							'url'     => array(
								'fields' => array( 'success_url' ),
							),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'success_message' => array(
						'type'          => 'editor',
						'label'         => '',
						'media_buttons' => false,
						'rows'          => 1,
						'default'       => __( 'You are logged in successfully!', 'fl-builder' ),
						'preview'       => array(
							'type' => 'none',
						),
						'connections'   => array( 'string' ),
					),
					'success_url'     => array(
						'type'        => 'link',
						'label'       => __( 'Redirect URL', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'url' ),
					),
				),
			),
			'btn_icon'    => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'btn_icon'           => array(
						'type'        => 'icon',
						'label'       => __( 'Button Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'btn_icon_position', 'btn_icon_animation' ),
						),
					),
					'btn_icon_position'  => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
					'btn_icon_animation' => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Always Visible', 'fl-builder' ),
							'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
						),
					),
				),
			),
			'btn_style'   => array(
				'title'  => __( 'Button Style', 'fl-builder' ),
				'fields' => array(
					'btn_width' => array(
						'type'    => 'select',
						'label'   => __( 'Button Width', 'fl-builder' ),
						'default' => 'full',
						'options' => array(
							'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
							'full' => __( 'Full Width', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto' => array(
								'fields' => array( 'btn_align' ),
							),
						),
					),
					'btn_align' => array(
						'type'       => 'align',
						'label'      => __( 'Button Align', 'fl-builder' ),
						'default'    => 'left',
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-button-wrap',
							'property' => 'text-align',
						),
					),
				),
			),
			'btn_colors'  => array(
				'title'  => __( 'Button Background', 'fl-builder' ),
				'fields' => array(
					'btn_bg_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'btn_bg_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
			'btn_border'  => array(
				'title'  => __( 'Button Border', 'fl-builder' ),
				'fields' => array(
					'btn_border'             => array(
						'type'       => 'border',
						'label'      => __( 'Button Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-button:is(a, button)',
							'important' => true,
						),
					),
					'btn_border_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
	'logout_button' => array(
		'title'    => __( 'Logout Button', 'fl-builder' ),
		'sections' => array(
			'lo_btn_general' => array(
				'title'  => '',
				'fields' => array(
					'lo_btn_enabled' => array(
						'type'    => 'select',
						'label'   => __( 'Show Logout Button', 'fl-builder' ),
						'default' => 'yes',
						'preview' => 'none',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields'   => array( 'lo_btn_text', 'lo_success_url' ),
								'sections' => array( 'lo_btn_icon', 'lo_btn_border', 'lo_btn_style', 'lo_btn_colors' ),
							),
						),
					),
					'lo_btn_text'    => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Logout', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-button-text',
						),
					),
					'lo_success_url' => array(
						'type'        => 'link',
						'label'       => __( 'Redirect URL', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
						'connections' => array( 'url' ),
					),
				),
			),
			'lo_btn_icon'    => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'lo_btn_icon'           => array(
						'type'        => 'icon',
						'label'       => __( 'Button Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'lo_btn_icon_position', 'lo_btn_icon_animation' ),
						),
					),
					'lo_btn_icon_position'  => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
					'lo_btn_icon_animation' => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Always Visible', 'fl-builder' ),
							'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
						),
					),
				),
			),
			'lo_btn_style'   => array(
				'title'  => __( 'Button Style', 'fl-builder' ),
				'fields' => array(
					'lo_btn_width' => array(
						'type'    => 'select',
						'label'   => __( 'Button Width', 'fl-builder' ),
						'default' => 'full',
						'options' => array(
							'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
							'full' => __( 'Full Width', 'fl-builder' ),
						),
						'toggle'  => array(
							'auto' => array(
								'fields' => array( 'lo_btn_align' ),
							),
						),
					),
					'lo_btn_align' => array(
						'type'       => 'align',
						'label'      => __( 'Button Align', 'fl-builder' ),
						'default'    => 'left',
						'responsive' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
				),
			),
			'lo_btn_colors'  => array(
				'title'  => __( 'Button Background', 'fl-builder' ),
				'fields' => array(
					'lo_btn_bg_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'lo_btn_bg_color_hover' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
			'lo_btn_border'  => array(
				'title'  => __( 'Button Border', 'fl-builder' ),
				'fields' => array(
					'lo_btn_border'             => array(
						'type'       => 'border',
						'label'      => __( 'Button Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type' => 'none',
						),
					),
					'lo_btn_border_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
) );
