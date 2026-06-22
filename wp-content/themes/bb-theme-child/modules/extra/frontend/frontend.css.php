<?php

isset($settings->section_icon) ? $section_icon = $settings->section_icon : $section_icon = FALSE;
isset($settings->custom_icon) ? $custom_icon = $settings->custom_icon: $custom_icon = FALSE;

?>

<?php if ($section_icon) { ?>

	.fl-node-<?php echo $id; ?> .section_icon {
	    position:absolute;
	    <?php echo $section_icon; ?>: -41px;
		left: 50%;
		transform: translateX(-50%);
		line-height: 0;
		font-size: 0;
	}
	
	<?php /* separator
	.fl-node-<?php echo $id; ?> .section_icon:before {
		content: '';
	    border-<?php echo $section_icon; ?>: 1px solid #d3dce7;
	    display: block;
	    position:absolute;
	    <?php echo $section_icon; ?>: 0;
		left: 0;
		right: 0;
	}
		*/ ?>
	
	<?php if ($custom_icon) { ?>	
		img.custom-s_icon {
			position: absolute;
			top: 25px;
			left: 52%;
			-webkit-transform: translateX(-50%);
			transform: translateX(-50%);
		}
	<?php } ?>
	
<?php } ?>
