<?php
/**
 * BBSlickSlider "frontend JS" file.
 *
 * Used by Beaver Builder to generate frontend JavaScript that will be applied to individual module instances.
 * NOTE: Try not to mix in too much PHP with the JS, it's too confusing.
 *
 * @see     \BBSlickSlider
 *
 * @var \BBSlickSlider $module   An instance of the module class.
 * @var string         $id       The module's node ID ( i.e. $module->node ).
 * @var stdClass       $settings The module's settings ( i.e. $module->settings ).
 *
 * @package TwisterMcBBModules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * If we don't have any slides, echo an empty string and return.
 * BB expects some kind of output for building caches and such.
 * If you return without any kind of output, it will cause a fatal error.
 */
if ( ! $module->has_slides() ) {

	echo '';

	return;
}

// @codingStandardsIgnoreStart

?>

(function( $ ) {

	/**
	 * The node ID for this slider.
	 *
	 * @type {string}
	 */
	var slickNode = '<?php echo esc_attr( $id ) ?>';

	/**
	 * Whether this module is configured to autoplay videos.
	 *
	 * @type {boolean}
	 */
	var slickAutoplayVideos = <?php echo esc_attr( $settings->autoplay_videos ); ?>;

	/**
	 * The Slick Slider settings object.
	 *
	 * @type {Object}
	 */
	var slickSettings = <?php $module->the_slick_settings_object(); ?>;

	/**
	 * Slider autoplay carousel flag.
	 *
	 * This is the original autoplay setting, before we apply any 'pausing' in init.
	 *
	 * @type {boolean}
	 */
	var slickSettingsAutoplay = slickSettings.autoplay;

	/**
	 * Slide data.
	 *
	 * @type {Object}
	 */
	var slickSlides = <?php $module->the_slick_slides_object(); ?>;

	/**
	 * Builder Interface active flag.
	 *
	 * @type {boolean}
	 */
	var builderActive = jQuery( 'body' ).hasClass( 'fl-builder-edit' );

	/**
	 * The target wrapper for this slider.
	 *
	 * @type {string}
	 */
	var slickSlider = jQuery( '.fl-node-' + slickNode + ' .slickWrapper_bb' );

	/**
	 * The target for this slider's pause button.
	 *
	 * @type {string}
	 */
	var pauseButton = jQuery( '.fl-node-' + slickNode + ' .js-slickModule_bb_Pause' );

	/**
	 * Video API load delay.
	 *
	 * @type {number}
	 */
	var videoApiLoadingDelay = 2000;

	/**
	 * Video API load delay.
	 *
	 * @type {number}
	 */
	var resumeAutoplayDelay = 1000;

	/**
	 * Type of the first slide.
	 *
	 * @type {string}
	 */
	var firstSlideType = slickSlides[0].type;

	/**
	 * There's a slight delay before YouTube APIs are "ready".
	 */
	if ( true !== builderActive && true === slickAutoplayVideos && true === slickSettingsAutoplay && 'youtube' === firstSlideType ) {
		slickSettings.autoplay = false;
	}

	/**
	 * Change the pause button appearance on load.
	 */
	if ( true !== slickSettingsAutoplay ) {
		pauseButton.addClass( 'paused' );
		pauseButton.addClass( 'fa-play-circle' );
		pauseButton.removeClass( 'fa-pause-circle' );
	}

	/**
	 * Bind the init Slick event handler before calling slick.
	 */
	slickSlider.on( 'init', function( event, slick ) {

		/**
		 * Do not autoplay videos while the builder is active, or if the slider was not configured for autoplaying videos.
		 */
		if ( true === builderActive || true !== slickAutoplayVideos ) {
			return;
		}

		var slide = slick.$slides[ 0 ];

		var iframes = jQuery( slide ).find( 'iframe' );

		var iframe = iframes[ 0 ];

		if ( 'vimeo' === firstSlideType ) {
			playVimeo( iframe );
		}

		if ( 'youtube' === firstSlideType ) {

			/**
			 * Wait a couple seconds to make sure the YouTube API has finished loading to autoplay the video.
			 * YouTube API appears to be significantly slower than Vimeo API.
			 */
			setTimeout(
				function() {

					playYouTube( iframe );

					/**
					 * If the slider itself was set to autoplay, reset that option and resume slider playing.
					 */
					if ( true === slickSettingsAutoplay ) {

						setTimeout(

							function() {
								slick.setOption( 'autplay', true );
								slick.slickPlay();
							},
							resumeAutoplayDelay
						);
					}
				},
				videoApiLoadingDelay
			);
		}
	} );

	/**
	 * Bind the beforeChange Slick event handler.
	 */
	slickSlider.on( 'beforeChange', function( event, slick, currentSlide, nextSlide ) {

		var slide = slick.$slides[ currentSlide ];

		var type = jQuery( slide ).data( 'type' );

		if ( 'youtube' !== type && 'vimeo' !== type ) {
			return;
		}

		var iframes = jQuery( slide ).find( 'iframe' );

		if ( !iframes.length ) {
			return;
		}

		var iframe = iframes[ 0 ];

		if ( 'youtube' === type ) {
			pauseYouTube( iframe );
		}

		if ( 'vimeo' === type ) {
			pauseVimeo( iframe );
		}

	} );

	/**
	 * Bind the afterChange Slick event handler.
	 */
	slickSlider.on( 'afterChange', function( event, slick, currentSlide ) {

		/**
		 * Do not autoplay videos while the builder is active, or if the slider was not configured for autoplaying videos.
		 */
		if ( true === builderActive || true !== slickAutoplayVideos ) {
			return;
		}

		var slide = slick.$slides[ currentSlide ];

		var type = jQuery( slide ).data( 'type' );

		if ( 'youtube' !== type && 'vimeo' !== type ) {
			return;
		}

		var iframes = jQuery( slide ).find( 'iframe' );

		if ( !iframes.length ) {
			return;
		}

		var iframe = iframes[ 0 ];

		if ( 'youtube' === type ) {
			playYouTube( iframe );
		}

		if ( 'vimeo' === type ) {
			playVimeo( iframe );
		}

	} );

	/**
	 * Run Slick.
	 */
	slickSlider.slick( slickSettings );

	/**
	 * Change the pause button appearance on click.
	 */
	pauseButton.on( 'click', function() {
		if ( pauseButton.hasClass( 'paused' ) ) {
			pauseButton.removeClass( 'paused' );
			pauseButton.removeClass( 'fa-play-circle' );
			pauseButton.addClass( 'fa-pause-circle' );
			slickSlider.slick( 'slickPlay' );
		} else {
			pauseButton.addClass( 'paused' );
			pauseButton.addClass( 'fa-play-circle' );
			pauseButton.removeClass( 'fa-pause-circle' );
			slickSlider.slick( 'slickPause' );
		}
	} );

	/**
	 * YouTube play handler.
	 *
	 * @param {Object} iframe
	 */
	function playYouTube( iframe ) {
		var player = tmcbbmYtPlayers[ iframe.id ];
		player.playVideo();
	}

	/**
	 * YouTube pause handler.
	 *
	 * @param {Object} iframe
	 */
	function pauseYouTube( iframe ) {
		var player = tmcbbmYtPlayers[ iframe.id ];
		player.pauseVideo();
	}

	/**
	 * Vimeo play handler.
	 *
	 * @param {Object} iframe
	 */
	function playVimeo( iframe ) {
		var player = new Vimeo.Player( iframe );
		player.play().catch( function( error ) {
			console.error( 'error playing the video:', error.name );
		} );
	}

	/**
	 * Vimeo pause handler.
	 *
	 * @param {Object} iframe
	 */
	function pauseVimeo( iframe ) {
		var player = new Vimeo.Player( iframe );
		player.pause().catch( function( error ) {
			console.error( 'error playing the video:', error.name );
		} );
	}

})( jQuery );
