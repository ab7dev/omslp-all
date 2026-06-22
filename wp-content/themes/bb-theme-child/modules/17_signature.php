<?php 
$img_1_lib = wp_get_attachment_image_src( $settings->img, "full" );
$img = $img_1_lib[0];
if (!$img_1_lib) { $img = WP_PLUGIN_URL . '/bb-modules/17_signature/img/csm_Philip_rund_Email_Abbinder_Signatur.jpg'; }

$text1 = $settings->text1;
$text2 = $settings->text2;
?>

<div class="row">
	<div class="col-sm-12 col-md-6 col-lg-4">
		<img src="<?php echo $img; ?>" alt="Unterschrift"/>
	</div>
	
	<div class="col-sm-12 col-md-6 col-lg-8">
	  
	  <p class="title"><?php echo $text1; ?></p>
	  <p class=""><?php echo $text2; ?></p>
	  
	</div>
</div>