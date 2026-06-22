<?php

$testimonials_class = 'fl-testimonials-wrap ' . sanitize_html_class( $settings->layout );

if ( '' == $settings->heading && 'compact' == $settings->layout ) {
	$testimonials_class .= ' fl-testimonials-no-heading';
}

?>
<div class="<?php echo $testimonials_class; ?>">

	<?php if ( ( 'wide' != $settings->layout ) && ! empty( $settings->heading ) ) : ?>
		<h3 class="fl-testimonials-heading"><?php echo $settings->heading; ?></h3>
	<?php endif; ?>

	<?php if ( ( 'compact' == $settings->layout && $settings->arrows ) || ( 'wide' == $settings->layout && $settings->dots ) ) : ?>
		<div class="fl-slider-prev"></div>
		<div class="fl-slider-next"></div>
	<?php endif; ?>
	<<?php echo $module->get_tag( 'fl-testimonials' ); ?>>
		<?php

		for ( $i = 0; $i < count( $settings->testimonials ); $i++ ) :

			if ( ! is_object( $settings->testimonials[ $i ] ) ) {
				continue;
			}

			$testimonials = $settings->testimonials[ $i ];
			echo $module->render_item( $testimonials->testimonial );

			?>
		<?php endfor; ?>
	</<?php echo $module->get_tag(); ?>>
</div>
<?php
