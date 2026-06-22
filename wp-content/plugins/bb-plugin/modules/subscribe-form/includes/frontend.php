<?php

$form_tag              = 1 === $module->version ? 'div role="form"' : 'form';
$subscribe_form_fields = apply_filters( 'fl_builder_subscribe_form_fields', array(
	'form_name'   => 'fl-subscribe-form-name',
	'form_email'  => 'fl-subscribe-form-email',
	'name_error'  => 'name-error',
	'email_error' => 'email-error',
), $settings );

?>
<<?php echo $form_tag; ?> <?php echo $module->get_form_attributes(); ?>>
<?php wp_nonce_field( 'fl-subscribe-form-nonce', 'fl-subscribe-form-nonce' ); ?>
	<?php if ( 'show' == $settings->show_name ) : ?>
	<div class="fl-form-field">
		<label <?php echo $module->get_label_attributes( $subscribe_form_fields['form_name'] ); ?>><?php echo esc_attr( $settings->name_field_text ); ?></label>
		<input type="text" <?php echo $module->get_input_attributes( 'name', $subscribe_form_fields ); ?> />
		<div <?php echo $module->get_error_attributes( $subscribe_form_fields['name_error'] ); ?>><?php _e( 'Please enter your name.', 'fl-builder' ); ?></div>
	</div>
	<?php endif; ?>

	<div class="fl-form-field">
		<label <?php echo $module->get_label_attributes( $subscribe_form_fields['form_email'] ); ?>><?php echo esc_attr( $settings->email_field_text ); ?></label>
		<input type="email" <?php echo $module->get_input_attributes( 'email', $subscribe_form_fields ); ?> />
		<div <?php echo $module->get_error_attributes( $subscribe_form_fields['email_error'] ); ?>><?php _e( 'Please enter a valid email address.', 'fl-builder' ); ?></div>
	</div>

	<?php
	if ( isset( $settings->custom_field ) && ! empty( $settings->custom_field ) && $module->is_custom_field_support( $settings ) ) :
		foreach ( $settings->custom_field as $field ) {
			?>
			<div class="fl-form-field">
				<label <?php echo $module->get_label_attributes( $field ); ?>><?php echo $field; ?></label>
				<input type="text" <?php echo $module->get_input_attributes( 'custom', $field ); ?> data-custom-field="<?php echo $field; ?>" />
				<div <?php echo $module->get_error_attributes( $field ); ?>><?php _e( 'Please fill the missing field.', 'fl-builder' ); ?></div>
			</div>
			<?php
		}
	endif;
	?>

	<?php if ( 'stacked' == $settings->layout ) : ?>
		<?php if ( 'show' == $settings->terms_checkbox ) : ?>
			<div class="fl-form-field fl-terms-checkbox">
				<?php if ( isset( $settings->terms_text ) && ! empty( $settings->terms_text ) ) : ?>
					<div class="fl-terms-checkbox-text"><?php echo $settings->terms_text; ?></div>
				<?php endif; ?>
				<div class="fl-terms-checkbox-wrap">
					<input type="checkbox" <?php echo $module->get_checkbox_attributes(); ?> />
					<label for="fl-terms-checkbox-<?php echo $id; ?>" class="fl-subscribe-form-label"><?php echo $settings->terms_checkbox_text; ?></label>
				</div>
				<div <?php echo $module->get_error_attributes( 'terms-error' ); ?>><?php _e( 'You must accept the Terms and Conditions.', 'fl-builder' ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( 'show' == $settings->show_recaptcha && ( isset( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_site_key ) ) ) : ?>
		<div class="fl-form-field fl-form-recaptcha">
			<div class="fl-form-error-message" role="alert"><?php _e( 'Please check the captcha to verify you are not a robot.', 'fl-builder' ); ?></div>
			<div id="<?php echo $id; ?>-fl-grecaptcha" class="fl-grecaptcha"<?php $module->recaptcha_data_attributes(); ?>></div>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="fl-form-button" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
	<?php FLBuilder::render_module_html( 'button', $module->get_button_settings(), $module->get_button_version() ); ?>
	</div>

	<?php if ( 'inline' == $settings->layout ) : ?>
		<?php if ( 'show' == $settings->terms_checkbox ) : ?>
			<div class="fl-form-field fl-terms-checkbox">
				<?php if ( isset( $settings->terms_text ) && ! empty( $settings->terms_text ) ) : ?>
					<div class="fl-terms-checkbox-text"><?php echo $settings->terms_text; ?></div>
				<?php endif; ?>
				<div class="fl-terms-checkbox-wrap">
					<input type="checkbox" <?php echo $module->get_checkbox_attributes(); ?> />
					<label for="fl-terms-checkbox-<?php echo $id; ?>" class="fl-subscribe-form-label"><?php echo $settings->terms_checkbox_text; ?></label>
				</div>
				<div <?php echo $module->get_error_attributes( 'terms-error' ); ?>><?php _e( 'You must accept the Terms and Conditions.', 'fl-builder' ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( 'show' == $settings->show_recaptcha && ( isset( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_site_key ) ) ) : ?>
		<div class="fl-form-field fl-form-recaptcha">
			<div class="fl-form-error-message" role="alert"><?php _e( 'Please check the captcha to verify you are not a robot.', 'fl-builder' ); ?></div>
			<div id="<?php echo $id; ?>-fl-grecaptcha" class="fl-grecaptcha"<?php $module->recaptcha_data_attributes(); ?>></div>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="fl-form-success-message" role="alert"><?php echo $settings->success_message; ?></div>

	<div class="fl-form-error-message" role="alert"><?php _e( 'Something went wrong. Please check your entries and try again.', 'fl-builder' ); ?></div>

</<?php echo esc_attr( $form_tag ); ?>>
