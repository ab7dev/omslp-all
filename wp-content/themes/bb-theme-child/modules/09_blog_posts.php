<?php 
$title = $settings->title;
if ( !is_user_logged_in() && !empty($settings->title_quest)){
  $title = $settings->title_quest;
}
$search = $settings->search;
$parent_page = $settings->parent_page;
if (!$parent_page) { $parent_page = get_the_ID(); } 

?>

  
  <div class="header">
    <h2 class="title<?php if ($search=='') { echo ' title-md'; } ?>"><?php echo $title; ?></h2>
  </div> 
  
  
 <?php 

if (!empty($search)){ 
  
  echo do_shortcode('[facetwp facet="search_field"]');

  ?>
  
  <div class="facet-row-grey">
    <div class="facet-column-left">Dein Level:</div>
    <div class="facet-column-right"><?php echo do_shortcode('[facetwp facet="choose_dificult"]'); ?></div>
  </div>
  <div class="facet-row-white">
    <div class="facet-column-left">Kategorien:</div>
    <div class="facet-column-right"><?php echo do_shortcode('[facetwp facet="tag"]'); ?></div>
  </div>
  <div class="facet-row-grey">
    <div class="facet-column-left">Stilistik:</div>
    <div class="facet-column-right"><?php echo do_shortcode('[facetwp facet="style"]'); ?></div>
  </div>  
   
<?php   //echo do_shortcode('[facetwp facet="new_course"]'); 
}

  
  $quantity = $settings->quantity;

  if (is_front_page()){
     $args = array(
      'post_type' => 'page', 
      'orderby' => 'rand',
      'posts_per_page' => $quantity,
      'meta_query' => array( 
           array(
             'key' => 'featured',
             'value' => 1,
             'compare' => '=='
           )
        ), 
      );
  } else if (is_page(1299)) {
  	if(isset($_COOKIE[last_course])) {
  	  $last_post_id = $_COOKIE[last_course];
      $parent_page = wp_get_post_parent_id( $last_post_id );
      if (in_array( $parent_page, array(625,629,627,632,633,637))) {
         $args = array(
        'post_type' => 'page',
        'post_parent' => $parent_page,
        'orderby' => 'menu_order',
        'order' => 'ASC', 
        'posts_per_page' => $quantity,
        'facetwp' => true, 
        );
      }else{
          $args = array(
            'post_type' => 'page',
            'post_parent__in' => array(625,629,627,632,633,637),
            'orderby' => 'menu_order',
            'order' => 'ASC', 
            'posts_per_page' => $quantity,
            'facetwp' => true, 
            );
      }
  	 
  	} else {
  	  $parent_page = array(625,629,627,632,633,637);
  	  $args = array(
  	    'post_type' => 'page',
  	    'post_parent__in' => $parent_page,
  	    'orderby' => 'menu_order',
  	    'order' => 'ASC', 
  	    'posts_per_page' => $quantity,
  	    'facetwp' => true, 
  	    );
  	}

  } else {
    $args = array(
      'post_type' => 'page',
      'post_parent' => $parent_page,
      'orderby' => 'menu_order',
      'order' => 'ASC', 
      'posts_per_page' => $quantity,
      'facetwp' => true, 
      );
  }

  $loop = new WP_Query ($args);
  
  if ($loop->have_posts()){
     
  ?>
  
  
  <div class="blog row facetwp-template">
   
  <?php 
    
    // loop start
    
    $i = 1;
    while ($loop->have_posts()): $loop->the_post(); 
      $id =  $loop->post->ID;
      
      
      if (get_field('short_title')) { 
        $post_title = get_field('short_title'); 
      } else { 
        $post_title = get_the_title(); 
      }
      
      $excerpt = get_field('excerpt');  
      $post_link = get_permalink();
      $post_date = get_the_date();
      $difficulty_level = get_field_object('difficulty_level');
        $value = $difficulty_level['value'];
        $difficulty_level = $difficulty_level['choices'][ $value ];
      
      $post_thumbnail_id = get_post_thumbnail_id( $id );
      $post_thumbnail_url = wp_get_attachment_image_src( $post_thumbnail_id , 'full' );
      
      // Get the URL of our processed image
      $post_image = get_the_post_thumbnail_url(get_the_ID(),'course-image-panel');
      //$post_image = get_the_post_thumbnail_url(get_the_ID(),'full' );
     
      // URL for label 'new'
      $label_new_url = WP_PLUGIN_URL . '/bb-modules/09_blog_posts/img/new_label.png'; 
      ?>
      
      <div class="col-lg-4 col-md-6 col-sm-12">
        <a href="<?php echo $post_link; ?>" class="post-thumbnail grd-bg" style="background-image: url('<?php echo $post_image; ?>')" title="<?php echo $post_title; ?>"></a>
        <?php 
     
        if (isset(get_field('new_course')[0]) && get_field('new_course')[0] == 'Neu'){
          echo '<img class="new_course" src="' . $label_new_url . '"/>';
        } 

        $label_one_url = get_field('label_one_url');
        if (!empty($label_one_url)) {
          echo '<img class="label-one" src="' . $label_one_url . '"/>';
        }


        ?>
        <div class="post-container">
        	<div class="post-excerpt eh">
        		<h3 class="post-title"><?php echo $post_title; ?></h3>
        	  <p class=""><?php echo $excerpt; ?></p>
        	</div>
        	<div class="post-footer">
        	  <div class="post-difficult <?php echo $difficulty_level; ?>">       
        	    <i class="fa fa-level-up" aria-hidden="true"></i>
        	    <?php echo $difficulty_level; ?>
      	    </div>
            <a href="<?php echo $post_link; ?>" class="link-more">ZUM KURS</a>
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
?>

    <h2 class="title title-md"><?php _e( 'Leider sind keine Treffer zu diesem Suchbegriff vorhanden.' ); ?></h2>
   
<?php
  } 
  wp_reset_postdata();

  ?>



