<?php
(isset ($settings->section_icon)) ? $section_icon = $settings->section_icon : $section_icon = 0;
(isset ($settings->select_icon)) ? $select_icon = $settings->select_icon : $select_icon = 0;
(isset ($settings->custom_icon)) ? $custom_icon = $settings->custom_icon : $custom_icon = 0;
?>

<?php if ($section_icon && ($select_icon != 'custom') ) { ?>

<div class="section_icon">
	<img src="<?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $select_icon; ?>.png" srcset="<?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $select_icon; ?>.png 1x, <?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $select_icon; ?>@2x.png 2x" alt="---">
</div>

<?php } ?>

<?php if ($section_icon && ($select_icon == 'custom') ) { ?>
<?php
$select_icon = 'icon_blank';
$custom_icon = $settings->custom_icon;
$custom_icon = wp_get_attachment_image_src($custom_icon, 'beaver-custom-icon' );

//$params = array( /*'width' => 51,*/ 'height' => 33 );
//$custom_icon = bfi_thumb( $custom_icon[0] , $params );

//$params = array( /*'width' => 51,*/ 'height' => 66 );
//$custom_icon_retina = bfi_thumb( $custom_icon_retina[0] , $params );


?>
<div class="section_icon">
	<img src="<?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $select_icon; ?>.png" srcset="<?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $select_icon; ?>.png 1x, <?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $select_icon; ?>@2x.png 2x" alt="---">

    <?php if ($custom_icon) { ?>
    	<img class="custom-s_icon" src="<?php echo $custom_icon; ?>" srcset="<?php echo $custom_icon; ?> 1x, <?php get_stylesheet_directory(); ?>/modules/extra/img/<?php echo $custom_icon_retina; ?> 2x" alt="-">
    <?php } ?>

</div>

<?php } ?>
