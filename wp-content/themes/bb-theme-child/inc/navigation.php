<?php /* Navigation issues that cannot be build with WP or BB */

// "Alle X Kurse" Shortcode
function oms_nav_parent_course_shortcode( $atts ) {

	get_template_part('template-parts/nav', 'parent_course');

}
add_shortcode( 'oms_nav_parent_course', 'oms_nav_parent_course_shortcode' );