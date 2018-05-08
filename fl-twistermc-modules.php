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
 * Text Domain:  tmcbb
 * Domain Path:  /languages
 *
 * @package TwisterMcBBModules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The full directory path to the main plugin file, with trailing slash.
 *
 * @var string TMC_BB_DIR
 */
define( 'TMC_BB_DIR', dirname( __FILE__ ) . '/' );

/**
 * The full URL to the main plugin file, with trailing slash.
 *
 * @var string HCMCSEARCH_PLUGIN_URL
 */
define( 'TMC_BB_URL', plugins_url( '/', __FILE__ ) );

/**
 * The relative path to this plugin directory, from WP_PLUGIN_DIR, with trailing slash.
 *
 * @var string TMC_BB_REL_DIR
 */
define( 'TMC_BB_REL_DIR', basename( TMC_BB_DIR ) . '/' );

/**
 * Setup the plugin text domain for gettext i18n/l10n.
 */
function tmcbb_load_textdomain() {
	load_plugin_textdomain( 'tmcbb', false, TMC_BB_REL_DIR . 'languages/' );
}

add_action( 'init', 'tmcbb_load_textdomain', 0 );

/**
 * Load our plugin's custom modules.
 *
 * @action init
 */
function tmcbb_load_modules() {

	if ( ! class_exists( 'FLBuilder' ) ) {
		return;
	}

	require_once TMC_BB_DIR . 'slick/class-bbslickslider.php';
	require_once TMC_BB_DIR . 'fullImage/fullImage.php';
}

add_action( 'init', 'tmcbb_load_modules' );

/**
 * Adds video attributes query strings to embedded YouTube videos.
 *
 * @filter oembed_result
 *
 * @param string $data The returned oEmbed HTML.
 * @param string $url  URL of the content to be embedded.
 * @param array  $args Optional arguments, usually passed from a shortcode.
 *
 * @return string
 */
function tmcbb_oembed_result( $data, $url, $args ) {
	return str_replace( '?feature=oembed', '?feature=oembed&loop=1&controls=0&showinfo=0&rel=0&enablejsapi=1', $data );
}

add_filter( 'oembed_result', 'tmcbb_oembed_result', 10, 3 );

/**
 * Adds video attributes query strings to embedded Vimeo videos.
 *
 * @filter oembed_fetch_url
 *
 * @param string $provider URL of the oEmbed provider.
 * @param string $url      URL of the content to be embedded.
 * @param array  $args     Optional arguments, usually passed from a shortcode.
 *
 * @return string
 */
function tmcbb_add_video_args( $provider, $url, $args ) {

	if ( false === strpos( $provider, '//vimeo.com/' ) ) {
		return $provider;
	}

	/**
	 * Query args for Vimeo URL.
	 *
	 * @var array $query_args
	 */
	$query_args = array(
		'title'       => 0,
		'byline'      => 0,
		'portrait'    => 0,
		'badge'       => 0,
		'loop'        => 1,
		'transparent' => 0,
	);

	return add_query_arg( $query_args, $provider );
}

add_filter( 'oembed_fetch_url', 'tmcbb_add_video_args', 10, 3 );
