<?php 
$header = $settings->header;
$header_weight = $settings->header_weight;

$content = $settings->content;

$img_lib = $settings->img_lib;
$img = $settings->img_url;
if ( $img_lib ) {$img = $img_lib;}

$img_lib_r = $settings->img_lib_r;
$img_r = $settings->img_url_r;
if ( $img_lib_r ) {$img_r = $img_lib_r;}

$img_lib_m = $settings->img_lib_m;
$img_m = $settings->img_url_m;
if ( $img_lib_m ) {$img_m = $img_lib_m;}

$img_alt = $settings->img_alt;
if ( !$img_alt ) {
	$img_alt = $header;
}
?>

<div class="section container img-content-block">
  <div class="row">
    <div class="col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10">
      
      <div class="row">
        <hr>
        <div class="col-sm-6 col-xs-12 div-img box-default lh0">
          <?php if ($img) { ?><img src="<?php echo $img; ?>" <?php if ($img_r) { ?>srcset="<?php echo $img; ?> 1x, <?php echo $img_r; ?> 2x"<?php } ?> alt="<?php echo $img_alt; ?>"/><?php } ?>
        </div>
        <div class="col-sm-6 col-xs-12 div-content">
          <p class="title"><?php echo $header; ?></p>
          <div class="fz16"><?php echo $content; ?></div>
        </div>
      </div>
      
    </div>
  </div>
</div>