<?php
/**
 * TwisterMc BB Modules Main plugin class.
 *
 * @package TwisterMC_BB_Modules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TwisterMC_BB_Modules
 */
class TwisterMC_BB_Modules {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @var null|\TwisterMC_BB_Modules $_instance
	 */
	protected static $_instance = null;

	/**
	 * TwisterMC_BB_Modules dummy constructor.
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
	 * @return \TwisterMC_BB_Modules
	 */
	public static function get_instance() {

		if ( ! empty( self::$_instance ) ) {
			return self::$_instance;
		}

		self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * Add hooks for our plugin.
	 */
	public function setup_hooks() {

		/**
		 * Load our text domain.
		 */
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		if ( TwisterMC_BB_Required_Plugin_Notice::is_bb_available() ) {
			$this->bb_available();

			/**
			 * Return early.
			 */
			return;
		}

		$this->bb_not_available();
	}

	/**
	 * Actions to run when Beaver Builder is available.
	 */
	public function bb_available() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Actions to run when Beaver Builder is not available.
	 */
	public function bb_not_available() {

		/**
		 * Get an instance of our BB required handling class.
		 *
		 * @var \TwisterMC_BB_Required_Plugin_Notice $bb_required
		 */
		$bb_required = TwisterMC_BB_Required_Plugin_Notice::get_instance();
		$bb_required->setup_hooks();
	}

	/**
	 * Init hook.
	 */
	public function init() {
		$this->load_bb_modules();
	}

	/**
	 * Setup the plugin text domain for gettext i18n/l10n.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'tmcbbm', false, TMCBBM_REL_DIR . 'languages/' );
	}

	/**
	 * Load our plugin's custom modules.
	 *
	 * @action init
	 */
	public function load_bb_modules() {

		/**
		 * By returning early here, we are preventing any further action.
		 * Our BB Modules will not be loaded, since they require that BB be active.
		 * Else we would throw a fatal error.
		 */
		if ( ! TwisterMC_BB_Required_Plugin_Notice::is_bb_available() ) {
			return;
		}

		require_once TMCBBM_DIR . 'modules/slick/class-bbslickslider.php';
		BBSlickSlider::register();

		require_once TMCBBM_DIR . 'modules/fullimage/class-bbfullimage.php';
		BBFullImage::register();
	}
}
