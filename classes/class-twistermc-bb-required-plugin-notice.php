<?php
/**
 * TwisterMc BB Required Plugin Notice class.
 *
 * @package TwisterMC_BB_Modules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TwisterMC_BB_Required_Plugin_Notice
 */
class TwisterMC_BB_Required_Plugin_Notice {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @var null|\TwisterMC_BB_Required_Plugin_Notice $_instance
	 */
	protected static $_instance = null;

	/**
	 * TwisterMC_BB_Required_Plugin_Notice dummy constructor.
	 *
	 * On construct, no hooks run.
	 * You have to explicitly call $instance->setup_hooks() on the first run.
	 */
	public function __construct() {}

	/**
	 * Get instance of this class.
	 *
	 * On get_instance, no hooks run.
	 * You have to explicitly call $instance->setup_hooks() on the first run.
	 *
	 * @return \TwisterMC_BB_Required_Plugin_Notice
	 */
	public static function get_instance() {

		if ( ! empty( self::$_instance ) ) {
			return self::$_instance;
		}

		self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * Add hooks for our Beaver Builder required notice.
	 */
	public function setup_hooks() {

		/**
		 * No Beaver Builder. We need to notify admin and return early to prevent further execution.
		 */
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Conditional check if Beaver Builder is available.
	 *
	 * @return bool
	 */
	public static function is_bb_available() {

		if ( ! class_exists( 'FLBuilder' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add our plugin to the recently_activated plugins list.
	 *
	 * This effectively removes the "Plugin activated." admin notice.
	 */
	public function remove_plugin_activated_notice() {

		$insert = array(
			TMCBBM_BASENAME => time(),
		);

		if ( ! is_network_admin() ) {
			update_option( 'recently_activated', ( $insert + (array) get_option( 'recently_activated' ) ) );
		} else {
			update_site_option( 'recently_activated', ( $insert + (array) get_site_option( 'recently_activated' ) ) );
		}
	}

	/**
	 * Get the array for displaying an admin notice when Beaver Builder is not activated.
	 *
	 * @return array
	 */
	public function get_bb_inactive_notice() {
		return array(
			'id'             => 'tmcbbm-bb-inactive',
			'message'        => trim(
				wpautop(
					wptexturize(
						__(
							'Error: Beaver Builder is not installed or not activated. Beaver Builder is required to use TwisterMc BB Modules.',
							'tmcbbm'
						)
					)
				)
			),
			'type'           => 'error',
			'is_dismissable' => false,
			'save_notice'    => false,
		);
	}

	/**
	 * Add an error notice to the WP-Admin if Beaver Builder is not active.
	 */
	public function notify_admin_bb_inactive() {

		$notice = $this->get_bb_inactive_notice();

		$tmcbbm_notices = TwisterMC_BB_Notices::get_instance();

		if ( $tmcbbm_notices->has_notice( $notice['id'] ) ) {
			return;
		}

		$tmcbbm_notices->add_notice(
			$notice['id'],
			$notice['message'],
			$notice['type'],
			$notice['is_dismissable'],
			$notice['save_notice']
		);
	}

	/**
	 * Remove the BB inactive notice.
	 */
	public function remove_bb_inactive_notice() {

		$notice = $this->get_bb_inactive_notice();

		$tmcbbm_notices = TwisterMC_BB_Notices::get_instance();

		if ( ! $tmcbbm_notices->has_notice( $notice['id'] ) ) {
			return;
		}

		$tmcbbm_notices->remove_notice( $notice['id'] );

		$tmcbbm_notices->remove_notice_from_options( $notice['id'] );
	}

	/**
	 * Actions to run when Beaver Builder is not available.
	 */
	public function init() {

		/**
		 * Remove the "Plugin activated" admin notice.
		 */
		add_action( 'init', array( $this, 'remove_plugin_activated_notice' ), 11 );

		/**
		 * Add our own notice indicating that a required plugin is not active.
		 *
		 * We *could* disable our own plugin after this,
		 * or we *could* leave our plugin active,
		 * but not call anything from BB (which would trigger a fatal error).
		 */
		add_action( 'init', array( $this, 'notify_admin_bb_inactive' ), 11 );

		/*
		 * This shows how we *could* deactivate our plugin at this point in execution.
		 *
		 * If we deactivate our plugin here, the admin notice that BB is required will display exactly one time.
		 * The admin error notice would not be displayed on subsequent wp-admin reloads/visits if we did this,
		 * since the code for displaying that notice is part of this plugin.
		 *
		 * Example...
		 *     add_action( 'admin_init', array( $this, 'force_deactivate' ) );
		 *     // Prevent trying to activate again on page reload.
		 *     if ( isset( $_GET['activate'] ) ) {
		 *         unset( $_GET['activate'] );
		 *     }
		 */
	}

	/**
	 * Force deactivate the plugin.
	 */
	public function force_deactivate() {
		deactivate_plugins( TMCBBM_BASENAME );
	}
}
