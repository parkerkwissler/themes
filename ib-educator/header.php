<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<?php
	$body_class = '';

	// Toolbar?
	$has_toolbar = get_theme_mod( 'toolbar_enable', 1 );

	if ( 1 == $has_toolbar ) {
		$body_class .= 'has-toolbar';
	}

	// Sidebar position.
	if ( 'left' == get_theme_mod( 'sidebar_position' ) ) {
		$body_class .= ' sidebar-left';
	}

	// Sticky footer.
	if ( 0 == get_theme_mod( 'footer_widgets_enable', 1 ) ) {
		$body_class .= ' sticky-footer';
	}
?>
<body <?php body_class( $body_class ); ?>>
	<div id="page-container">
		<?php if ( 1 == $has_toolbar ) : ?>
			<div id="page-toolbar">
				<div class="container clearfix">
					<div class="toolbar-items">
						<div class="item">
							<div class="inner">
								<?php
									// This option allows a number of HTML tags.
									echo get_theme_mod( 'toolbar_text' );
								?>
							</div>
						</div>
					</div>

					<?php
						echo edutheme_social_links();

						/**
						 * Hook to add additional HTML to the toolbar.
						 */
						do_action( 'educator_theme_toolbar' );
					?>
				</div>
			</div>
		<?php endif; ?>

		<header id="page-header" <?php if ( 1 == get_theme_mod( 'fixed_header', 0 ) ) echo ' class="fixed-header"'; ?>>
			<div id="page-header-inner">
				<div id="header-container">
					<div class="container clearfix">
						<div id="main-logo">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<?php
									$logo_1x = get_theme_mod( 'logo' );
									$logo_2x = get_theme_mod( 'logo_2x' );

									if ( $logo_2x ) {
										echo '<img src="' . esc_url( $logo_1x ) . '" srcset="' . esc_url( $logo_1x ) . ' 1x, ' . esc_url( $logo_2x ) . ' 2x" alt="">';
									} else {
										echo '<img src="' . esc_url( $logo_1x ) . '" alt="">';
									}
								?>
							</a>
						</div>

						<?php if ( is_user_logged_in() ) : $cur_user = wp_get_current_user(); ?>
							<div id="user-nav" class="user-menu">
								<a class="user-menu-name" href="#"><?php echo esc_html( $cur_user->display_name ); ?> <span class="fa fa-angle-down"></span></a>
								<ul class="menu">
									<?php
										wp_nav_menu( array(
											'theme_location' => 'user_menu',
											'container'      => false,
											'items_wrap'     => '%3$s',
											'menu_class'     => 'menu',
										) );

										$logout_to = '';
										$login_page_id = get_theme_mod( 'login_page' );

										if ( $login_page_id ) {
											$logout_to = get_permalink( $login_page_id );
										}
									?>
									<li><a href="<?php echo wp_logout_url( $logout_to ); ?>"><?php _e( 'Log Out', 'ib-educator' ); ?></a></li>
								</ul>
							</div>
						<?php elseif ( 1 == get_theme_mod( 'main_menu_auth', 1 ) ) : ?>
							<?php echo educator_auth_nav(); ?>
						<?php endif; ?>

						<div id="header-search">
							<button><span class="fa fa-search"></span></button>
							<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
								<div class="clearfix">
									<input type="text" name="s" placeholder="<?php _e( 'Search...', 'ib-educator' ); ?>">
								</div>
							</form>
						</div>

						<nav id="main-nav">
							<?php
								wp_nav_menu( array(
									'theme_location' => 'primary',
									'container'      => false,
									'fallback_cb'    => false,
								) );
							?>
						</nav>
					</div>
				</div>
			</div>
		</header>
