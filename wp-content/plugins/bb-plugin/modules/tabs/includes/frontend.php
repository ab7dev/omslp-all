<?php

global $wp_embed;

if ( 'post' == $settings->source ) {
	$query = FLBuilderLoop::query( $settings );
}
?>

<div class="fl-tabs fl-tabs-<?php echo sanitize_html_class( $settings->layout ); ?> fl-clearfix">

	<<?php echo 1 === $module->version ? 'div' : 'ul'; ?> class="fl-tabs-labels fl-clearfix" role="tablist">
		<?php
		$active_tab     = intval( $settings->active_tab );
		$tabs_on_mobile = $settings->tabs_on_mobile;

		if ( $active_tab > count( $settings->items ) && 'post' !== $settings->source ) {
			$active_tab = 1;
		}
		?>
		<?php
		$control_tag = 1 === $module->version ? 'a role="button" tabindex="0"' : 'button type="button"';
		$tag_classes = 1 === $module->version ? 'fl-tabs-label' : 'fl-tabs-label fl-content-ui-button';
		if ( 'content' == $settings->source ) {
			for ( $i = 0; $i < count( $settings->items ); $i++ ) {
				if ( ! is_object( $settings->items[ $i ] ) ) {
					continue;
				}

				$tab_label_id = 'fl-tabs-' . $module->node . '-label-' . $i;
				$tab_panel_id = 'fl-tabs-' . $module->node . '-panel-' . $i;
				$tab_selected = ( ( $active_tab - 1 ) == $i ) ? 'true' : 'false';
				$tab_active   = ( ( $active_tab - 1 ) == $i ) ? ' fl-tab-active' : '';
				$tab_classes  = $tag_classes . $tab_active;
				$tab_aria     = 'role="tab" aria-selected="' . $tab_selected . '" aria-controls="' . $tab_panel_id . '"';
				$id_in_label  = apply_filters( 'fl_tabs_id_in_label', false, $settings, $i );

				if ( $id_in_label && ! empty( $settings->id ) ) {
					$tab_label_id = esc_attr( $settings->id ) . '-label-' . $i;
				}
				?>
				<?php
				if ( 1 !== $module->version ) :
					echo '<li role="presentation">';
				endif;
				?>
				<<?php echo $control_tag; ?> class="<?php echo $tab_classes; ?>" id="<?php echo $tab_label_id; ?>" data-index="<?php echo $i; ?>" <?php echo $tab_aria; ?>>
				<?php echo $settings->items[ $i ]->label; ?>
				</<?php echo esc_attr( $control_tag ); ?>>
				<?php
				if ( 1 !== $module->version ) {
					echo '</li>';
				}
				?>
				<?php
			}
		} elseif ( 'post' == $settings->source ) {
			if ( $query->have_posts() ) {
				$i = 0;

				while ( $query->have_posts() ) {
					$query->the_post();

					$tab_label_id = 'fl-tabs-' . $module->node . '-label-' . $i;
					$tab_panel_id = 'fl-tabs-' . $module->node . '-panel-' . $i;
					$tab_selected = ( ( $active_tab - 1 ) == $i ) ? 'true' : 'false';
					$tab_active   = ( ( $active_tab - 1 ) == $i ) ? ' fl-tab-active' : '';
					$tab_classes  = $tag_classes . $tab_active;
					$tab_aria     = 'role="tab" aria-selected="' . $tab_selected . '" aria-controls="' . $tab_panel_id . '"';
					$id_in_label  = apply_filters( 'fl_tabs_id_in_label', false, $settings, $i );

					if ( $id_in_label && ! empty( $settings->id ) ) {
						$tab_label_id = esc_attr( $settings->id ) . '-label-' . $i;
					}
					?>
					<?php
					if ( 1 !== $module->version ) :
						echo '<li role="presentation">';
					endif;
					?>
					<<?php echo $control_tag; ?> class="<?php echo $tab_classes; ?>" id="<?php echo $tab_label_id; ?>" data-index="<?php echo $i; ?>" <?php echo $tab_aria; ?>>
						<?php the_title(); ?>
					</<?php echo esc_attr( $control_tag ); ?>>
					<?php
					if ( 1 !== $module->version ) :
						echo '</li>';
					endif;
					?>
					<?php
					$i++;
				}
				wp_reset_postdata();
			}
		}
		?>

	</<?php echo 1 === $module->version ? 'div' : 'ul'; ?>>

	<div class="fl-tabs-panels fl-clearfix">
		<?php
		$tag_classes .= ' fl-tabs-panel-label';
		$control_tag  = 1 === $module->version ? 'div role="button" tabindex="0"' : 'button type="button"';
		if ( 'content' == $settings->source ) {
			for ( $i = 0; $i < count( $settings->items ); $i++ ) {
				$tab_label_id  = 'fl-tabs-' . $module->node . '-label-' . $i;
				$tab_panel_id  = 'fl-tabs-' . $module->node . '-panel-' . $i;
				$tab_selected  = ( ( $active_tab - 1 ) == $i ) ? 'true' : 'false';
				$tab_active    = ( ( $active_tab - 1 ) == $i ) ? ' fl-tab-active' : '';
				$tab_classes   = $tag_classes . $tab_active;
				$tab_aria      = 'role="tab" aria-selected="' . $tab_selected . '" aria-controls="' . $tab_panel_id . '"';
				$panel_classes = 'fl-tabs-panel-content fl-clearfix' . $tab_active;
				$panel_hidden  = ( ( $active_tab - 1 ) !== $i ) ? ' aria-hidden="true"' : '';
				$panel_aria    = 'role="tabpanel" aria-live="polite" aria-labelledby="' . $tab_label_id . '"' . $panel_hidden;

				if ( ! is_object( $settings->items[ $i ] ) ) {
					continue;
				}
				?>
				<div class="fl-tabs-panel"<?php echo ( ! empty( $settings->id ) ) ? ' id="' . sanitize_html_class( $settings->id ) . '-' . $i . '"' : ''; ?>>
					<<?php echo $control_tag; ?> class="<?php echo $tab_classes; ?>" data-index="<?php echo $i; ?>" <?php echo $tab_aria; ?>>
						<span><?php echo $settings->items[ $i ]->label; ?></span>
						<i class="fas<?php echo ( ( $active_tab - 1 ) !== $i || 'close-all' === $tabs_on_mobile ) ? ' fa-plus' : ''; ?>"></i>
					</<?php echo esc_attr( $control_tag ); ?>>
					<div class="<?php echo $panel_classes; ?>" id="<?php echo $tab_panel_id; ?>" data-index="<?php echo $i; ?>" <?php echo $panel_aria; ?>>
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
			if ( $query->have_posts() ) {
				$i = 0;

				while ( $query->have_posts() ) {
					$tab_label_id  = 'fl-tabs-' . $module->node . '-label-' . $i;
					$tab_panel_id  = 'fl-tabs-' . $module->node . '-panel-' . $i;
					$tab_selected  = ( ( $active_tab - 1 ) == $i ) ? 'true' : 'false';
					$tab_active    = ( ( $active_tab - 1 ) == $i ) ? ' fl-tab-active' : '';
					$tab_classes   = $tag_classes . $tab_active;
					$tab_aria      = 'role="tab" aria-selected="' . $tab_selected . '" aria-controls="' . $tab_panel_id . '"';
					$panel_classes = 'fl-tabs-panel-content fl-clearfix' . $tab_active;
					$panel_hidden  = ( ( $active_tab - 1 ) !== $i ) ? ' aria-hidden="true"' : '';
					$panel_aria    = 'role="tabpanel" aria-live="polite" aria-labelledby="' . $tab_label_id . '"' . $panel_hidden;
					$query->the_post();
					?>
					<div class="fl-tabs-panel"<?php echo ( ! empty( $settings->id ) ) ? ' id="' . sanitize_html_class( $settings->id ) . '-' . $i . '"' : ''; ?>>
						<<?php echo $control_tag; ?> class="<?php echo $tab_classes; ?>" data-index="<?php echo $i; ?>" <?php echo $tab_aria; ?>>
							<span><?php the_title(); ?></span>
							<i class="fas<?php echo ( ( $active_tab - 1 ) !== $i || 'close-all' === $tabs_on_mobile ) ? ' fa-plus' : ''; ?>"></i>
						</<?php echo esc_attr( $control_tag ); ?>>
						<div class="<?php echo $panel_classes; ?>" id="<?php echo $tab_panel_id; ?>" data-index="<?php echo $i; ?>" <?php echo $panel_aria; ?>>
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

</div>
