<?php
/**
 * Very basic helper class to work with WordPress admin notices.
 *
 * Ian Dunn's admin notice helper inspired this.
 * Norcross' fork of a notice helper by Stéphan Zych also informed some of this.
 *
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
 * @link https://make.wordpress.org/core/2015/04/23/spinners-and-dismissible-admin-notices-in-4-2/
 * @Link https://github.com/iandunn/admin-notice-helper
 * @link https://gist.github.com/norcross/813ad101bdef69dc306acb11f64eb7c7#file-admin-notice-class-php
 *
 * @package TwisterMC_BB_Modules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TwisterMC_BB_Notices
 */
class TwisterMC_BB_Notices {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @var null|\TwisterMC_BB_Notices $_instance
	 */
	protected static $_instance = null;

	/**
	 * An array of all our admin notices.
	 *
	 * Example array structure...
	 *     $this->notices = array(
	 *         'my-info-notice'    => array(
	 *             'id'             => 'my-info-notice',
	 *             'message'        => '<p>This is a test info notice!</p>',
	 *             'type'           => 'info',
	 *             'is_dismissible' => true,
	 *             'script'         => 'jQuery(document).on("click", ... });',
	 *             'save_notice'    => true,
	 *         ),
	 *         'my-warning-notice' => array(
	 *             'id'             => 'my-warning-notice',
	 *             'message'        => '<p>This is a test warning notice!</p>',
	 *             'type'           => 'warning',
	 *             'is_dismissible' => false,
	 *             'script'         => '',
	 *             'save_notice'    => false,
	 *         ),
	 *     );
	 *
	 * @var array $notices
	 */
	protected $notices = array();

	/**
	 * Flag for updates to notices array.
	 *
	 * This flag is used to determine if we need to print notices, and save notices on shutdown.
	 *
	 * @var bool $notices_were_updated
	 */
	protected $notices_were_updated = false;

	/**
	 * TwisterMC_BB_Notices dummy constructor.
	 *
	 * On construct, no hooks run.
	 * You have to explicitly call $instance->setup_hooks() on the first run.
	 */
	public function __construct() {}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * On get_instance, no hooks run.
	 * You have to explicitly call $instance->setup_hooks() on the first run.
	 *
	 * @return \TwisterMC_BB_Notices The main instance.
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add hooks for our notices library to function properly.
	 */
	public function setup_hooks() {

		/**
		 * Fires after WordPress has finished loading but before any headers are sent.
		 *
		 * @action init
		 *
		 * @link https://developer.wordpress.org/reference/hooks/init/
		 */
		add_action( 'init', array( $this, 'init' ), 0 );

		/**
		 * Print admin screen notices.
		 *
		 * @action admin_notices
		 *
		 * @link https://developer.wordpress.org/reference/hooks/admin_notices/
		 */
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		/**
		 * Enqueue scripts for admin pages.
		 *
		 * @action admin_enqueue_scripts
		 *
		 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		/**
		 * Fires just before PHP shuts down execution.
		 *
		 * @action shutdown
		 *
		 * @link https://developer.wordpress.org/reference/hooks/shutdown/
		 */
		add_action( 'shutdown', array( $this, 'shutdown' ) );

		/**
		 * Fires authenticated Ajax actions for logged-in users.
		 *
		 * The dynamic portion of the hook name, $_REQUEST['action'],
		 * refers to the name of the Ajax action callback being fired.
		 *
		 * @action wp_ajax_{$_REQUEST[‘action’]}
		 *
		 * @link https://developer.wordpress.org/reference/hooks/wp_ajax__requestaction/
		 */
		add_action( 'wp_ajax_tmcbbm_dismiss_notice', array( $this, 'tmcbbm_dismiss_notice' ) );
	}

	/**
	 * Init hook.
	 */
	public function init() {

		/**
		 * Out of the gate, we don't know about any notices.
		 * Set the notices_were_updated flag to false.
		 */
		$this->notices_were_updated = false;

		$this->initialize_notices();

		/**
		 * Now we know we have notices.
		 * Set the notices_were_updated flag to true,
		 * indicating that we need to print notices.
		 */
		if ( ! empty( $this->notices ) ) {
			$this->notices_were_updated = true;
		}
	}

	/**
	 * Print admin screen notices.
	 */
	public function admin_notices() {

		if ( empty( $this->notices ) ) {
			return;
		}

		foreach ( $this->notices as $id => $notice ) {
			$this->print_notice( $notice );
		}

		$this->notices_were_updated = true;
	}

	/**
	 * Enqueue admin JS for notices.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		/**
		 * If we don't have any notices, we don't need to enqueue anything.
		 */
		if ( empty( $this->notices ) ) {
			return;
		}

