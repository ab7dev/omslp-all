<?php 
$hw = $settings->header_weight;
$title = $settings->title;
/* bfi */  //$params = array( 'width' => 347, 'height' => 199 );

?>



<div class="header">
  <<?php echo $hw; ?> class="title title-md"><?php echo $title; ?></<?php echo $hw; ?>>
</div>

<div class="row categories_m">

  <?php for ( $i = 0; $i < count( $settings->items ); $i++ ) : if ( empty( $settings->items[ $i ] ) ) continue; ?>	
    <?php 
  	$cat_name = $settings->items[ $i ]->cat_name;
  	$cat_link = $settings->items[ $i ]->cat_link;
  	$cat_img = $settings->items[ $i ]->cat_img;
  	$img = wp_get_attachment_image_src($cat_img, 'full' );
  	//$img = bfi_thumb( $img[0] , $params );
  	?>
  
  
  <div class="col-sm-6 col-md-4">
    <a href="<?php echo $cat_link; ?>" style="background-image: url(<?php echo $img[0]; ?>);">
      <span><?php echo $cat_name; ?></span>
    </a>
  </div>
  
  
	<?php endfor; ?>
  
</div>


