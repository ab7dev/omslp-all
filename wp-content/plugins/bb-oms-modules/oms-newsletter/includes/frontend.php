<?php 

/* Mailster Form ID */
$form_id = $settings->form_id;

//$text1 = $settings->text1;
//$text2 = $settings->text2;
?>

<div class="center">
  <?php if ($form_id) : ?> 
    
    <?php $form_shortcode = '[newsletter_signup_form id=' . $form_id . ']'; ?>
    <?php echo do_shortcode( $form_shortcode ); ?>
  
  <?php else : ?>  
    
    <form method="post">
      <input type="email" class="form-control" name="email_address" id="email_address" placeholder="Deine Mail-Adresse">
      <button class="btn">Jetzt anfordern!</button>
    </form>

  <?php endif; ?>
</div>