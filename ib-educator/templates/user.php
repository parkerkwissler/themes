<?php
/*
Template Name: User page
*/

get_header();
?>

<?php if ( ! is_user_logged_in() ) : ?>
<section class="section-content">
	<div class="container clearfix">
		<div class="short-fw-container">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php educator_page_title( 'page' ); ?>
			<div class="entry-content">
				<p>
					<?php _e( 'Please log in to view this page.', 'ib-educator' ); ?>
					<a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log In', 'ib-educator' ); ?></a>
				</p>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); return; ?>
<?php endif; ?>

<?php
$page_id = get_the_ID();
$pretty_permalinks = get_option( 'permalink_structure' );
$action = get_query_var( 'action' );
if ( 'profile' == $action ) $action = '';
$edu_settings = ib_edu_get_settings();
$page_ids = array();
$page_ids['profile_page'] = get_theme_mod( 'user_page' );
$page_keys = array( 'student_courses_page', 'user_membership_page', 'user_payments_page' );

foreach ( $edu_settings as $key => $value ) {
	if ( in_array( $key, $page_keys ) ) {
		$page_ids[ $key ] = $value;
	}
}

$pages = get_pages( array(
	'include'     => $page_ids,
	'post_status' => 'publish',
	'sort_order'  => 'ASC',
	'sort_column' => 'menu_order',
) );
$tabs = array();

if ( $pages ) {
	foreach ( $pages as $page ) {
		$tabs[] = array(
			'page_id' => $page->ID,
			'url'     => get_permalink( $page->ID ),
			'label'   => $page->post_title,
		);
	}
}

/**
 * @deprecated 1.2.0
 */
if ( empty( $page_ids['student_courses_page'] ) ) {
	$tabs[] = array(
		'page_id' => 0,
		'action'  => 'courses',
		'url'     => ib_edu_get_endpoint_url( 'action', 'courses', get_permalink( $page_ids['profile_page'] ) ),
		'label'   => __( 'Courses', 'ibeducator' ),
	);
}
?>

