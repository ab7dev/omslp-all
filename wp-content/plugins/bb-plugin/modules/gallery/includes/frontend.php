<?php $container = 1 === $module->version ? 'div role="list"' : 'ul'; ?>
<?php if ( 'collage' == $settings->layout ) : ?>
<div class="fl-mosaicflow">
	<<?php echo $container; ?> class="fl-mosaicflow-content">
		<?php foreach ( $module->get_photos() as $photo ) : ?>
		<div class="fl-mosaicflow-item">
			<?php

			$url = 'none' == $settings->click_action ? '' : $photo->link;

			if ( 'lightbox' == $settings->click_action && isset( $settings->lightbox_image_size ) ) {
				if ( '' !== $settings->lightbox_image_size ) {
					$size = $settings->lightbox_image_size;
					$data = FLBuilderPhoto::get_attachment_data( $photo->id );
					if ( isset( $data->sizes->{$size} ) ) {
						$url = $data->sizes->{$size}->url;
					}
				}
			}

			FLBuilder::render_module_html('photo', array(
				'crop'         => false,
				'link_target'  => '_self',
				'link_type'    => 'none' == $settings->click_action ? '' : 'url',
				'link_url'     => $url,
				'photo'        => $photo,
				'photo_src'    => $photo->src,
				'show_caption' => $settings->show_captions,
			), $module->get_photo_version());

			?>
		</div>
		<?php endforeach; ?>
	</<?php echo esc_attr( $container ); ?>>
	<div class="fl-clear"></div>
</div>
<?php else : ?>
<<?php echo $container; ?> class="fl-gallery">
	<?php foreach ( $module->get_photos() as $photo ) : ?>
	<<?php echo ( 1 === $module->version ) ? 'div role="listitem"' : 'li'; ?> class="fl-gallery-item">
		<?php

		$url = 'none' == $settings->click_action ? '' : $photo->link;

		if ( 'lightbox' == $settings->click_action && isset( $settings->lightbox_image_size ) ) {
			if ( '' !== $settings->lightbox_image_size ) {
				$size = $settings->lightbox_image_size;
				$data = FLBuilderPhoto::get_attachment_data( $photo->id );
				if ( isset( $data->sizes->{$size} ) ) {
					$url = $data->sizes->{$size}->url;
				}
			}
		}

		FLBuilder::render_module_html('photo', array(
			'crop'         => false,
			'link_target'  => '_self',
			'link_type'    => 'none' == $settings->click_action ? '' : 'url',
			'link_url'     => $url,
			'photo'        => $photo,
			'photo_src'    => $photo->src,
			'show_caption' => $settings->show_captions,
		), $module->get_photo_version());

		?>
	</<?php echo ( 1 === $module->version ) ? 'div' : 'li'; ?>>
	<?php endforeach; ?>
</<?php echo esc_attr( $container ); ?>>
<?php endif; ?>
