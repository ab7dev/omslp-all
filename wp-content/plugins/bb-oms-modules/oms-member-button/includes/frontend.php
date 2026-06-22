<?php

$button_node_id = "fl-node-$id";

if ( isset( $settings->id ) && ! empty( $settings->id ) ) {
	$button_node_id = $settings->id;
}

/**
 *  Get promo image/video details
 *  Copy from 38_kurs_inside.php
 *  Get instrument specific target url for button
 */ 
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
<div class="<?php echo $module->get_classname(); ?>">
	<?php if ( isset( $settings->click_action ) && 'lightbox' == $settings->click_action ) : ?>
		<a href="<?php echo 'video' == $settings->lightbox_content_type ? esc_url( do_shortcode( $settings->lightbox_video_link ) ) : '#'; ?>" class="fl-button <?php echo $button_node_id; ?> fl-button-lightbox<?php echo ( 'enable' == $settings->icon_animation ) ? ' fl-button-icon-animation' : ''; ?>"<?php echo $module->get_role(); ?>>
	<?php else : ?>
		<a href="<?php echo $promo_button_target_url; ?>"<?php echo ( isset( $settings->link_download ) && 'yes' === $settings->link_download ) ? ' download' : ''; ?> target="<?php echo $settings->link_target; ?>" class="fl-button<?php echo ( 'enable' == $settings->icon_animation ) ? ' fl-button-icon-animation' : ''; ?>"<?php echo $module->get_role(); ?><?php echo $module->get_rel(); ?>>
	<?php endif; ?>
		<?php
		if ( ! empty( $settings->icon ) && ( 'before' == $settings->icon_position || ! isset( $settings->icon_position ) ) ) :
			?>
		<i class="fl-button-icon fl-button-icon-before <?php echo $settings->icon; ?>" aria-hidden="true"></i>
		<?php endif; ?>
		<?php if ( ! empty( $settings->text ) ) : ?>
		<span class="fl-button-text"><?php echo $settings->text; ?></span>
		<?php endif; ?>
		<?php
		if ( ! empty( $settings->icon ) && 'after' == $settings->icon_position ) :
			?>
		<i class="fl-button-icon fl-button-icon-after <?php echo $settings->icon; ?>" aria-hidden="true"></i>
		<?php endif; ?>
	</a>
</div>
<?php if ( 'lightbox' == $settings->click_action && 'html' == $settings->lightbox_content_type && isset( $settings->lightbox_content_html ) ) : ?>
	<div class="<?php echo $button_node_id; ?> fl-button-lightbox-content mfp-hide">
		<?php echo $settings->lightbox_content_html; ?>
	</div>
<?php endif; ?>
