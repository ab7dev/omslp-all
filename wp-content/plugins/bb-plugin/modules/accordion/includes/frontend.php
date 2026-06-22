<?php global $wp_embed; ?>
<?php
$label_tag     = 'a' === $settings->label_tag ? $settings->label_tag . ' role="heading" aria-level="2" tabindex="-1" ' : esc_attr( $settings->label_tag );
$icon_tag      = 1 === $module->version ? 'a role="button" tabindex="0"' : 'button type="button"';
$exclude_class = 1 === $module->version ? '' : 'fl-content-ui-button';
$module_class  = 'class="fl-accordion fl-accordion-' . sanitize_html_class( $settings->label_size ) . ( $settings->collapse ? ' fl-accordion-collapse' : '' ) . '"';
?>

<div <?php echo $module_class; ?> <?php echo ( ! $settings->collapse ) ? ' multiselectable="true"' : ''; ?>>
	<?php
	if ( 'content' == $settings->source ) {
		for ( $i = 0; $i < count( $settings->items ); $i++ ) {
			if ( ! is_object( $settings->items[ $i ] ) ) {
				continue;
			}

			$label_id    = 'fl-accordion-' . $module->node . '-label-' . $i;
			$icon_id     = 'id="fl-accordion-' . $module->node . '-icon-' . $i . '"';
			$content_id  = 'fl-accordion-' . $module->node . '-panel-' . $i;
			$settings_id = ( ! empty( $settings->id ) ) ? ' id="' . sanitize_html_class( $settings->id ) . '-' . $i . '"' : '';
			$item_opened = ( 0 === $i && '1' === $settings->open_first ) ? true : false;
			$item_class  = $item_opened ? 'fl-accordion-item-active' : '';
			$icon_active = $item_opened ? $settings->label_active_icon : $settings->label_icon;
			$icon_class  = 'class="fl-accordion-button-icon fl-accordion-button-icon-' . $settings->label_icon_position . ' ' . $exclude_class . '"';
			$icon_aria   = 'aria-expanded="' . ( $item_opened ? 'true' : 'false' ) . '" aria-controls="' . $content_id . '"';
			$expand_text = ( $i > 0 || ! $settings->open_first ) ? __( 'Expand', 'fl-builder' ) : __( 'Collapse', 'fl-builder' );
			$icon_markup = '<i class="fl-accordion-button-icon ' . $icon_active . '"><span class="sr-only">' . esc_attr( $expand_text ) . '</span></i>';
			?>
			<div class="fl-accordion-item <?php echo $item_class; ?>" <?php echo $settings_id; ?>>
				<div class="fl-accordion-button">

					<?php if ( 'left' === $settings->label_icon_position ) : ?>
					<<?php echo join( ' ', [ $icon_tag, $icon_id, $icon_class, $icon_aria ] ); ?>><?php echo $icon_markup; ?></<?php echo esc_attr( $icon_tag ); ?>>
					<?php endif; ?>

					<<?php echo $label_tag; ?> id="<?php echo $label_id; ?>" class="fl-accordion-button-label"><?php echo wp_kses_post( $settings->items[ $i ]->label ); ?></<?php echo esc_attr( $settings->label_tag ); ?>>

					<?php if ( 'right' === $settings->label_icon_position ) : ?>
						<<?php echo join( ' ', [ $icon_tag, $icon_id, $icon_class, $icon_aria ] ); ?>><?php echo $icon_markup; ?></<?php echo esc_attr( $icon_tag ); ?>>
					<?php endif; ?>

				</div>
				<div class="fl-accordion-content fl-clearfix" role="region" id="<?php echo $content_id; ?>" aria-labelledby="<?php echo $label_id; ?>" aria-hidden="<?php echo $item_opened ? 'false' : 'true'; ?>">
					<?php
					if ( 'none' === $settings->items[ $i ]->saved_layout ) {
						echo FLBuilderUtils::wpautop( $wp_embed->autoembed( $settings->items[ $i ]->content ), $module );
					} else {
						$post_id = $settings->items[ $i ]->{'saved_' . $settings->items[ $i ]->saved_layout};

						if ( ! empty( $post_id ) ) {
							$module->render_content( $post_id );
						}
					}
					?>
				</div>
			</div>
			<?php
		}
	} elseif ( 'post' == $settings->source ) {
		$settings->exclude_self = 'yes';
		$query                  = FLBuilderLoop::query( $settings );

		if ( $query->have_posts() ) {
			$i = 0;

			while ( $query->have_posts() ) {
				$query->the_post();

				$label_id    = 'fl-accordion-' . $module->node . '-title-' . $i;
				$icon_id     = 'id="fl-accordion-' . $module->node . '-icon-' . $i . '"';
				$content_id  = 'fl-accordion-' . $module->node . '-content-' . $i;
				$settings_id = ( ! empty( $settings->id ) ) ? ' id="' . sanitize_html_class( $settings->id ) . '-' . $i . '"' : '';
				$item_opened = ( 0 === $i && '1' === $settings->open_first ) ? true : false;
				$item_class  = $item_opened ? 'fl-accordion-item-active' : '';
				$icon_active = $item_opened ? $settings->label_active_icon : $settings->label_icon;
				$icon_class  = 'class="fl-accordion-button-icon fl-accordion-button-icon-' . $settings->label_icon_position . ' ' . $exclude_class . '"';
				$icon_aria   = 'aria-expanded="' . ( $item_opened ? 'true' : 'false' ) . '" aria-controls="' . $content_id . '"';
				$expand_text = ( $i > 0 || ! $settings->open_first ) ? __( 'Expand', 'fl-builder' ) : __( 'Collapse', 'fl-builder' );
				$icon_markup = '<i class="fl-accordion-button-icon ' . $icon_active . '"><span class="sr-only">' . esc_attr( $expand_text ) . '</span></i>';
				?>
				<div class="fl-accordion-item <?php echo $item_class; ?>" <?php echo $settings_id; ?>>
					<div class="fl-accordion-button">

						<?php if ( 'left' === $settings->label_icon_position ) : ?>
						<<?php echo join( ' ', [ $icon_tag, $icon_id, $icon_class, $icon_aria ] ); ?>><?php echo $icon_markup; ?></<?php echo esc_attr( $icon_tag ); ?>>
						<?php endif; ?>

						<<?php echo $label_tag; ?> id="<?php echo $label_id; ?>" class="fl-accordion-button-label"><?php the_title(); ?></<?php echo esc_attr( $settings->label_tag ); ?>>

						<?php if ( 'right' === $settings->label_icon_position ) : ?>
							<<?php echo join( ' ', [ $icon_tag, $icon_id, $icon_class, $icon_aria ] ); ?>><?php echo $icon_markup; ?></<?php echo esc_attr( $icon_tag ); ?>>
						<?php endif; ?>

					</div>
					<div class="fl-accordion-content fl-clearfix" role="region" id="<?php echo $content_id; ?>" aria-labelledby="<?php echo $label_id; ?>" aria-hidden="<?php echo $item_opened ? 'false' : 'true'; ?>">
					<?php

					$post_id = get_the_id();
					if ( ! empty( $settings->content_type ) && 'post_content' === $settings->content_type ) {
						$module->render_content( $post_id );
					} else {
						$module->render_excerpt( $post_id );
						$more_link_text = ( ! empty( $settings->more_link ) && 'show' === $settings->more_link ) ? $settings->more_link_text : '';
						$module->render_more_link( $post_id, $more_link_text );
					}

					?>
					</div>
				</div>
				<?php
				$i++;
			}
			wp_reset_postdata();
		}
	}
	?>
</div>
