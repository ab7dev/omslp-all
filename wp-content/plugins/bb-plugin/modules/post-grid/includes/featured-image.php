<div class="fl-post-<?php echo sanitize_html_class( $layout ); ?>-image">

	<?php do_action( 'fl_builder_post_' . $layout . '_before_image', $settings, $this ); ?>

	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" aria-hidden="true" tabindex="-1">
		<?php the_post_thumbnail( $settings->image_size, [ 'alt' => '' ] ); ?>
	</a>

	<?php do_action( 'fl_builder_post_' . $layout . '_after_image', $settings, $this ); ?>

</div>