		/**
		 * Our "dismiss" handler.
		 */
		wp_enqueue_script(
			'tmcbmm-notices',
			TMCBBM_URL . 'assets/js/tmcbmm-notices.js',
			array(
				'jquery',
			),
			'1.0.0',
			true
		);
	}

	/**
	 * Shutdown hook.
	 */
	public function shutdown() {

		/**
		 * Notices were not updated, bail.
		 */
		if ( ! $this->notices_were_updated ) {
			return;
		}

		$save_notices = array();

		foreach ( $this->notices as $id => $notice ) {
			if ( true === $notice['save_notice'] ) {
				$save_notices[ $id ] = $notice;
			}
		}

		/**
		 * Notices don't need to be saved for future display state, bail.
		 */
		if ( empty( $save_notices ) ) {
			return;
		}

		$this->save_notices_to_options( $save_notices );
	}

	/**
	 * Initialize notices.
	 *
	 * Initializes $this->notices based on what's stored in options.
	 */
	public function initialize_notices() {

		/**
		 * Get any saved notices from options.
		 */
		$option_notices = $this->get_option_notices();

		/**
		 * If we have saved notices, merge them into this object's in memory notices.
		 */
		if ( ! empty( $option_notices ) ) {
			$this->notices = array_merge(
				$this->notices,
				$option_notices
			);
		}
	}

	/**
	 * Get all known notices.
	 *
	 * @return array
	 */
	public function get_notices() {
		return $this->notices;
	}

	/**
	 * Get the stored notices from options.
	 *
	 * @return array
	 */
	public function get_option_notices() {

		$option_notices = get_option( 'tmcbmm_notices', '' );

		if ( empty( $option_notices ) ) {
			return array();
		}

		return json_decode( $option_notices, true );
	}

	/**
	 * Dismiss notice AJAX handler.
	 */
	public function tmcbbm_dismiss_notice() {

		$notice_id = filter_input( INPUT_POST, 'notice_id', FILTER_SANITIZE_STRING );

		if ( empty( $notice_id ) ) {
			return;
		}

		$this->remove_notice( $notice_id );
		$this->remove_notice_from_options( $notice_id );
	}

	/**
	 * Remove a notice.
	 *
	 * @param string $notice_id A notice id.
	 */
	public function remove_notice( $notice_id = '' ) {

		if ( empty( $notice_id ) ) {
			return;
		}

		if ( empty( $this->notices[ $notice_id ] ) ) {
			return;
		}

		unset( $this->notices[ $notice_id ] );

		$this->notices_were_updated = true;
	}

	/**
	 * Save array of notices back to options.
	 *
	 * @param array $notices An array of notices.
	 */
	public function save_notices_to_options( $notices = array() ) {

		$save_notices = array();

		/**
		 * Only save the notices that have the 'save_notice' key.
		 */
		foreach ( $notices as $id => $notice ) {
			if ( true === $notice['save_notice'] ) {
				$save_notices[ $id ] = $notice;
			}
		}

		update_option( 'tmcbmm_notices', wp_json_encode( (array) $save_notices ), false );
	}

	/**
	 * Remove a notice from the options.
	 *
	 * @param string $notice_id A notice id.
	 */
	public function remove_notice_from_options( $notice_id = '' ) {

		/**
		 * Empty id.
		 */
		if ( empty( $notice_id ) ) {
			return;
		}

		$option_notices = $this->get_option_notices();

		/**
		 * Key does not exist in options.
		 */
		if ( empty( $option_notices[ $notice_id ] ) ) {
			return;
		}

		/**
		 * Unset the notice from the array.
		 */
		unset( $option_notices[ $notice_id ] );

		$this->notices_were_updated = true;

		/**
		 * Save the updated array.
		 */
		$this->save_notices_to_options( $option_notices );
	}

	/**
	 * Print a single admin notice.
	 *
	 * @param array $notice An admin notice.
	 */
	public function print_notice( $notice = array() ) {

		$class = ! empty( $notice['is_dismissible'] )
			? 'tmcbmm-notice notice notice-' . $notice['type'] . ' is-dismissible'
			: 'tmcbmm-notice notice notice-' . $notice['type'];

		printf(
			'<div id="%1$s" class="%2$s" data-id="%3$s">%4$s</div>',
			esc_attr( $notice['id'] ),
			esc_attr( $class ),
			esc_attr( $notice['id'] ),
			wp_kses( $notice['message'], wp_kses_allowed_html( 'post' ) )
		);
	}

	/**
	 * Add a message to the notice list.
	 *
	 * @param string $id             Required. ID for the notice. A non-unique ID will overwrite a notice with the same ID.
	 * @param string $message        Required. The admin message to display.
	 * @param string $type           Optional. Type of notice. Valid values are 'info', 'success', 'warning', or 'error'. Defaults to 'info'.
	 * @param bool   $is_dismissible Optional. Whether the notice is dismissable. Defaults to true.
	 * @param bool   $save_notice    Optional. Saves the notice to options for persistence. Defaults to true.
	 * @param string $script         Optional. JS to run. Defaults to empty string. Not yet implemented.
	 *
	 * @return array The notice array.
	 */
	public function add_notice( $id, $message, $type = 'info', $is_dismissible = true, $save_notice = true, $script = '' ) {

		$notice = array(
			'id'             => (string) $id,
			'message'        => (string) $message,
			'type'           => (string) $type,
			'is_dismissible' => (bool) $is_dismissible,
			'script'         => (string) $script,
			'save_notice'    => (bool) $save_notice,
		);

		$this->notices[ $notice['id'] ] = $notice;

		$this->notices_were_updated = true;

		return $notice;
	}

	/**
	 * Get a notice by it's ID.
	 *
	 * @param string $notice_id A notice id.
	 *
	 * @return array
	 */
	public function get_notice( $notice_id = '' ) {

		if ( empty( $notice_id ) ) {
			return array();
		}

		if ( empty( $this->notices[ $notice_id ] ) ) {
			return array();
		}

		return $this->notices[ $notice_id ];
	}

	/**
	 * Conditional check if the given notice ID is in the notices array.
	 *
	 * @param string $notice_id A notice id.
	 *
	 * @return bool
	 */
	public function has_notice( $notice_id = '' ) {

		$notice = $this->get_notice( $notice_id );

		if ( empty( $notice ) ) {
			return false;
		}

		return true;
	}
}
