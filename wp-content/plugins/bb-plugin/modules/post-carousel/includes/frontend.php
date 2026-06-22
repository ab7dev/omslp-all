<?php

// Do the removal of paged & offset parameters
add_filter( 'fl_builder_loop_query_args', array( $module, 'override_query_args' ), 10 );

// Get the query data.
$query  = FLBuilderLoop::query( $settings );
$layout = isset( $settings->layout ) ? $settings->layout : 'grid';

// Remove filter to prevent breaking other modules
remove_filter( 'fl_builder_loop_query_args', array( $module, 'override_query_args' ), 10 );

// Render the posts.
if ( $query->have_posts() ) :

	?>

	<div class="fl-post-carousel fl-post-carousel-<?php echo sanitize_html_class( $layout ); ?>"<?php FLBuilder::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/Blog"' ); ?>>
		<?php if ( 'yes' == $settings->navigation && $query->have_posts() ) : ?>
		<div class="fl-post-carousel-navigation" aria-label="carousel buttons">
			<?php if ( 1 == $module->version ) : ?>
				<a class="carousel-prev" href="#" aria-label="previous" role="button"><div class="fl-post-carousel-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-left.svg'; ?></div></a>
				<a class="carousel-next" href="#" aria-label="next" role="button"><div class="fl-post-carousel-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-right.svg'; ?></div></a>
			<?php else : ?>
				<button class="carousel-prev fl-content-ui-button" aria-label="previous" type="button"><div class="fl-post-carousel-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-left.svg'; ?></div></button>
				<button class="carousel-next fl-content-ui-button" aria-label="next" type="button"><div class="fl-post-carousel-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-right.svg'; ?></div></button>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<<?php echo ( 1 == $module->version ) ? 'div' : 'ul'; ?> class="fl-post-carousel-wrapper">
			<?php

			while ( $query->have_posts() ) {

				$query->the_post();

				ob_start();

				include apply_filters( 'fl_builder_posts_carousel_layout_path', $module->dir . 'includes/post-' . $layout . '-loop.php', $settings->layout, $settings, $module );

				// Do shortcodes here so they are parsed in context of the current post.
				echo do_shortcode( ob_get_clean() );

			}

			?>
		</<?php echo ( 1 == $module->version ) ? 'div' : 'ul'; ?>>
	</div>

	<div class="fl-clear"></div>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
