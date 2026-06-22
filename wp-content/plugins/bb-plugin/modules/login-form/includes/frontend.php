<?php if ( ! is_user_logged_in() || FLBuilderModel::is_builder_active() ) : ?>
	<?php	$tag = 1 === $module->version ? 'div role="form"' : 'form'; ?>
	<<?php echo $tag; ?> <?php echo $module->get_form_attributes( 'login' ); ?>>
		<?php wp_nonce_field( 'fl-login-form', 'fl-login-form-nonce' ); ?>
		<div class="fl-form-field fl-form-name-wrap">
			<?php if ( 'yes' === $settings->labels ) : ?>
				<label for="name-<?php echo $id; ?>" class="fl-login-form-label"><?php echo esc_html( $settings->name_field_text ); ?></label>
			<?php endif; ?>
			<?php FLBuilder::render_module_html( 'icon', $module->get_icon_settings( 'un_' ) ); ?>
			<input <?php echo $module->get_input_attributes( 'name' ); ?> />
			<div <?php echo $module->get_error_attributes( 'name' ); ?>><?php _e( 'Please enter your username/email.', 'fl-builder' ); ?></div>
		</div>

		<div class="fl-form-field fl-form-password-wrap">
			<?php if ( 'yes' === $settings->labels ) : ?>
				<label for="password-<?php echo $id; ?>" class="fl-login-form-label"><?php echo esc_html( $settings->password_field_text ); ?></label>
			<?php endif; ?>
			<?php FLBuilder::render_module_html( 'icon', $module->get_icon_settings( 'pw_' ) ); ?>
			<input <?php echo $module->get_input_attributes( 'password' ); ?> />
			<div <?php echo $module->get_error_attributes( 'password' ); ?>><?php _e( 'Please enter your password.', 'fl-builder' ); ?></div>
		</div>

		<?php if ( 'stacked' === $settings->layout ) : ?>
			<?php if ( isset( $settings->forget ) && 'yes' === $settings->forget && 'default' === $settings->forget_position && ! empty( $settings->forget_text ) ) : ?>
			<div class="fl-input-field fl-remember-forget">
				<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="<?php echo esc_attr( $settings->forget_text ); ?>">
					<?php echo esc_html( $settings->forget_text ); ?>
				</a>
			</div>
			<?php endif; ?>

			<?php if ( isset( $settings->remember ) && 'yes' === $settings->remember && ! empty( $settings->remember_text ) ) : ?>
			<div class="fl-input-field fl-remember-checkbox">
				<label for="fl-login-checkbox-<?php echo $id; ?>" class="fl-login-form-label">
					<input id="fl-login-checkbox-<?php echo $id; ?>" type="checkbox" name="fl-login-form-remember" value="1" />
					<span class="fl-remember-checkbox-text"><?php echo esc_html( $settings->remember_text ); ?></span>
				</label>
			</div>
			<?php endif; ?>
			<div class="fl-form-button" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
				<?php FLBuilder::render_module_html( 'button', $module->get_button_settings( 'btn_' ), $module->get_button_version() ); ?>
			</div>

			<?php if ( isset( $settings->forget ) && 'yes' === $settings->forget && 'below' === $settings->forget_position && ! empty( $settings->forget_text ) ) : ?>
			<div class="fl-input-field fl-remember-forget fl-lost-password-below">
				<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="<?php echo esc_attr( $settings->forget_text ); ?>">
					<?php echo esc_html( $settings->forget_text ); ?>
				</a>
			</div>
			<?php endif; ?>

		<?php else : ?>
			<div class="fl-form-button" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
			<?php FLBuilder::render_module_html( 'button', $module->get_button_settings( 'btn_' ), $module->get_button_version() ); ?>
			</div>

		<?php endif; ?>

	<div class="fl-form-error-message" role="alert"><?php _e( 'Something went wrong. Please check your entries and try again.', 'fl-builder' ); ?></div>

	</<?php echo esc_attr( $tag ); ?>>
<?php else : ?>
	<div <?php echo $module->get_form_attributes( 'logout' ); ?>>
		<?php if ( 'yes' == $settings->lo_btn_enabled ) : ?>
			<?php wp_nonce_field( 'fl-login-form', 'fl-login-form-nonce' ); ?>
		<div class="fl-form-button log-out" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
			<?php FLBuilder::render_module_html( 'button', $module->get_button_settings( 'lo_btn_', false ), $module->get_button_version() ); ?>
		</div>
		<?php endif; ?>
		<?php if ( 'message' == $settings->redirect_to ) : ?>
			<span class="fl-success-msg" role="alert"><?php echo $settings->success_message; ?></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
