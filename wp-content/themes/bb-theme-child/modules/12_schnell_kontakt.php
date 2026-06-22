<?php 
$hw = $settings->header_weight;
$title = $settings->title;

$content1 = $settings->content1;
$content2 = $settings->content2;
$content3 = $settings->content3;

$img1 = WP_PLUGIN_URL . '/bb-modules/12_schnell_kontakt/img/icon_email.png';
$img2 = WP_PLUGIN_URL . '/bb-modules/12_schnell_kontakt/img/icon_call.png';
$img3 = WP_PLUGIN_URL . '/bb-modules/12_schnell_kontakt/img/icon_career.png';
?>

<div class="header">
    <<?php echo $hw; ?> class="title title-md"><?php echo $title; ?></<?php echo $hw; ?>>
</div>

<div class="row s_kontakt">
  
  <div class="col-lg-4 col-sm-12">
     <a href="mailto:<?php echo $content1; ?>" class="eh">
       <img src="<?php echo $img1; ?>" alt="<?php echo $content1; ?>"/>
       <p class="title">SCHREIBE UNS AN</p>
       <?php echo $content1; ?>
     </a>
  </div>
  
  <div class="col-lg-4 col-sm-12">
     <a href="callto:<?php echo $content2; ?>" class="eh">
      <img src="<?php echo $img2; ?>" alt="<?php echo $content2; ?>"/>
      <p class="title">RUFE UNS AN</p>
      <?php echo $content2; ?>
     </a>
  </div>
  
  <div class="col-lg-4 col-sm-12">    
     <a href="mailto:<?php echo $content3; ?>" class="eh">
      <img src="<?php echo $img3; ?>" alt="<?php echo $content3; ?>"/>
      <p class="title">PRESSE KONTAKT</p>
      <?php echo $content3; ?>
     </a>    
  </div>
  
</div>


