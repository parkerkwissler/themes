<?php
/*
Template Name: Register Page
*/
?>

<?php
	get_header();
	the_post();
?>

<section class="section-content">
	<div class="container clearfix">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php educator_page_title( 'page' ); ?>
			
			<div class="entry-content">
				<?php the_content(); ?>

				<div id="auth-forms">
					<?php if ( ! is_user_logged_in() ) : ?>
					
					<div class="the-tabs">
						<ul>
							<li><a href="<?php echo esc_url( get_permalink( get_theme_mod( 'login_page' ) ) ); ?>"><?php _e( 'Log in', 'ib-educator' ); ?></a></li>
							<li class="active"><a href="<?php echo esc_url( get_permalink( get_theme_mod( 'register_page' ) ) ); ?>"><?php _e( 'Register', 'ib-educator' ); ?></a></li>
						</ul>
					</div>

					<?php
						if ( 'registered' == get_query_var( 'action' ) ) {
							echo '<div class="ib-edu-message success">' . __( 'Registration complete. Please check your e-mail.', 'ib-educator' ) . '</div>';
						}
					?>

					<div class="register-form">
						<form id="registerform" name="registerform" action="<?php echo esc_url( site_url( 'wp-login.php?action=register' ) ); ?>" method="post">
							<p class="login-username">
								<label for="register-login"><?php _e( 'Username', 'ib-educator' ); ?></label>
								<input type="text" name="user_login" id="register-login" class="input" size="20" required>
							</p>

							<p class="login-email">
								<label for="register-email"><?php _e( 'Email', 'ib-educator' ); ?></label>
								<input type="email" name="user_email" id="register-email" class="input" size="20" required>
							</p>

							<p class="login-submit">
								<input type="submit" name="wp-submit" id="submit-register-form" class="button" value="<?php _e( 'Register', 'ib-educator' ); ?>">

								<?php
									$redirect_to = add_query_arg( 'action', 'registered', get_permalink() );
								?>
								<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>">
							</p>
						</form>
					</div>

					<?php else : ?>

					<p class="text-center"><?php _e( 'You are logged in.', 'ib-educator' ); ?> <a href="<?php echo esc_url( wp_logout_url( get_permalink( get_theme_mod( 'login_page' ) ) ) ); ?>"><?php _e( 'Log out', 'ib-educator' ); ?></a></p>

					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>