<?php defined( 'ABSPATH' ) or die( "You can't access this file directly." ); ?>
<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php if ( 'show' === $settings->label ) : ?>
		<label for="search-input-<?php echo $id; ?>" class="fl-search-form-label"><?php echo __( 'Search', 'fl-builder' ); ?></label>
	<?php endif; ?>
	<div class="fl-form-field">
		<input <?php echo $module->get_input_attributes(); ?> />
		<span id="search-error-<?php echo $id; ?>" class="sr-only">
			<?php
			if ( 'ajax' === $settings->result ) :
				echo __( 'Search field required with a minimum length of 3 characters', 'fl-builder' );
			else :
				echo __( 'Search field required', 'fl-builder' );
			endif;
			?>
		</span>
		<?php if ( 'ajax' == $settings->result ) : ?>
		<div class="fl-search-loader-wrap">
			<div class="fl-search-loader">
				<svg class="spinner" viewBox="0 0 50 50">
					<circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
				</svg>
			</div>
		</div>
		<?php endif; ?>
		<?php if ( 'ajax' == $settings->result && ( 'show' === $settings->label || 2 < $module->version ) ) : ?>
			<div class="fl-search-results-content" aria-live="polite"></div>
		<?php endif; ?>
	</div>
	<?php
	if ( 2 < $module->version && 'fullscreen' !== $settings->btn_action ) :
		$module->render_button( 'reveal' === $settings->btn_action );
	endif;
	?>
	<?php if ( 'ajax' == $settings->result && ( 'hide' === $settings->label && 3 > $module->version ) ) : ?>
	<div class="fl-search-results-content" aria-live="polite"></div>
	<?php endif; ?>
</form>
