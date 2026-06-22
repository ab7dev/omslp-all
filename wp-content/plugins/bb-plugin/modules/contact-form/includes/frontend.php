<?php
global $post;
$contact_form_fields = apply_filters( 'fl_builder_contact_form_fields', array(
	'form_name'        => 'fl-name',
	'form_subject'     => 'fl-subject',
	'form_email'       => 'fl-email',
	'form_phone'       => 'fl-phone',
	'form_message'     => 'fl-message',
	'form_success_url' => 'fl-success-url',
	'name_error'       => 'name-error',
	'subject_error'    => 'subject-error',
	'email_error'      => 'email-error',
	'phone_error'      => 'phone-error',
	'message_error'    => 'message-error',
), $settings );
?>
<form <?php echo $module->get_form_attributes(); ?>>
	<?php wp_nonce_field( 'fl-contact-form-nonce', 'fl-contact-form-nonce' ); ?>
	<input type="hidden" name="fl-layout-id" value="<?php echo is_object( $post ) ? $post->ID : 0; ?>" />
	<?php if ( 'show' == $settings->name_toggle ) : ?>
	<div class="fl-input-group fl-name">
		<label <?php echo $module->get_label_attributes( $contact_form_fields['form_name'] ); ?>><?php echo esc_attr( $settings->name_placeholder ); ?></label>
		<span <?php echo $module->get_error_attributes( $contact_form_fields['name_error'] ); ?>><?php _e( 'Please enter your name.', 'fl-builder' ); ?></span>
		<input type="text" <?php echo $module->get_input_attributes( 'name', $contact_form_fields ); ?> />
	</div>
	<?php endif; ?>
	<?php if ( 'show' == $settings->subject_toggle ) : ?>
	<div class="fl-input-group fl-subject">
		<label <?php echo $module->get_label_attributes( $contact_form_fields['form_subject'] ); ?>><?php echo esc_attr( $settings->subject_placeholder ); ?></label>
		<span <?php echo $module->get_error_attributes( $contact_form_fields['subject_error'] ); ?>><?php _e( 'Please enter a subject.', 'fl-builder' ); ?></span>
		<input type="text" <?php echo $module->get_input_attributes( 'subject', $contact_form_fields ); ?> />
	</div>
	<?php endif; ?>
	<?php if ( 'show' == $settings->email_toggle ) : ?>
	<div class="fl-input-group fl-email">
		<label <?php echo $module->get_label_attributes( $contact_form_fields['form_email'] ); ?>><?php echo esc_attr( $settings->email_placeholder ); ?></label>
		<span <?php echo $module->get_error_attributes( $contact_form_fields['email_error'] ); ?>><?php _e( 'Please enter a valid email.', 'fl-builder' ); ?></span>
		<input type="email" <?php echo $module->get_input_attributes( 'email', $contact_form_fields ); ?> />
	</div>
	<?php endif; ?>
	<?php if ( 'show' == $settings->phone_toggle ) : ?>
	<div class="fl-input-group fl-phone">
		<label <?php echo $module->get_label_attributes( $contact_form_fields['form_phone'] ); ?>><?php echo esc_attr( $settings->phone_placeholder ); ?></label>
		<span <?php echo $module->get_error_attributes( $contact_form_fields['phone_error'] ); ?>><?php _e( 'Please enter a valid phone number.', 'fl-builder' ); ?></span>
		<input type="tel" <?php echo $module->get_input_attributes( 'phone', $contact_form_fields ); ?> />
	</div>
	<?php endif; ?>
	<div class="fl-input-group fl-message">
		<label <?php echo $module->get_label_attributes( $contact_form_fields['form_message'] ); ?>><?php echo esc_attr( $settings->message_placeholder ); ?></label>
		<span <?php echo $module->get_error_attributes( $contact_form_fields['message_error'] ); ?>><?php _e( 'Please enter a message.', 'fl-builder' ); ?></span>
		<textarea <?php echo $module->get_input_attributes( 'message', $contact_form_fields ); ?>></textarea>
	</div>
	<?php if ( 'show' == $settings->terms_checkbox ) : ?>
		<div class="fl-input-group fl-terms-checkbox">
			<?php if ( isset( $settings->terms_text ) && ! empty( $settings->terms_text ) ) : ?>
				<div class="fl-terms-checkbox-text"><?php echo $settings->terms_text; ?></div>
			<?php endif; ?>
			<label for="fl-terms-checkbox-<?php echo $id; ?>" class="fl-contact-form-label">
				<input type="checkbox" <?php echo $module->get_checkbox_attributes(); ?> /> <?php echo $settings->terms_checkbox_text; ?>
			</label>
			<span <?php echo $module->get_error_attributes( 'terms-error' ); ?>><?php _e( 'You must accept the Terms and Conditions.', 'fl-builder' ); ?></span>
		</div>
	<?php endif; ?>

	<?php
	if ( 'show' == $settings->recaptcha_toggle && ( isset( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_site_key ) ) ) :
		?>
	<div class="fl-input-group fl-recaptcha">
		<span class="fl-contact-error" role="alert"><?php _e( 'Please check the captcha to verify you are not a robot.', 'fl-builder' ); ?></span>
		<div id="<?php echo $id; ?>-fl-grecaptcha" class="fl-grecaptcha"<?php $module->recaptcha_data_attributes(); ?>></div>
	</div>
	<?php endif; ?>
	<?php FLBuilder::render_module_html( 'button', $module->get_button_settings(), $module->get_button_version() ); ?>
	<?php if ( 'redirect' == $settings->success_action ) : ?>
		<input type="hidden" value="<?php echo $settings->success_url; ?>" class="fl-success-url">
	<?php elseif ( 'none' == $settings->success_action ) : ?>
		<span class="fl-success-none" role="alert" style="display:none;"><?php _e( 'Message Sent!', 'fl-builder' ); ?></span>
	<?php endif; ?>

	<span class="fl-send-error" role="alert" style="display:none;"><?php _e( 'Message failed. Please try again.', 'fl-builder' ); ?></span>
</form>
<?php if ( 'show_message' == $settings->success_action ) : ?>
	<span class="fl-success-msg" role="alert" style="display:none;"><?php echo $settings->success_message; ?></span>
<?php endif; ?>
