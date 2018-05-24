<?php get_header(); ?>
<?php
	if ( 'posts' == get_option( 'show_on_front' ) ) {
		include( get_home_template() );
	} else {
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
	}
?>
<?php get_footer(); ?>
