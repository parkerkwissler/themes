<?php

/**
 * Theme version.
 */
define( 'IB_THEME_VERSION', '1.4.0' );

// Educator WP plugin customization.
require get_template_directory() . '/include/ibeducator.php';

// Template tags.
require get_template_directory() . '/include/template-tags.php';

// Theme customizer setup.
require get_template_directory() . '/include/customizer.php';

// Fonts.
require get_template_directory() . '/include/theme-fonts.php';

// Maximum width for the content.
if ( ! isset( $content_width ) ) {
	$content_width = 1140;
}

if ( is_admin() ) {
	function educator_admin_editor_width( $params ) {
		$params['width'] = '620px';
		return $params;
	}
	add_filter( 'tiny_mce_before_init', 'educator_admin_editor_width' );
}

if ( ! function_exists( 'educator_setup' ) ) :
/**
 * Setup the theme.
 */
function educator_setup() {
	// Make the theme available for translation.
	load_theme_textdomain( 'ib-educator', get_template_directory() . '/languages' );

	// Style visual editor.
	add_editor_style( array( 'css/editor-style.css' ) );

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails, and declare two sizes.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'ib-educator-grid', 360, 224, true );
	add_image_size( 'ib-educator-main-column', 620, 384, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary'   => __( 'Top primary menu', 'ib-educator' ),
		'user_menu' => __( 'Logged in user menu', 'ib-educator' ),
		'footer'    => __( 'Footer menu', 'ib-educator' ),
	) );

	// HTML5 support for built-in components.
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	// Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	// Let WordPress manage the <title> tag.
	add_theme_support( 'title-tag' );
}
endif;
add_action( 'after_setup_theme', 'educator_setup' );

if ( ! function_exists( '_wp_render_title_tag' ) ) :
function educator_render_title() {
	?>
<title><?php wp_title( '' ); ?></title>
	<?php
}
add_action( 'wp_head', 'educator_render_title' );
endif;

/**
 * Custom image sizes in media library.
 */
function educator_custom_image_sizes_choose( $sizes ) {
  $sizes['ib-educator-grid'] = __( 'Grid', 'ib-educator' );
  $sizes['ib-educator-main-column'] = __( 'Main Column', 'ib-educator' );
  
  return $sizes;
}
add_filter( 'image_size_names_choose', 'educator_custom_image_sizes_choose' );

/**
 * Enqueue scripts and styles.
 */
