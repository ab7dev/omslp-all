<div class="fl-content-slider">
	<?php
	// Render the navigation.
	if ( $settings->arrows && count( $settings->slides ) > 0 ) :
		?>
		<div class="fl-content-slider-navigation" aria-label="content slider buttons">
			<?php if ( 1 === $module->version ) : ?>
				<a class="slider-prev" href="#" aria-label="previous" role="button"><div class="fl-content-slider-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-left.svg'; ?></div></a>
				<a class="slider-next" href="#" aria-label="next" role="button"><div class="fl-content-slider-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-right.svg'; ?></div></a>
			<?php else : ?>
				<button class="slider-prev fl-content-ui-button" aria-label="previous" type="button"><div class="fl-content-slider-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-left.svg'; ?></div></button>
				<button class="slider-next fl-content-ui-button" aria-label="next" type="button"><div class="fl-content-slider-svg-container"><?php include FL_BUILDER_DIR . 'img/svg/arrow-right.svg'; ?></div></button>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<<?php echo 1 === $module->version ? 'div' : 'ul'; ?> class="fl-content-slider-wrapper">
		<?php
		for ( $i = 0; $i < count( $settings->slides ); $i++ ) :

			if ( ! is_object( $settings->slides[ $i ] ) ) {
				continue;
			} else {
				$slide = $settings->slides[ $i ];
			}
			?>
		<<?php echo 1 === $module->version ? 'div' : 'li'; ?> class="fl-slide fl-slide-<?php echo $i; ?> fl-slide-text-<?php echo sanitize_html_class( $slide->text_position ); ?>">
			<?php

			// Mobile photo or video
			$module->render_mobile_media( $slide );

			// Background photo or video
			$module->render_background( $slide );

			?>
			<div class="fl-slide-foreground clearfix">
				<?php

				// Content
				$module->render_content( $slide, $i, $id );

				// Foreground photo or video
				$module->render_media( $slide );

				?>
			</div>
		</<?php echo 1 === $module->version ? 'div' : 'li'; ?>>
	<?php endfor; ?>
	</<?php echo 1 === $module->version ? 'div' : 'ul'; ?>>
	<div class="fl-clear"></div>
</div>
