<?php 

/* Mailster Form ID */
$form_id = $settings->form_id;

$text1 = $settings->text1;
$text2 = $settings->text2;
?>


<div class="row video_one">
  
	<div class="col-sm-12 col-md-12 col-lg-12">
		
		<div class="row" style="position: relative;">
			<img class="arrowTop" src="<?php echo WP_PLUGIN_URL ?>/bb-modules/13_conversion_one_video/img/arrow.jpg"/>
		</div>
		  
		<div class="box-headline text-center">
			<p><?php echo $text1; ?></p>
		</div>

      	<div class="box text-center">
      		<div class="center">
      			<?php echo $text2; ?>
      		</div>
      	
      	<?php if ($form_id) { 
      		
      		$form_shortcode = '[newsletter_signup_form id=' . $form_id . ']';
      		echo do_shortcode( $form_shortcode );
      		
      		
      		} else { /* ?>
	      	<form method="post">
	      	  <input type="email" class="form-control" name="email_address" id="email_address" placeholder="Deine E-Mail-Adresse">
	      	  <button class="btn">Jetzt anfordern!</button>
	      	</form>
      	<?php */ } ?>
      	
      	
      		<div class="privacy">
      			Wir geben deine Daten niemals an Dritte weiter. Du meldest dich hier zu unserem Gratiskurs und Newsletter an. Du kannst dich jederzeit mit nur einem Mausklick wieder abmelden.	
      		</div>
      	</div>
			
	</div>
</div>