function educator_enqueue_scripts() {
	$disable_lightbox = (int) get_theme_mod( 'disable_lightbox', 0 );

	// Stylesheets.
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.2.0' );
	wp_enqueue_style( 'flexslider', get_template_directory_uri() . '/css/flexslider.css', array(), '2.2.2' );

	if ( 0 == $disable_lightbox ) {
		// Magnific popup.
		wp_enqueue_style( 'magnific-popup', get_template_directory_uri() . '/css/magnific-popup.css', array(), '0.9.9' );
		wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.js', array( 'jquery' ), '0.9.9', true );
	}
	
	wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), '1.3.3' );
	wp_enqueue_style( 'educator-style', get_template_directory_uri() . '/style.css', array(), '1.4.0' );

	// Scripts.
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.js', array(), '2.8.3' );
	wp_enqueue_script( 'jquery-validate', get_template_directory_uri() . '/js/jquery.validate.js', array( 'jquery' ), '1.13.1' );
	wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array( 'jquery' ), '1.3.3', true );
	wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/js/jquery.flexslider.js', array( 'jquery' ), '2.2.2', true );
	wp_enqueue_script( 'educator-base', get_template_directory_uri() . '/js/base.js', array( 'jquery' ), '1.4.0', true );
	wp_enqueue_script( 'educator-main', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), '1.4.0', true );
	wp_localize_script( 'educator-main', 'eduThemeObj', array(
		'disableLightbox' => $disable_lightbox,
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'educator_enqueue_scripts' );

/**
 * Setup widgets.
 */
function educator_widgets_init() {
	require get_template_directory() . '/include/widgets.php';
	register_widget( 'Educator_Contact_Widget' );

	// Main sidebar.
	register_sidebar( array(
		'name'          => __( 'Main Sidebar', 'ib-educator' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'The page sidebar.', 'ib-educator' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	// Footer widget areas.
	$footer_layout = get_theme_mod( 'footer_layout', '3_columns' );

	switch ( $footer_layout ) {
		case '4_columns':
			$columns = 4;
			break;

		default:
			$columns = 3;
	}

	register_sidebars( $columns, array(
		'name'          => __( 'Footer Column %d', 'ib-educator' ),
		'id'            => 'footer',
		'description'   => __( 'The widgets in the page footer.', 'ib-educator' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'educator_widgets_init' );

/**
 * Output courses on the author page.
 *
 * @param WP_Query $query
 * @return WP_Query
 */
function educator_author_page_posts( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( is_author() ) {
			$view = get_query_var( 'view' );

			switch ( $view ) {
				case 'courses':
					$post_type = 'ib_educator_course';
					break;

				default:
					$post_type = 'post';
			}

			$query->set( 'post_type', $post_type );
		} elseif ( is_post_type_archive( 'ib_educator_course' ) || is_tax( 'ib_educator_category' ) ) {
			$membership_id = get_query_var( 'membership_id' );

			if ( $membership_id ) {
				$ms = IB_Educator_Memberships::get_instance();
				$meta = $ms->get_membership_meta( $membership_id );

				if ( ! empty( $meta['categories'] ) ) {
					$query->set( 'tax_query', array(
						array(
							'taxonomy' => 'ib_educator_category',
							'field'    => 'id',
							'terms'    => $meta['categories'],
							'operator' => 'IN',
						)
					) );
				}
			}
		}
	}

	return $query;
}
add_filter( 'pre_get_posts', 'educator_author_page_posts' );

/**
 * Get login URL.
 *
 * @deprecated 1.2.1 Using the 'login_url' filter instead.
 * @return string
 */
function educator_login_url() {
	$login_page_id = get_theme_mod( 'login_page' );

	if ( $login_page_id ) {
		return get_permalink( $login_page_id );
	}

	return wp_login_url();
}

/**
 * Get registration URL.
 *
 * @deprecated 1.2.1 Using the 'register_url' filter instead.
 * @return string
 */
function educator_registration_url() {
	$register_page_id = get_theme_mod( 'register_page' );

	if ( $register_page_id ) {
		return get_permalink( $register_page_id );
	}

	return wp_registration_url();
}

/**
 * Filter the login URL.
 *
 * @param string $login_url
 * @param string $redirect
 * @return string
 */
function educator_login_url_filter( $login_url, $redirect ) {
	$login_page_id = get_theme_mod( 'login_page' );

	if ( $login_page_id ) {
		$login_url = get_permalink( $login_page_id );

		if ( ! empty( $redirect ) ) {
			$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
		}
	}

	return $login_url;
}
add_filter( 'login_url', 'educator_login_url_filter', 10, 2 );

/**
 * Filter the register URL.
 *
 * @param string $register_url
 * @return string
 */
function educator_register_url_filter( $register_url ) {
	$register_page_id = get_theme_mod( 'register_page' );

	if ( $register_page_id ) {
		return get_permalink( $register_page_id );
	}

	return $register_url;
}
add_filter( 'register_url', 'educator_register_url_filter' );

/**
 * Wrap oembed videos into responsive HTML container.
 *
 * @param string $html
 * @param string $url
 * @param array $attr
 * @param int $post_id
 * @return string
 */
function educator_wrap_oembed( $html, $url, $attr, $post_id ) {
	return '<div class="video-container">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'educator_wrap_oembed', 10, 4 );

/**
 * Page title.
 *
 * @param string $title
 * @param string $sep
 * @return string
 */
function educator_theme_wp_title( $title, $sep ) {
	if ( empty( $title ) && ( is_home() || is_front_page() ) ) {
		return __( 'Home', 'ib-educator' );
	}

	return $title;
}
add_filter( 'wp_title', 'educator_theme_wp_title', 10, 2 );

if ( ! function_exists( 'educator_theme_lecturer_link' ) ) :
/**
 * Get the lecturer profile URL.
 *
 * @param int $author_id
 * @return string
 */
function educator_theme_lecturer_link( $author_id ) {
	$link = get_author_posts_url( $author_id );

	if ( 'courses' == get_theme_mod( 'lecturer_link', 'courses' ) ) {
		if ( get_option( 'permalink_structure' ) ) {
			$link = untrailingslashit( $link ) . '/view/courses/';
		} else {
			$link = add_query_arg( 'view', 'courses', $link );
		}
	}

	return $link;
}
endif;

/**
 * Manage custom plugins and theme update notifications.
 */
if ( is_admin() && current_user_can( 'install_plugins' ) ) {
	/**
	* Check incrediblebytes.com if there is a theme update available.
	*/
	function educator_theme_updates_notifier() {
		if ( 1 == get_theme_mod( 'updates_notifier', 0 ) ) {
			require_once get_template_directory() . '/include/ib-update-notifier.php';

			IB_Update_Notifier::init(
				'http://update.incrediblebytes.com/ib-educator.xml',
				IB_THEME_VERSION,
				array(
					'update'       => __( '<strong>An update to the theme is available!</strong> The version of installed theme is %s. The latest version is %s.', 'ib-educator' ),
					'server_error' => __( 'Could not check for the theme update.', 'ib-educator' ),
				)
			);
		}
	}
	add_action( 'admin_init', 'educator_theme_updates_notifier' );

	require get_template_directory() . '/include/plugins.php';
}