<section class="section-content">
	<div class="container clearfix">
		<div class="short-fw-container">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php educator_page_title( 'page' ); ?>
				
				<div class="entry-content">
					<div id="user-page">
						<div class="the-tabs">
							<ul>
								<?php
									foreach ( $tabs as $tab ) {
										$active = '';

										if ( ( $page_id == $tab['page_id'] && empty( $action ) ) || ( isset( $tab['action'] ) && $action == $tab['action'] ) ) {
											$active = ' class="active"';
										}

										echo '<li' . $active . '><a href="' . esc_url( $tab['url'] ) . '">' . esc_html( $tab['label'] ) . '</a></li>';
									}
								?>
							</ul>
						</div>

						<?php if ( 'courses' == $action && $page_id == $page_ids['profile_page'] ) : ?>

						<?php
							if ( function_exists( 'ib_edu_student_courses' ) ) {
								echo ib_edu_student_courses( array() );
							}
						?>

						<?php elseif ( $page_id == $page_ids['profile_page'] ) : ?>

						<?php
							$error_fields = array();

							if ( ! empty( $GLOBALS['ibeducator_errors'] ) ) {
								echo '<div class="ib-edu-message error"><ul>';

								foreach ( $GLOBALS['ibeducator_errors'] as $error ) {
									switch ( $error ) {
										case 'email_invalid':
											echo '<li>' . __( 'Invalid email', 'ib-educator' ) . '</li>';
											$error_fields['user_email'] = true;
											break;

										case 'email_exists':
											echo '<li>' . __( 'This email is already registered', 'ib-educator' ) . '</li>';
											$error_fields['user_email'] = true;
											break;

										case 'passwords_no_match':
											echo '<li>' . __( 'Passwords do not match', 'ib-educator' ) . '</li>';
											$error_fields['user_pass'] = true;
											$error_fields['user_pass_2'] = true;
											break;

										case 'short_password':
											echo '<li>' . __( 'Password must be at least 6 characters long', 'ib-educator' ) . '</li>';
											$error_fields['user_pass'] = true;
											break;
									}
								}

								echo '</ul></div>';
							} elseif ( isset( $GLOBALS['ibeducator_success'] ) && $GLOBALS['ibeducator_success'] ) {
								echo '<div class="ib-edu-message success">' . __( 'Profile has been updated.', 'ib-educator' ) . '</div>';
							}

							$form_action = get_permalink();

							if ( $pretty_permalinks ) {
								$form_action = untrailingslashit( $form_action ) . '/action/profile';
							} else {
								$form_action = add_query_arg( 'action', 'profile', $form_action );
							}

							$curauth = wp_get_current_user();
							$first_name = ! isset( $_POST['first_name'] ) ? $curauth->first_name : stripslashes( $_POST['first_name'] );
							$last_name = ! isset( $_POST['last_name'] ) ? $curauth->last_name : stripslashes( $_POST['last_name'] );
							$user_email = ! isset( $_POST['user_email'] ) ? $curauth->user_email : stripslashes( $_POST['user_email'] );
							$description = ! isset( $_POST['description'] ) ? $curauth->description : stripslashes( $_POST['description'] );
						?>

						<form class="ib-edu-form" action="<?php echo esc_url( $form_action ); ?>" method="post">
							<?php wp_nonce_field( 'educator_edit_profile', 'educator_edit_profile_nonce' ); ?>

							<div class="ib-edu-form-field">
								<label for="user-first-name"><?php _e( 'First Name', 'ib-educator' ); ?></label>

								<div class="ib-edu-form-control">
									<input type="text" id="user-first-name" name="first_name" value="<?php echo esc_attr( $first_name ); ?>">
								</div>
							</div>

							<div class="ib-edu-form-field">
								<label for="user-last-name"><?php _e( 'Last Name', 'ib-educator' ); ?></label>

								<div class="ib-edu-form-control">
									<input type="text" id="user-last-name" name="last_name" value="<?php echo esc_attr( $last_name ); ?>">
								</div>
							</div>

							<div class="ib-edu-form-field<?php if ( array_key_exists( 'user_email', $error_fields ) ) echo ' error'; ?>">
								<label for="user-email"><?php _e( 'Email', 'ib-educator' ); ?></label>

								<div class="ib-edu-form-control">
									<input type="email" id="user-email" name="user_email" value="<?php echo esc_attr( $user_email ); ?>">
								</div>
							</div>

							<div class="ib-edu-form-field">
								<label for="user-description"><?php _e( 'About', 'ib-educator' ); ?></label>

								<div class="ib-edu-form-control">
									<textarea name="description" id="user-description" cols="30" rows="4"><?php echo esc_textarea( $description ); ?></textarea>
								</div>
							</div>

							<div class="ib-edu-form-field<?php if ( array_key_exists( 'user_pass', $error_fields ) ) echo ' error'; ?>">
								<label for="user-pass"><?php _e( 'New Password', 'ib-educator' ); ?></label>

								<div class="ib-edu-form-control">
									<input type="password" id="user-pass" name="user_pass" autocomplete="off">
								</div>
							</div>

							<div class="ib-edu-form-field<?php if ( array_key_exists( 'user_pass_2', $error_fields ) ) echo ' error'; ?>">
								<label for="user-pass-2"><?php _e( 'Repeat New Password', 'ib-educator' ); ?></label>

								<div class="ib-edu-form-control">
									<input type="password" id="user-pass-2" name="user_pass_2">
								</div>
							</div>

							<div class="ib-edu-form-actions">
								<input type="hidden" name="redirect" value="<?php echo esc_url( $form_action ); ?>">
								<button type="submit" class="ib-edu-button"><?php _e( 'Update Profile', 'ib-educator' ); ?></button>
							</div>
						</form>

						<?php else : ?>

						<?php
							the_post();
							the_content();
						?>

						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>