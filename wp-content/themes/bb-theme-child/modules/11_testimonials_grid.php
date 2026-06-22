<?php 
$hw = $settings->header_weight;
$title = $settings->title;

?>

  <div class="header">
    <<?php echo $hw; ?> class="title title-md"><?php echo $title; ?></<?php echo $hw; ?>>
  </div>
  
  
<?php 

$quantityt = $settings->quantity;
$categoryt = $settings->categoryt;

if (!empty($categoryt)) {
  $argst = array(
  'post_type' => 'testimonials',
  'post_status' => array(  
             'publish'
    ),       
  'posts_per_page' => $quantityt,  
  'meta_query' => array( 
       array(
         'key' => 'testimonial_categories_field',
         'value' => $categoryt,
         'compare' => '=='
       )
    ), 
 
  );
  
} else {
  
$argst = array(
  'post_type' => 'testimonials',
  'orderby' =>'rand',
  'posts_per_page' => $quantityt,
  'meta_query' => array( 
     array(
       'key' => 'featured_testimonial',
       'value' => 1,
       'compare' => '=='
     )
  ), 
   

  );
  
}
  
  
  
  
  $loopt = new WP_Query ($argst);
 if ($loopt->have_posts()){
?>

 
<div class="testimonials-grid row">

<?php 
  
  // loop start
  
  $i = 1;
  while ($loopt->have_posts()): $loopt->the_post(); 
    $id =  $loopt->post->ID;
    
    
    if (get_field('short_title')) { 
      $post_title = get_field('short_title'); 
    } else { 
      $post_title = get_the_title(); 
    }
    
    $content = get_the_content();  
    $post_link = get_permalink();
    $post_date = get_the_date();
    
    $post_thumbnail_id = get_post_thumbnail_id( $id );
    $post_thumbnail_url = wp_get_attachment_image_src( $post_thumbnail_id , 'full' );
    
    // Our parameters, do a resize
    $params = array( 'width' => 51, 'height' => 51 );
    // Get the URL of our processed image
    //$post_image = bfi_thumb( $post_thumbnail_url[0] , $params );
    $post_image = $post_thumbnail_url[0];
    
    //
    
    ?>
    
    <div class="col-lg-4 col-md-6 col-sm-12<?php /* if ($i == 1 || (($i %3 ) == 1)) echo ' clear'; */ ?>">
    
      <div class="t-container eh">
        <div class="t-header">
           <img src="<?php echo $post_image; ?>"/>
           <p class="t-title"><?php echo $post_title; ?></p>
        </div>
       
        <div class="t-content">
          <?php echo $content; ?>
        </div>
      </div>
    </div>
    
    <?php 
    $i++;
  endwhile; 
  

  ?>
</div>



<?php 
} else { 
// no posts
} 

wp_reset_postdata();
?>



