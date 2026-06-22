<?php 
$hw = $settings->header_weight;
$title = $settings->title;
if (!$title || $title == '') { $title = get_the_title(); }

if (is_archive()) {
$title = the_archive_title( '<h1 class="page-title">', '</h1>' );
}

$tel_sel = $settings->tel_sel;
$tel_num = $settings->tel_num;
if ($tel_num == '') { $tel_num = '+49 8141 3474274'; }



$bg_settings = $settings->bg_toggle;
if ($bg_settings == 'module') {
	$img_default = WP_PLUGIN_URL . '/bb-modules/39_page_header/img/page_header.jpg';
	$img = wp_get_attachment_image_src( $settings->img_0_lib, "full" );
	if ($img == '') { $img = $img_default; } else { $img = $img[0]; }
}
?>

<div class="page_header"<?php if ($img) { echo ' style="background-image: url(' . $img . ');"'; } ?>>
  <div class="container">
  	<div class="v-align">
  	
      <<?php echo $hw; ?> class="title title-md<?php if ($tel_sel == 'show') { ?> pr<?php } ?>"><?php echo $title; ?></<?php echo $hw; ?>>
      
        <?php
      		if ( function_exists('yoast_breadcrumb') ) {
      		yoast_breadcrumb('
      		<p id="breadcrumbs">','</p>
      		');
      		}
      	?>
  	
 	 </div>
 	 <?php if ($tel_sel == 'show') { ?> 
 	 <div class="phone_container">
 	     <img src="<?php echo WP_PLUGIN_URL ?>/bb-modules/39_page_header/img/telephone.png" srcset="<?php echo WP_PLUGIN_URL ?>/bb-modules/39_page_header/img/telephone.png 1x, <?php echo WP_PLUGIN_URL ?>/bb-modules/39_page_header/img/telephone@2x.png 2x"/>
 	     <?php echo $tel_num; ?>
      </div>
 	 
 	 <?php } ?>
  </div>
</div>