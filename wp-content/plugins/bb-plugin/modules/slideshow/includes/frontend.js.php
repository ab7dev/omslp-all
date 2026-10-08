<?php

$source = $module->get_source();

if ( ! empty( $source ) ) :

	?>
(function($) {
	$(function() {
		YUI({'logExclude': { 'yui': true } }).use('fl-slideshow', function(Y){

			if( null === Y.one('.fl-node-<?php echo $id; ?> .fl-slideshow-container') ) {
				return;
			}

			var oldSlideshow = Y.one('.fl-node-<?php echo $id; ?> .fl-slideshow-container .fl-slideshow'),
				newSlideshow = new Y.FL.Slideshow({
					fallback: <?php echo ( 1 === $module->version ) ? 'true' : 'false'; ?>,
					autoPlay: <?php echo esc_js( $settings->auto_play ); ?>,
					<?php if ( 'url' == $settings->click_action ) : ?>
					clickAction: 'url',
					clickActionUrl: '<?php echo esc_js( $settings->click_action_url ); ?>',
					<?php endif; ?>
					color: '<?php echo esc_js( $settings->color ); ?>',
					<?php if ( $settings->crop ) : ?>
					crop: true,
					<?php endif; ?>
					height: <?php echo esc_js( $settings->height ); ?>,
					imageNavEnabled: <?php echo esc_js( $settings->image_nav ); ?>,
					likeButtonEnabled: <?php echo esc_js( $settings->facebook ); ?>,
					<?php if ( 'none' != $settings->nav_type ) : ?>
					navButtons: [<?php $module->get_nav_buttons(); ?>],
					navButtonsLeft: [<?php $module->get_nav_buttons_left(); ?>],
					navButtonsRight: [<?php $module->get_nav_buttons_right(); ?>],
					<?php endif; ?>
					<?php if ( $settings->nav_overlay ) : ?>
					navOverlay: true,
					<?php endif; ?>
					navPosition: '<?php echo esc_js( $settings->nav_position ); ?>',
					navType: '<?php echo esc_js( $settings->nav_type ); ?>',
					<?php if ( $settings->nav_overlay ) : ?>
					overlayHideDelay: <?php echo intval( $settings->overlay_hide_delay ) * 1000; ?>,
					overlayHideOnMousemove: <?php echo esc_js( $settings->overlay_hide ); ?>,
					<?php endif; ?>
					pinterestButtonEnabled: <?php echo esc_js( $settings->pinterest ); ?>,
					protect: <?php echo esc_js( $settings->protect ); ?>,
					randomize: <?php echo esc_js( $settings->randomize ); ?>,
					responsiveThreshold: 0,
					source: [{<?php echo $source; ?>}],
					speed: <?php echo intval( $settings->speed ) * 1000; ?>,
					tweetButtonEnabled: <?php echo esc_js( $settings->twitter ); ?>,
					thumbsImageHeight: <?php echo esc_js( $settings->thumbs_size ); ?>,
					thumbsImageWidth: <?php echo esc_js( $settings->thumbs_size ); ?>,
					transition: '<?php echo esc_js( $settings->transition ); ?>',
					transitionDuration: <?php echo esc_js( $settings->transitionDuration ); // @codingStandardsIgnoreLine ?>
				});

			if(oldSlideshow) {
				oldSlideshow.remove(true);
			}

			// Responsive height handling.
			if ( <?php echo $global_settings->responsive_enabled ? 'true' : 'false'; ?> ) {
				var height = <?php echo esc_js( $settings->height ); ?>;
				var heightLarge = <?php echo isset( $settings->height_large ) && '' !== $settings->height_large ? esc_js( $settings->height_large ) : 'null'; ?>;
				var heightMedium = <?php echo isset( $settings->height_medium ) && '' !== $settings->height_medium ? esc_js( $settings->height_medium ) : 'null'; ?>;
				var heightResponsive = <?php echo isset( $settings->height_responsive ) && '' !== $settings->height_responsive ? esc_js( $settings->height_responsive ) : 'null'; ?>;
				var largeBreakpoint = <?php echo $global_settings->large_breakpoint; ?>;
				var mediumBreakpoint = <?php echo $global_settings->medium_breakpoint; ?>;
				var responsiveBreakpoint = <?php echo $global_settings->responsive_breakpoint; ?>;

				function updateSlideshowHeight( shouldRender = true ) {
					var windowWidth = Y.one('body').get('winWidth');
					var newHeight = height;

					if ( windowWidth <= responsiveBreakpoint && heightResponsive !== null ) {
						newHeight = heightResponsive;
					} else if ( windowWidth <= mediumBreakpoint && heightMedium !== null ) {
						newHeight = heightMedium;
					} else if ( windowWidth <= largeBreakpoint && heightLarge !== null ) {
						newHeight = heightLarge;
					}

					if ( newHeight !== newSlideshow.get('height') ) {
						newSlideshow.set('height', newHeight);
						if ( shouldRender ) {
							newSlideshow.resize();
						}
					}
				}

				// Update height on window resize
				Y.one(window).on('resize', Y.bind(function() {
					updateSlideshowHeight( true );
				}, this));
				
				// Initial height update without rendering
				updateSlideshowHeight( false );
			}

			newSlideshow.render('.fl-node-<?php echo $id; ?> .fl-slideshow-container');

			Y.one('.fl-node-<?php echo $id; ?> .fl-slideshow-container').setStyle( 'height', 'auto' );
		});
	});
})(jQuery);
<?php endif; ?>
