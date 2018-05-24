<?php
/**
 * IB Update Notifier.
 * Check for theme updates.
 *
 * @version 1.1
 */
class IB_Update_Notifier {
	protected static $url;
	protected static $current_version;
	protected static $messages;
	protected static $note;

	/**
	 * Initialize.
	 *
	 * @param string $url
	 * @param string $current_version
	 * @param string $text_domain
	 */
	public static function init( $url, $current_version, $messages ) {
		self::$url = $url;
		self::$current_version = $current_version;
		self::$messages = $messages;
		self::notify();
	}

	/**
	 * Check for the theme update.
	 * Notify the user if there is an update available.
	 */
	public static function notify() {
		$note = get_option( 'ib_update_notifier' );

		if ( ! is_object( $note ) ) {
			$note = new stdClass();
			$note->checked = 0;
			$note->version = 0;
			$note->error = 0;
		}

		if ( ! $note->checked || ( time() - $note->checked ) > 10800 ) {
			$response = wp_remote_get( self::$url );

			if ( ! is_wp_error( $response ) ) {
				$response_xml = simplexml_load_string( $response['body'] );

				if ( $response_xml ) {
					if ( isset( $response_xml->version ) ) {
						$note->version = (string) $response_xml->version;
					} else {
						$note->version = 0;
					}

					$note->error = 0;
				} else {
					$note->error = 1;
				}
			} else {
				$note->error = 1;
			}

			$note->checked = time();
			update_option( 'ib_update_notifier', $note );
		}

		if ( 1 == $note->error ) {
			add_action( 'admin_notices', array( __CLASS__, 'server_error_notice' ) );
		}

		if ( version_compare( self::$current_version, $note->version, '<' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'update_notice' ) );
		}

		self::$note = $note;
	}

	/**
	 * Display theme update notice.
	 */
	public static function update_notice() {
		$message = sprintf(
			self::$messages['update'],
			esc_html( self::$current_version ),
			esc_html( self::$note->version )
		);

		echo '<div class="updated"><p>' . $message . '</p>';
		do_action( 'ib_update_notifier_instructions' );
		echo '</div>';
	}

	/**
	 * Notify the user about failures to connect to the update notification server.
	 */
	public static function server_error_notice() {
		$message = self::$messages['server_error'];
		echo '<div class="updated error"><p>' . $message . '</p></div>';
	}
}