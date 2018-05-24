<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the course meta.
 *
 * @param int $course_id
 * @param array $include
 * @return string
 */
function educator_course_meta( $course_id = null, $include = null ) {
	if ( ! $course_id ) $course_id = get_the_ID();

	if ( ! $include ) {
		$include = array( 'lecturer', 'num_lessons', 'difficulty' );
	}

	$include = apply_filters( 'edutheme_course_meta_include', $include );

	$meta = apply_filters( 'pre_educator_course_meta', '', $course_id, $include );

	if ( ! empty( $meta ) ) {
		return $meta;
	}

	foreach ( $include as $item ) {
		switch ( $item ) {
			case 'lecturer':
				// Lecturer.
				$meta .= '<span class="author lecturer">' . sprintf( __( 'by %s', 'ib-educator' ), get_the_author() ) . '</span>';
				break;

			case 'num_lessons':
				// Number of lessons.
				$num_lessons = IB_Educator::get_instance()->get_num_lessons( $course_id );
				if ( $num_lessons ) $meta .= '<span class="num-lessons">' . sprintf( _n( '1 lesson', '%d lessons', $num_lessons, 'ib-educator' ), $num_lessons ) . '</span>';
				break;

			case 'difficulty':
				// Difficulty.
				$difficulty = ib_edu_get_difficulty( $course_id );

				if ( $difficulty ) {
					$meta .= '<span class="difficulty">' . esc_html( $difficulty['label'] ) . '</span>';
				}
				break;
		}
	}

	return apply_filters( 'edutheme_course_meta', $meta );
}

function educator_before_course_content() {
	// Output course featured image.
	$show_thumbnail = get_post_meta( get_the_ID(), '_educator_show_image', true );

	if ( 1 == $show_thumbnail && has_post_thumbnail() ) {
		echo '<div class="course-image">';
		the_post_thumbnail( 'ib-educator-main-column' );
		echo '</div>';
	}
}
add_action( 'ib_educator_before_course_content', 'educator_before_course_content' );

function educator_format_price( $formatted, $currency, $price ) {
	if ( 0 == $price ) {
		return __( 'Free', 'ib-educator' );
	}

	return $formatted;
}
add_filter( 'ib_educator_format_price', 'educator_format_price', 10, 3 );

// Remove default stylesheet.
add_filter( 'ib_educator_stylesheet', '__return_false' );

// Remove default HTML actions.
remove_action( 'ib_educator_before_main_loop', array( 'IB_Educator_Main', 'action_before_main_loop' ) );
remove_action( 'ib_educator_after_main_loop', array( 'IB_Educator_Main', 'action_after_main_loop' ) );
remove_action( 'ib_educator_sidebar', array( 'IB_Educator_Main', 'action_sidebar' ) );
remove_action( 'ib_educator_before_course_content', array( 'IB_Educator_Main', 'before_course_content' ) );