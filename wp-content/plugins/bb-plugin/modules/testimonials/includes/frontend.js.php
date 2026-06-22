(function($) {
	$(function() {

		// Clear the controls in case they were already created.
		$('.fl-node-<?php echo $id; ?> .fl-slider-next').empty();
		$('.fl-node-<?php echo $id; ?> .fl-slider-prev').empty();

		// Create the slider and assign an instance to a variable.
		var testimonials = $('.fl-node-<?php echo $id; ?> .fl-testimonials').bxSlider({
			fallbackHTML: <?php echo ( 1 === $module->version ) ? 1 : 0; ?>,
			autoStart : <?php echo ( '1' === $settings->auto_play ) ? 1 : 0; ?>,
			auto : true,
			adaptiveHeight: true,
			ariaLive: false,
			pause : <?php echo esc_js( $settings->pause * 1000 ); ?>,
			mode : '<?php echo esc_js( $settings->transition ); ?>',
			autoDirection: '<?php echo esc_js( $settings->direction ); ?>',
			speed : <?php echo esc_js( $settings->speed * 1000 ); ?>,
			pager : <?php echo ( 'wide' == $settings->layout ) ? $settings->dots : 0; ?>,
			nextSelector : '.fl-node-<?php echo $id; ?> .fl-slider-next',
			prevSelector : '.fl-node-<?php echo $id; ?> .fl-slider-prev',
			nextText: '<i class="fas fa-chevron-circle-right"></i>',
			prevText: '<i class="fas fa-chevron-circle-left"></i>',
			controls : <?php echo ( 'compact' == $settings->layout ) ? $settings->arrows : 0; ?>,
			autoControls: <?php echo ( $settings->play_pause ) ? $settings->play_pause : 0; ?>,
			autoControlsCombine: <?php echo ( $settings->play_pause ) ? $settings->play_pause : 0; ?>,
			onSliderLoad: function(currentIndex) {
				$('.fl-node-<?php echo $id; ?> .fl-testimonials').addClass('fl-testimonials-loaded');
				$('.fl-node-<?php echo $id; ?> .fl-slider-next <?php echo ( 1 === $module->version ) ? 'a' : 'button'; ?>').attr('aria-label', '<?php _e( 'Next testimonial.', 'fl-builder' ); ?>' );
				$('.fl-node-<?php echo $id; ?> .fl-slider-prev <?php echo ( 1 === $module->version ) ? 'a' : 'button'; ?>').attr('aria-label', '<?php _e( 'Previous testimonial.', 'fl-builder' ); ?>' );
			},
			onSliderResize: function(currentIndex){
				this.working = false;
				this.reloadSlider();
			}
		});

		// Fix slider width not right when column is resized/deleted or when in responsive editing mode.
		if ( 'undefined' !== typeof( FLBuilder ) ) {
			var reloadTestimonials = function() {
				setTimeout( function(){
					testimonials.reloadSlider();
				}, 50 );
			}
			if ( ! FLBuilder.UIIFrame.isEnabled() ) {
				FLBuilder.addHook( 'responsive-editing-switched', reloadTestimonials );
			}
			FLBuilder.addHook( 'col-resize-drag', reloadTestimonials );
			FLBuilder.addHook( 'col-deleted', reloadTestimonials );
		}

	});
})(jQuery);