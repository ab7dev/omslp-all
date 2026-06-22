<?php 

$text1 = get_the_title();
$text2 = get_field('course_description_left');
$text3 = get_field('course_description_right');
$vimeo_free_course = get_field('vimeo_free_course');
$vimeo_id = get_field('vimeo_id'); // Vimeo showcase ID
$gallery_id = get_field('gallery_id'); // Vimeography Gallery ID
$preload  = FLBuilderModel::is_builder_active() ? ' preload="none"' : '';


// User details
$user_id = get_current_user_id();
$product_id = 215999;


// Get OLD WC status  
$active_subscriptions = get_posts( array(
  'numberposts' => -1,
  'meta_key'    => '_customer_user',
  'meta_value'  => $user_id,
  'post_type'   => 'shop_subscription',
  'post_status' =>  array('wc-active','wc-pending-cancel'), // Active subscription
  )
);

// Get NEW PMS status
$has_membership = false;
if (function_exists('pmpro_hasMembershipLevel'))  { 
	if (pmpro_hasMembershipLevel( array('2','3','4','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','39','40','41','42','43') ) ) {
    $has_membership = true;
  } 
}  

// Set capability for all instrument subscriber
(!empty($active_subscriptions) || $has_membership == true) ? $has_subscription = true : $has_subscription = false;
	
// Check for product id of KCBundle to distinct between all instruments subscription and a bundle subscription
$user_has_product_id = wc_customer_bought_product( '', $user_id, $product_id );

// Set capability for KCB subscriber KCB = Klavier Classic Bundle
(!empty($active_subscriptions) && $user_has_product_id == true) ? $has_subscription_bundle = true : $has_subscription_bundle = false;

// For debugging allow admins to view like subscriber
if ( is_user_logged_in() && current_user_can('administrator') ) $has_subscription = true;
if ( is_user_logged_in() && current_user_can('administrator') ) $has_subscription_bundle = true;
  
?>


<div class="row kurs_inside">
  
	<div class="col-sm-12 col-md-4">
      <div class="blub"><?php echo $text1; ?></div>
	</div>
	
	<div class="col-sm-12 col-md-4">
      <?php echo $text2; ?>
	</div>
	
	<div class="col-sm-12 col-md-4">
      <?php echo $text3; ?>
	</div>
      	  
</div> 


