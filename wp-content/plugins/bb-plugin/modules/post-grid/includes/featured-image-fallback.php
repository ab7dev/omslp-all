<div class="fl-post-<?php echo sanitize_html_class( $layout ); ?>-image">
	<?php
	/**
	 * Fires before the post featured image fallback is rendered.
	 * The dynamic portion of the hook name, `$layout`, is the current post layout (e.g. grid, feed, gallery).
	 *
	 * @since 2.2.5
	 * @param object $settings Module settings.
	 * @param object $module   The post grid module instance.
	 */
	do_action( 'fl_builder_post_' . $layout . '_before_image', $settings, $this );
	?>

	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" aria-hidden="true" tabindex="-1">
		<?php
		/**
		 * @since 2.2.5
		 * @see fl_render_featured_image_fallback
		 */
		$fallback_image = apply_filters( 'fl_render_featured_image_fallback', $settings->image_fallback, $settings );
		echo wp_get_attachment_image( $fallback_image, $settings->image_size, false, [ 'alt' => '' ] );
		?>
	</a>

	<?php
	/**
	 * Fires after the post featured image fallback is rendered.
	 * The dynamic portion of the hook name, `$layout`, is the current post layout (e.g. grid, feed, gallery).
	 *
	 * @since 2.2.5
	 * @param object $settings Module settings.
	 * @param object $module   The post grid module instance.
	 */
	do_action( 'fl_builder_post_' . $layout . '_after_image', $settings, $this );
	?>

</div>
