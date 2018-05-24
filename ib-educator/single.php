<?php get_header(); ?>
<?php the_post(); ?>

<section class="section-content">
	<div class="container clearfix">
		<div class="main-content">
			<?php
				get_template_part( 'content', 'single' );
				echo educator_share();

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</section>

<?php get_footer(); ?>
