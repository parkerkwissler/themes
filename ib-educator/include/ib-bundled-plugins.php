<?php

/**
 * @version 1.0.1
 */
class IB_Bundled_Plugins {
	/**
	 * @var array
	 */
	public static $plugins;

	/**
	 * @var string
	 */
	public static $theme_version;

	/**
	 * @var string
	 */
	public static $version_option_name;

	/**
	 * Initialize.
	 *
	 * @param array $plugins
	 * @param string $theme_version
	 * @param string $version_option_name
	 */
	public static function init( $plugins, $theme_version, $version_option_name ) {
		self::$plugins = $plugins;
		self::$theme_version = $theme_version;
		self::$version_option_name = $version_option_name;
		self::generate_plugins_file_paths();

		add_action( 'plugins_api', array( __CLASS__, 'check_info' ), 10, 3 );
		add_action( 'admin_init', array( __CLASS__, 'check_theme_version' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'update_plugins_transient' ) );
	}

	/**
	 * Generate file paths for plugins.
	 */
	public static function generate_plugins_file_paths() {
		foreach ( self::$plugins as $key => $plugin ) {
			self::$plugins[ $key ]['file_path'] = $plugin['slug'] . '/' . $plugin['slug'] . '.php';
		}
	}

	/**
	 * Modify update_plugins transient to notify WordPress
	 * about which custom plugins should be updated.
	 * 
	 * @param stdClass $transient
	 * @return stdClass
	 */
	public static function check_for_updates( $transient ) {
		if ( empty( $transient ) ) {
			$transient = new stdClass();
		}

		if ( ! isset( $transient->response ) ) {
			$transient->response = array();
		}

		$installed_plugins = get_plugins();

		foreach ( self::$plugins as $plugin ) {
			// Update only custom plugins.
			if ( ! isset( $plugin['plugin_type'] ) || 'bundled' != $plugin['plugin_type'] ) continue;

			// If plugin is not installed, it can't updated.
			if ( ! isset( $installed_plugins[ $plugin['file_path'] ] ) ) continue;

			// Check if there is a new version of this plugin available.
			if ( ! isset( $plugin['version'] ) || version_compare( $installed_plugins[ $plugin['file_path'] ]['Version'], $plugin['version'], '>=' ) ) continue;

			$transient->response[ $plugin['file_path'] ] = new stdClass();
			$transient->response[ $plugin['file_path'] ]->slug = $plugin['slug'];
			$transient->response[ $plugin['file_path'] ]->new_version = $plugin['version'];
			$transient->response[ $plugin['file_path'] ]->package = $plugin['source'];
		}

		return $transient;
	}

	/**
	 * Notify WordPress about new versions of custom plugins.
	 */
	public static function check_theme_version() {
		if ( ! current_user_can( 'update_plugins' ) ) return;

		$version = get_option( self::$version_option_name );

		if ( self::$theme_version != $version ) {
			// We need to trigger pre_set_site_transient_update_plugins filter.
			set_site_transient( 'update_plugins', get_site_transient( 'update_plugins' ) );
			update_option( self::$version_option_name, self::$theme_version );
		}
	}

	/**
	 * Notify WordPress about new versions of custom plugins when it checks for updates.
	 *
	 * @param stdClass $transient
	 * @return stdClass
	 */
	public static function update_plugins_transient( $transient ) {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		return self::check_for_updates( $transient );
	}

	/**
	 * Set info for custom plugins.
	 *
	 * @param bool|object $false
	 * @param string $action
	 * @param object $args
	 * @return bool|object
	 */
	public static function check_info( $false, $action, $arg ) {
		foreach ( self::$plugins as $plugin ) {
			// Only custom plugins.
			if ( ! isset( $plugin['plugin_type'] ) || 'bundled' != $plugin['plugin_type'] ) continue;

			if ( ! isset( $arg->slug ) || $plugin['slug'] != $arg->slug ) continue;

			$info = new stdClass();
			$info->name = $plugin['name'];
			$info->slug = $plugin['slug'];
			$info->version = $plugin['version'];
			$info->sections = array();
			$info->sections['description'] = ( ! empty( $plugin['description'] ) ) ? $plugin['description'] : '';

			return $info;
		}

		return $false;
	}
}