<nav id="site-navigation" class="" role="navigation">
	
	        <?php /* When user is logged in set link to panel page */ ?>
					<?php if ( is_user_logged_in() ) {
						/*wp_nav_menu( array( 
							'menu' => '6245',
							'menu_id' => 'primary-menu',
							'menu_class' => 'menu',
							'container_id' => 'primary-menu',
							 ) );*/
					} ?>
	
					<?php /* On single course page set back link to all courses of same instrument */ ?>					
					<?php //if (get_field('course_layout') == 1  ){ ?>
						<ul id="primary-menu" class="menu">
							<li class="menu-item"><a href="<?php echo get_permalink($post->post_parent); ?>">ALLE <?php echo get_the_title($post->post_parent); ?> KURSE</a></li>
						</ul>
					<?php //} ?>

</nav>