<?php if ( get_field('use_vimeo') == true || get_field('vimeo_id') != 0 ) { ?> 
<?php /* START VIMEO */ ?>

<?php if (  $has_subscription == true || $vimeo_free_course == true ) { ?>
  
  <?php if ( $gallery_id != 99 ) { // value 99 is a fallback to skip vimeography ?>

  	<?php /* VIMEOGRAPHY */ ?>

  	<?php if ( $gallery_id == 0) { $gallery_id = 1; } // reset the default value ?>
    
		<?php /* Case overwrite showcase with course_option */
		      $vimeography_shortcode = "[vimeography id=\"".$gallery_id."\" source=\"https://vimeo.com/showcase/".$vimeo_id."\"]"; ?>

		<?php /* Case use video from vimeography gallery */
		      // $vimeography_shortcode = "[vimeography id=\"".$gallery_id."\" width=\"100%\" ]"; ?>

    <?php write_log("VIMEOGRAPHY SHORTCODE: ".$vimeography_shortcode); ?>

    <?php echo do_shortcode($vimeography_shortcode); ?>

  <?php } else { ?>

  	<?php /* VIMEO PLAYER - case playlist inside videoplayer (LEGACY)*/ ?>

<div style='padding:56.25% 0 0 0;position:relative;'><iframe src='https://vimeo.com/showcase/<?php echo get_field('vimeo_id'); ?>/embed' allowfullscreen frameborder='0' style='position:absolute;top:0;left:0;width:100%;height:100%;'></iframe></div>

  <?php } //endif get_field('gallery_id') != 0 ?>

<?php } else { ?>

  <?php /* PROMO IMAGE */?>

  <?php
    // Get promo image/video details
    $promo_button_target_url = get_field('promo_button_target_url_default','option');
    if ( has_post_parent() == TRUE ) {
    	$parent_post = get_post_parent();
    	switch ($parent_post->ID) {
    		case '625': //Klavier
    		  $promo_button_target_url = get_field('promo_button_target_url_klavier','option');
    		  break;
    		case '221020': //Gesang
    		  $promo_button_target_url = get_field('promo_button_target_url_gesang','option');
    		  break;
    		case '159182': //Keyboard
    		  $promo_button_target_url = get_field('promo_button_target_url_keyboard','option');
    		  break;
    		case '629': //Gitarre
    		  $promo_button_target_url = get_field('promo_button_target_url_gitarre','option');
    		  break;
    		case '176146': //E-Gitarre
    		  $promo_button_target_url = get_field('promo_button_target_url_e-gitarre','option');
    		  break;
    		case '165254': //Ukulele
    		  $promo_button_target_url = get_field('promo_button_target_url_ukulele','option');
    		  break;
    		case '632': //Schlagzeug
    		  $promo_button_target_url = get_field('promo_button_target_url_schlagzeug','option');
    		  break;
    		case '627': //E-Bass
    		  $promo_button_target_url = get_field('promo_button_target_url_e-bass','option');
    		  break;
    	}
    } 
    ?>

<div style='width: 100%; text-align: center; padding: 10px;'>
  <a href="<?php echo $promo_button_target_url; ?>"><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2022/08/Mitgliedwerden-Einblendung-Vimeo.png"></a>
</div>

<?php } //endif $has_subscription == true ?>

<?php /* END VIMEO */ ?>

<?php } else { ?>

<?php /* START DEFAULT PLAYER */ ?>

<div class="row course_player loader_svg on"<?php if ( get_field('playlist_visibility') == 1  ) { ?> style="width:100%"<?php } ?>>
	<?php if ( get_field('playlist_visibility') != 1  ) { $colv = 'col-lg-8'; } else { $colv = 'col-sm-12 full-width-video'; }?>
	<div class="<?php echo $colv; ?>">
		<div class="fl-video fl-wp-video">
			
			
		</div> 
		<?php
			
			if (  $has_subscription != true || $has_subscription_bundle == true ) { ?>
				
				<div class="promo_add">
					<img src="<?php echo get_field('default_promo_add','option'); ?>"/>
					<div class="content">
						<?php echo get_field('promo_add_content','option'); ?>
						<a class="btn btn-default" href="<?php echo get_field('promo_add_button_url','option'); ?>"><?php echo get_field('promo_add_button_text','option'); ?></a>
					</div>
				</div>
					
			<?php
			}
			
		?>
	</div> 
	
	<?php if ( get_field('playlist_visibility') != 1 ) { $coll = 'col-sm-12 col-lg-4'; } else { $coll = 'display-none'; }?>
	<div class="<?php echo $coll; ?>">
	
 		
		<div class="lesson_list lessons"  id="<?php echo get_the_ID(); ?>">
		    <?php 
		    $i = 0;
		    $j = 0;
		    if( have_rows('lesson') ): ?>
		 
		 	   <?php while( have_rows('lesson') ): the_row(); 
			    
			    	$i++;
			    
			 		$is_finished = '';
			 		$free_lesson = '';
			 		$current_lesson = ''; 
			 		
			 		
			 		if ( get_sub_field('free_lesson') == 1 && $has_subscription != true ) { $free_lesson = ' free_lesson'; }
			 		
			 		// current lesson test for finished option in FUTURE
			 		// if ( get_sub_field('finished_lesson') == 0 && $j == 0  ) {   
			 		// 	$j++;
			 		// 	$current_lesson = ' active'; 
			 			
			 		// } else {
			 		// 		$current_lesson = ''; 
			 		// }
			 		// if ( get_sub_field('finished_lesson') == 1 ) { $is_finished = ' finished'; }
			 		
			 		if ( $i == 1 ) { $current_lesson = ' active'; }
			 		
			 		?>
			 
			        <div class="part<?php echo $is_finished . $current_lesson . $free_lesson; ?>" id="<?php echo $i; ?>">
			        	<div class="image">
				        	<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'course-image-single-thumb'); ?>"/>
				        	<span><?php echo $i; ?></span>
			        	</div>
			        	<div>
			        		<h4><?php the_sub_field('lesson_title'); ?></h4>
			        		<p><?php the_sub_field('lesson_description'); ?></p>
		        		</div>
		        	</div>
			    <?php endwhile; ?>
			 
			<?php endif; ?>  
		</div>
	</div>
</div>
<?php /* END DEFAULT PLAYER */ ?>
<?php } //END VIMEO OR DEFAULT PLAYER ?>
<div class="kurs-inside-footer"></div>
