<div <?php echo $module->get_wrapper_attributes(); ?>>
	<?php
	if ( 'content' == $settings->source ) :
		global $wp_embed;
		foreach ( $settings->items as $index => $item ) :
			if ( ! is_object( $item ) ) :
				return;
			endif;
			echo $module->build_item_structure( $index, $wp_embed );
		endforeach;
	elseif ( 'post' == $settings->source ) :
		$settings->exclude_self = 'yes';
		$query                  = FLBuilderLoop::query( $settings );
		if ( $query->have_posts() ) {
			$index = 0;
			while ( $query->have_posts() ) :
				$query->the_post();
				echo $module->build_item_structure( $index );
				$index++;
			endwhile;
			wp_reset_postdata();
		}
	endif;
	?>
</div>
