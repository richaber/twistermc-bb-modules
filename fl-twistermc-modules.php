<?php
/**
 * Plugin Name: TwisterMc BB Modules
 * Plugin URI: https://www.twistermc.com
 * Description: Custom modules to extend BeaverBuilder
 * Version: 0.6.6
 * Author: TwisterMc
 * Author URI: http://www.twistermc.com
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
 * Load our plugin's custom modules.
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
 * Adds video attributes query strings to embedded YouTube videos
 */
function tmcbb_oembed_result( $html, $url, $args ) {
	return str_replace( '?feature=oembed', '?feature=oembed&loop=1&controls=0&showinfo=0&rel=0&enablejsapi=1', $html );
}

add_filter( 'oembed_result', 'tmcbb_oembed_result', 10, 3 );

/**
 * Adds video attributes query strings to embedded Vimeo videos
 */
function tmcbb_add_video_args( $provider, $url, $args ) {
	if ( strpos( $provider, '//vimeo.com/' ) !== false ) {
		$args     = array(
			'title'       => 0,
			'byline'      => 0,
			'portrait'    => 0,
			'badge'       => 0,
			//'autoplay' => 1,
			'loop'        => 1,
			'transparent' => 0,
		);
		$provider = add_query_arg( $args, $provider );
	}
	return $provider;
}

add_filter( 'oembed_fetch_url', 'tmcbb_add_video_args', 10, 3 );
