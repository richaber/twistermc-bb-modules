<?php
/**
 * Plugin Name: TwisterMc BB Modules
 * Plugin URI: https://www.twistermc.com
 * Description: Custom modules to extend BeaverBuilder
 * Version: 0.6.6
 * Author: TwisterMc
 * Author URI: http://www.twistermc.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  tmcbbm
 * Domain Path:  /languages
 *
 * @package TwisterMC_BB_Modules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The full path and filename of this file.
 *
 * Example: /webroot/wp-content/plugins/twistermc-bb-modules/fl-twistermc-modules.php
 *
 * @var string TMCBBM_FILE
 */
define( 'TMCBBM_FILE', __FILE__ );

/**
 * The full directory path to the main plugin file, with trailing slash.
 *
 * Example: /webroot/wp-content/plugins/twistermc-bb-modules/
 *
 * @var string TMCBBM_DIR
 */
define( 'TMCBBM_DIR', dirname( TMCBBM_FILE ) . '/' );

/**
 * The "basename" of this plugin.
 *
 * Example: twistermc-bb-modules/fl-twistermc-modules.php
 *
 * @var string TMCBBM_BASENAME
 */
define( 'TMCBBM_BASENAME', plugin_basename( TMCBBM_FILE ) );

/**
 * The relative path to this plugin directory, from WP_PLUGIN_DIR, with trailing slash.
 *
 * Example: twistermc-bb-modules/
 *
 * @var string TMCBBM_REL_DIR
 */
define( 'TMCBBM_REL_DIR', basename( TMCBBM_DIR ) . '/' );

/**
 * The full URL to the main plugin directory, with trailing slash.
 *
 * Example: http://example.com/wp-content/plugins/twistermc-bb-modules/
 *
 * @var string TMCBBM_URL
 */
define( 'TMCBBM_URL', plugins_url( '/', TMCBBM_FILE ) );

/**
 * Include our Admin Notices helper class.
 *
 * This is a framework for displaying admin notices.
 */
require_once( TMCBBM_DIR . 'classes/class-twistermc-bb-notices.php' );

/**
 * Include our Required Plugin Notice class.
 *
 * This is the class for adding a notice when Beaver Builder is not active.
 */
require_once( TMCBBM_DIR . 'classes/class-twistermc-bb-required-plugin-notice.php' );

/**
 * Include our DOMDocument helper class.
 *
 * This is a utility class to ease working with partial markup strings.
 */
require_once( TMCBBM_DIR . 'classes/class-twistermc-bb-domdocument-utility.php' );

/**
 * Include our Main Plugin class.
 *
 * This is the main class that drives our plugin, for loading BB modules, firing filter actions, etc.
 */
require_once( TMCBBM_DIR . 'classes/class-twistermc-bb-modules.php' );

/**
 * Run the plugin.
 *
 * Setup instances of the TwisterMC_BB_Notices and TwisterMC_BB_Modules classes.
 * Sets up the hooks for each to integrate with WordPress.
 */
function tmcbbm_run() {

	$tmcbbm_notices = TwisterMC_BB_Notices::get_instance();
	$tmcbbm_notices->setup_hooks();

	$tmcbb_modules = TwisterMC_BB_Modules::get_instance();
	$tmcbb_modules->setup_hooks();
}

add_action( 'plugins_loaded', 'tmcbbm_run', 0 );
