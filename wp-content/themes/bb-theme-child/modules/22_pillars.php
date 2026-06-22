<?php

$hw = $settings->header_weight;
if (!$hw) { $hw = 'p'; }
$title = $settings->title;

?>
 
<div class="pillars">
		
	<?php if ($title) { ?>
	    <<?php echo $hw; ?> class="title title-md"><?php echo $title; ?></<?php echo $hw; ?>>
	<?php } ?>

	
	<?php for ( $i = 0; $i < count( $settings->items ); $i++ ) : if ( empty( $settings->items[ $i ] ) ) continue; ?>
	
		<?php $title = $settings->items[ $i ]->title; ?>
		<?php $content = $settings->items[ $i ]->content; ?>
		<?php $imgID = $settings->items[ $i ]->img; ?>
		<?php
		$img_data = wp_get_attachment_image_src($imgID, 'partner' ); 
	    $params = array( 'width' => 264, 'height' => 320 );
//	    $img = bfi_thumb( $img[0] , $params );
	    $img = $img_data[0]
		?>
		
		<div class="pillar">
			
			<div class="content">
				<?php if ($title) { ?><p class="title title-sm"><?php echo $title; ?></p><?php } ?>
				<?php echo $content; ?>
			</div>
			
			<?php if ($img) { ?>
			<div class="pillar-img" style="background-image: url(<?php echo $img; ?>);"></div>
			<?php } ?>
			
		</div>
		
	<?php endfor; ?>
	
</div>
