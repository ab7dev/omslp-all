<?php
$post_id     = get_the_ID();
$post_tag    = ( 1 == $module->version ) ? 'div' : 'li';
$post_schema = FLBuilder::print_schema( 'itemscope="itemscope" itemtype="' . FLPostGridModule::schema_itemtype() . '"', false );
?>
<<?php echo $post_tag; ?> <?php $module->render_post_class(); ?> <?php echo $post_schema; ?> > <?php // @codingStandardsIgnoreLine ?>
	<?php

		FLPostGridModule::schema_meta();

		// render featured images
	if ( isset( $settings->show_thumb ) && 'show' == $settings->show_thumb ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo $module->render_mobile_img( $post_id );
		}
		if ( has_post_thumbnail( $post_id ) ) {
			echo $module->render_img( $post_id );
		}
	}
	?>

	<div class="fl-post-slider-content">

		<?php $module->render_post_title( $post_id ); ?>

		<?php if ( $settings->show_author || $settings->show_date || $settings->show_comments ) : ?>
		<div class="fl-post-slider-feed-meta">
			<?php if ( $settings->show_author ) : ?>
				<span class="fl-post-slider-feed-author">
					<?php

					printf(
						/* translators: %s: Author name */
						_x( 'By %s', '%s stands for author name.', 'fl-builder' ),
						'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '"><span>' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
					);

					?>
				</span>
			<?php endif; ?>
			<?php if ( $settings->show_date ) : ?>
				<?php if ( 1 == $settings->show_author ) : ?>
					<span class="fl-sep"> | </span>
				<?php endif; ?>
				<span class="fl-post-slider-feed-date">
					<?php FLBuilderLoop::post_date( $settings->date_format ); ?>
				</span>
			<?php endif; ?>
			<?php if ( $settings->show_comments && comments_open() ) : ?>
				<?php if ( 1 == $settings->show_author || $settings->show_date ) : ?>
					<span class="fl-sep"> | </span>
				<?php endif; ?>
				<span class="fl-post-slider-feed-comments">
					<?php comments_popup_link( __( '0 Comments', 'fl-builder' ), __( '1 Comment', 'fl-builder' ), __( '% Comments', 'fl-builder' ) ); ?>
				</span>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( $settings->show_content || $settings->show_more_link ) : ?>
		<div class="fl-post-slider-feed-content swiper-no-swiping" itemprop="text">
			<?php
			if ( $settings->show_content ) {
				$module->render_excerpt();
			}
			?>
			<?php
			if ( $settings->show_more_link ) :
				$more_link_context = '<span class="sr-only"> about ' . the_title_attribute( array( 'echo' => false ) ) . '</span>';
				?>
				<a class="fl-post-slider-feed-more" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" aria-hidden="true" tabindex="-1"><?php echo $settings->more_link_text . $more_link_context; ?></a>
			<?php endif; ?>
		</div>
		<?php endif; ?>

	</div>

</<?php echo $post_tag; ?>>
