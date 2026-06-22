<?php 

/*img0*/
//$img_1_default = WP_PLUGIN_URL . '/bb-modules/04_newsletter/img/imac.jpg';

$select_img = $settings->select_img;

$img_envel_d = WP_PLUGIN_URL . '/bb-modules/04_newsletter/img/mail.png';
$img_envelope = $settings->img_envelope;
$img_envelope = wp_get_attachment_image_src($img_envelope, 'full' );
if (!$img_envelope) { $img_envelope = $img_envel_d; } else { $img_envelope = $img_envelope[0]; }

$img_1_lib = wp_get_attachment_image_src( $settings->img_1_lib, "full" );
$img_1_url = $settings->img_1_url;
if ($select_img == 'lib') { $img_1 = $img_1_lib[0]; } else { $img_1 = $img_1_url; }

$name = $settings->name;
$text = $settings->text;

$form_id = $settings->form_id;

//$params = array( 'width' => 570, 'height' => 340 );
//$img_1 = bfi_thumb( $img_1, $params );

$poster_url = $settings->poster_url;
//$params = array( 'width' => 570, 'height' => 320 );
//$poster_url = bfi_thumb( $poster_url, $params );
	
$select = $settings->select;
$video_type = $settings->video_type;
$vid_data = $module->get_data();
$preload  = FLBuilderModel::is_builder_active() ? ' preload="none"' : '';
?>


<div class="newsletter row">
  
  <div class="col-1 col-xs-12 col-sm-12 col-lg-9 eh">
    <img src="<?php echo WP_PLUGIN_URL ?>/bb-modules/04_newsletter/img/imac.jpg" alt="Screen">
    
    <div class="screen">
      
      <?php if ($select == 'image') { ?>
  			  
        <?php if ($img_1) { ?>
          <img src="<?php echo $img_1; ?>" alt="<?php echo $name; ?>">
        <?php } ?>
        
      <?php } elseif ($select == 'video') { ?>
        
        <?php
  			if ($video_type == 'media_library') {
  				
  			  echo '[video width="100%" height="55%" ' . $vid_data->extension . '="' . $vid_data->url . '"'. $vid_data->video_webm .' poster="' . $vid_data->poster . '"' . $vid_data->autoplay . $vid_data->loop . $preload . '][/video]';
  			
  			} elseif ($video_type == 'url') {
  			
  				echo '[video width="100%" height="100%" mp4="' . $settings->video_url . '" poster="' . $poster_url . '"' . $vid_data->autoplay . $vid_data->loop . $preload . '][/video]';
			
  			
  			}
  			?>
      
      <?php } ?>
      
    </div>
    
  </div>
  
  <div class="col-2 col-xs-12 col-sm-12 col-lg-5 eh">
  		
		<img class="arrowTop" src="<?php echo WP_PLUGIN_URL ?>/bb-modules/04_newsletter/img/arrow4.png">
  
    <div class="col-2c">
    
      <?php /*<img class="forward" src="<?php echo WP_PLUGIN_URL ?>/bb-modules/04_newsletter/img/forward.png"/> */ ?>
      
      
      <p class="header"><?php echo $name; ?></p>
      
      <?php if ($form_id) { 
      		
      		$form_shortcode = '[newsletter_signup_form id=' . $form_id . ']';
      		echo do_shortcode( $form_shortcode );
      		
      		
      		} else { /* ?>
      
      <form action="newsletter">
        <select name="sel1" id="sel1">
          <option value="Klavier">Klavier</option>
          <option value="Klavier">Klavier2</option>
          <option value="Klavier">Klavier3</option>
        </select>
        <input type="email" name="email" placeholder="email"/>
        <button class="btn btn-primary" type="submit">Anmelden</button>
      </form>
      
      	<?php */ } ?>
      
      <p class="infotext">
        <img src="<?php echo $img_envelope; ?>"/>
        <span><?php echo $text; ?></span>
      </p>
      
      
    </div>
  </div>
  
</div>

