<?php 

/*img0*/
$img_0_lib = wp_get_attachment_image_src( $settings->img_0_lib, "full" );
$img_0_lib = $img_0_lib[0];
if (!$img_0_lib) { $img_0_lib = WP_PLUGIN_URL . '/bb-modules/02_testimonial_inline/img/eKomi-Kundenzufriedenheit-Home95.png'; }

/*img1*/
$img_1_lib = wp_get_attachment_image_src( $settings->img_1_lib, "full" );
$img_1_lib = $img_1_lib[0];
if (!$img_1_lib) { $img_1_lib = WP_PLUGIN_URL . '/bb-modules/02_testimonial_inline/img/testimonial.png'; }

/*img2*/
$img_2_lib = wp_get_attachment_image_src( $settings->img_2_lib, "full" );
$img_2_lib = $img_2_lib[0];
if (!$img_2_lib) { $img_2_lib = WP_PLUGIN_URL . '/bb-modules/02_testimonial_inline/img/testimonial.png'; }

/*img3*/
$img_3_lib = wp_get_attachment_image_src( $settings->img_3_lib, "full" );
$img_3_lib = $img_3_lib[0];
if (!$img_3_lib) { $img_3_lib = WP_PLUGIN_URL . '/bb-modules/02_testimonial_inline/img/testimonial.png'; }


$name1 = $settings->name;
$content1 = $settings->content;
$name2 = $settings->name2;
$content2 = $settings->content2;
$name3 = $settings->name3;
$content3 = $settings->content3;


?>

<div class="testimonials_inline row">
  <div class="col-xs-12 col-sm-6 col-lg-3 eh">
   <a href="https://www.ekomi.de/bewertungen-openmusicschoolde.html" rel="nofollow" target="_blank">
    <img src="<?php echo $img_0_lib; ?>" alt="eKomi">
  </a>
  </div>
  <div class="col-xs-12 col-sm-6 col-lg-3 eh">
    <img class="photo" src="<?php echo $img_1_lib; ?>" alt="<?php echo $name1; ?>">
    <div class="content">
      <p class="name"><?php echo $name1; ?></p>
      <p class="text"><?php echo $content1; ?></p>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-lg-3 eh">
    <img class="photo" src="<?php echo $img_2_lib; ?>" alt="<?php echo $name2; ?>">
    <div class="content">
      <p class="name"><?php echo $name2; ?></p>
      <p class="text"><?php echo $content2; ?></p>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-lg-3 eh">
    <img class="photo" src="<?php echo $img_3_lib; ?>" alt="<?php echo $name3; ?>">
    <div class="content">
      <p class="name"><?php echo $name3; ?></p>
      <p class="text"><?php echo $content3; ?></p>
    </div>
  </div>
</div>

