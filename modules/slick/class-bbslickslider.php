<?php
/**
 * BBSlickSlider Class file.
 *
 * @package TwisterMcBBModules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BBSlickSlider
 */
class BBSlickSlider extends FLBuilderModule {

	/**
	 * Instance of the WP_oEmbed object.
	 *
	 * @var \WP_oEmbed
	 */
	public $oembed;

	/**
	 * Regex patterns for matching against oEmbed provider URLs.
	 *
	 * YouTube and Vimeo patterns are verbatim from WordPress Version 4.9.5 wp_video_shortcode() URL matching pattern.
	 *
	 * @see wp_video_shortcode()
	 *
	 * @var array $oembed_url_patterns Regex patterns for URL matching.
	 */
	protected $oembed_url_patterns = array(
		'youtube' => '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#',
		'vimeo'   => '#^https?://(.+\.)?vimeo\.com/.*#',
	);

	/**
	 * BBSlickSlider constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'name'          => __( 'Slick', 'tmcbb' ),
				'description'   => __( 'Slick Slider for BeaverBuilder', 'tmcbb' ),
				'category'      => __( 'Advanced Modules', 'tmcbb' ),
				'dir'           => TMCBBM_DIR . 'slick/',
				'url'           => TMCBBM_URL . 'slick/',
			)
		);

		$this->oembed = _wp_oembed_get_object();

		$this->add_css( 'font-awesome' );

		$this->add_css( 'slick-slider-css-cdn', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css', array(), '' );

		$this->add_js( 'slick-slider-js-cdn', TMCBBM_URL . '/slick/js/slick.js', array( 'jquery' ), '', false );
	}

	/**
	 * Enqueue Vimeo Helper Script
	 *
	 * @todo This should only be included if we have Vimeo modules.
	 */
	public function enqueue_scripts() {
		if ( $this->settings && 'video' === $this->settings->photoVideo ) {
			wp_enqueue_script( 'vimeo-helper', '//f.vimeocdn.com/js/froogaloop2.min.js', array(), '3', true );
		}
	}

	/**
	 * Register the module and its form settings.
	 *
	 * This is just a convenience wrapper so we only have to call BBSlickSlider::register.
	 * What's implied, but not specifically stated, is that Beaver Builder itself handles instantiation on demand.
	 * An unusual side effect of that approach, is that hooks in constructor appear to fire more than once.
	 *
	 * @uses \BBSlickSlider::register_settings_form(), \BBSlickSlider::register_module()
	 *
	 * @action init
	 */
	public static function register() {
		BBSlickSlider::register_settings_form();
		BBSlickSlider::register_module();
	}

	/**
	 * Register the module and it's form settings with Beaver Builder.
	 *
	 * @see \FLBuilderModel::register_module()
	 */
	public static function register_module() {
		FLBuilder::register_module(
			'BBSlickSlider',
			array(
				'media'         => array(
					'title'    => __( 'Slideshow Media', 'tmcbbm' ),
					'sections' => array(
						'general' => array(
							'title'  => __( 'Slides', 'tmcbbm' ),
							'fields' => array(
								'slides' => array(
									'type'         => 'form',
									'label'        => __( 'Slide', 'tmcbbm' ),
									'form'         => 'tmcbb_slickslider_slide',
									'preview_text' => 'slide_label',
									'multiple'     => true,
								),
							),
						),
					),
				),
				'media_settings' => array(
					'title'    => __( 'Media Settings', 'tmcbbm' ),
					'sections' => array(
						'general' => array(
							'title'  => __( 'Media Settings', 'tmcbbm' ),
							'fields' => array(
								'showCaptions'    => array(
									'type'    => 'select',
									'label'   => __( 'Show Image Captions?', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'forceImageSize'  => array(
									'type'    => 'select',
									'label'   => __( 'Force Images to Full Width?', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'autoplay_videos' => array(
									'type'    => 'select',
									'label'   => __( 'Auto play videos?', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
							),
						),
					),
				),
				'slider_settings' => array(
					'title'    => __( 'Slideshow Controls', 'tmcbbm' ),
					'sections' => array(
						'general' => array(
							'title'  => __( 'Slideshow Controls', 'tmcbbm' ),
							'fields' => array(
								'autoplay'         => array(
									'type'    => 'select',
									'label'   => __( 'Slideshow Auto Play', 'tmcbbm' ),
									'help'    => __( 'Enables Autoplay of the slider.', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
									'toggle'  => array(
										'true' => array(
											'fields' => array(
												'autoplaySpeed',
												'pauseOnHover',
											),
										),
									),
									'hide'    => array(
										'false' => array(
											'fields' => array(
												'pauseOnDotsHover',
											),
										),
									),
									'trigger' => array(
										'true' => array(
											'fields' => array(
												'dots',
											),
										),
									),
								),
								'autoplaySpeed'    => array(
									'type'        => 'text',
									'label'       => __( 'Auto Play Speed', 'tmcbbm' ),
									'help'        => __( 'Autoplay Speed in milliseconds.', 'tmcbbm' ),
									'default'     => '3000',
									'description' => __( 'milliseconds', 'tmcbbm' ),
								),
								'arrows'           => array(
									'type'    => 'select',
									'label'   => __( 'Show Arrows', 'tmcbbm' ),
									'help'    => __( 'Prev/Next Arrows.', 'tmcbbm' ),
									'default' => 'true',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'pauseOnHover'     => array(
									'type'    => 'select',
									'label'   => __( 'Pause on Hover', 'tmcbbm' ),
									'help'    => __( 'Pause Autoplay On Hover.', 'tmcbbm' ),
									'default' => 'true',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'dots'             => array(
									'type'    => 'select',
									'label'   => __( 'Show Dots', 'tmcbbm' ),
									'help'    => __( 'Show dot indicators.', 'tmcbbm' ),
									'default' => 'true',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
									'toggle'  => array(
										'true' => array(
											'fields' => array(
												'pauseOnDotsHover',
											),
										),
									),
								),
								'pauseOnDotsHover' => array(
									'type'    => 'select',
									'label'   => __( 'Pause on Dots Hover', 'tmcbbm' ),
									'help'    => __( 'Pause Autoplay when a dot is hovered.', 'tmcbbm' ),
									'default' => 'true',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'oneSlide'         => array(
									'type'    => 'select',
									'label'   => __( 'Show # Slides', 'tmcbbm' ),
									'help'    => __( 'This setting determines if a single slide, or multiple slides, will be viewable in the slider at once.', 'tmcbbm' ),
									'options' => array(
										'true'  => __( 'Single Item', 'tmcbbm' ),
										'false' => __( 'Multiple Items', 'tmcbbm' ),
									),
									'toggle'  => array(
										'true'  => array(
											'fields' => array(
												'adaptiveHeight',
											),
										),
										'false' => array(
											'fields' => array(
												'centerMode',
												'slidesToShow',
												'slidesToScroll',
											),
										),
									),
								),
								'adaptiveHeight'   => array(
									'type'    => 'select',
									'label'   => __( 'Adaptive Height', 'tmcbbm' ),
									'help'    => __( 'Enables adaptive height for single slide horizontal carousels.', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
									'toggle'  => array(
										'false' => array(
											'fields' => array(
												'fixedHeightSize',
											),
										),
									),
								),
								'fixedHeightSize'  => array(
									'type'        => 'text',
									'label'       => __( 'Fixed Height Size', 'tmcbbm' ),
									'default'     => '500',
									'description' => 'pixels',
								),
								'centerMode'       => array(
									'type'    => 'select',
									'label'   => __( 'Center Mode', 'tmcbbm' ),
									'help'    => __( 'Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts.', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'slidesToShow'     => array(
									'type'    => 'text',
									'label'   => __( 'Slides to Show', 'tmcbbm' ),
									'help'    => __( 'The number of slides to show.', 'tmcbbm' ),
									'default' => '1',
								),
								'slidesToScroll'   => array(
									'type'    => 'text',
									'label'   => __( 'Slides to Scroll', 'tmcbbm' ),
									'help'    => __( 'The number of slides to scroll.', 'tmcbbm' ),
									'default' => '1',
								),
								'variableWidth'    => array(
									'type'    => 'select',
									'label'   => __( 'Variable Width', 'tmcbbm' ),
									'help'    => __( 'Variable width slides.', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'infinite'         => array(
									'type'    => 'select',
									'label'   => __( 'Infinite Loop', 'tmcbbm' ),
									'help'    => __( 'Infinite loop sliding', 'tmcbbm' ),
									'default' => 'true',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
								'vertical'         => array(
									'type'    => 'select',
									'label'   => __( 'Orientation', 'tmcbbm' ),
									'help'    => __( 'Horizontal or Vertical layout', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Vertical', 'tmcbbm' ),
										'false' => __( 'Horizontal', 'tmcbbm' ),
									),
									'toggle'  => array(
										'false' => array(
											'fields' => array( 'fade' ),
										),
									),
								),
								'fade'             => array(
									'type'    => 'select',
									'label'   => __( 'Fade', 'tmcbbm' ),
									'help'    => __( 'Enable fade', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
								),
							),
						),
					),
				),
				'design'          => array(
					'title'    => __( 'Slideshow Design', 'tmcbbm' ),
					'sections' => array(
						'general' => array(
							'title'  => __( 'Tweak the Design', 'tmcbbm' ),
							'fields' => array(
								'arrowSize'                 => array(
									'type'        => 'text',
									'label'       => __( 'Arrow Size', 'tmcbbm' ),
									'default'     => '20',
									'description' => 'pixels',
								),
								'arrowColor'                => array(
									'type'    => 'color',
									'label'   => __( 'Arrow Color', 'tmcbbm' ),
									'default' => 'ffffff',
								),
								'arrowBackgroundColor'      => array(
									'type'       => 'color',
									'label'      => __( 'Arrow Background Color', 'tmcbbm' ),
									'default'    => '333333',
									'show_reset' => true,
								),
								'arrowHoverColor'           => array(
									'type'    => 'color',
									'label'   => __( 'Arrow Hover Color', 'tmcbbm' ),
									'default' => 'ffffff',
								),
								'arrowHoverBackgroundColor' => array(
									'type'       => 'color',
									'label'      => __( 'Arrow Hover Background Color', 'tmcbbm' ),
									'default'    => '689BCA',
									'show_reset' => true,
								),
								'dotSize'                   => array(
									'type'        => 'text',
									'label'       => __( 'Dot Size', 'tmcbbm' ),
									'default'     => '14',
									'description' => 'pixels',
								),
								'dotColor'                  => array(
									'type'    => 'color',
									'label'   => __( 'Dot Color', 'tmcbbm' ),
									'default' => '000000',
								),
								'dotBackgroundColor'        => array(
									'type'       => 'color',
									'label'      => __( 'Dot Background Color', 'tmcbbm' ),
									'show_reset' => true,
								),
								'dotActiveColor'            => array(
									'type'    => 'color',
									'label'   => __( 'Dot Active Color', 'tmcbbm' ),
									'default' => 'ffffff',
								),
								'dotActiveBackgroundColor'  => array(
									'type'       => 'color',
									'label'      => __( 'Dot Active Background Color', 'tmcbbm' ),
									'default'    => '333333',
									'show_reset' => true,
								),
								'dotHoverColor'             => array(
									'type'    => 'color',
									'label'   => __( 'Dot Hover Color', 'tmcbbm' ),
									'default' => 'ffffff',
								),
								'dotHoverBackgroundColor'   => array(
									'type'       => 'color',
									'label'      => __( 'Dot Hover Background Color', 'tmcbbm' ),
									'default'    => '689BCA',
									'show_reset' => true,
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Register the tmcbb_slickslider_slide subform.
	 *
	 * @see \FLBuilderModel::register_settings_form()
	 */
	public static function register_settings_form() {
		FLBuilder::register_settings_form(
			'tmcbb_slickslider_slide',
			array(
				'title' => __( 'Slide Settings', 'tmcbbm' ),
				'tabs'  => array(
					'general' => array(
						'title'    => __( 'General', 'tmcbbm' ),
						'sections' => array(
							'general'    => array(
								'title'  => '',
								'fields' => array(
									'slide_label' => array(
										'type'  => 'text',
										'label' => __( 'Slide Label', 'tmcbbm' ),
										'help'  => __( 'A label to identify this slide on the Slides tab of the Content Slider settings.', 'tmcbbm' ),
									),
								),
							),
							'slide_type' => array(
								'title'  => __( 'Slide Type', 'tmcbbm' ),
								'fields' => array(
									'slide_type_select' => array(
										'type'    => 'select',
										'label'   => __( 'Type', 'tmcbbm' ),
										'default' => 'image',
										'help'    => __( 'This setting is for choosing the slide type.', 'tmcbbm' ),
										'options' => array(
											'image' => __( 'Image', 'tmcbbm' ),
											'embed' => __( 'Youtube or Vimeo Video URL', 'tmcbbm' ),
										),
										'toggle'  => array(
											'image' => array(
												'fields' => array(
													'slide_image',
												),
											),
											'embed' => array(
												'fields' => array(
													'slide_embed',
												),
											),
										),
									),
									'slide_image'       => array(
										'type'  => 'photo',
										'label' => __( 'Photo', 'tmcbbm' ),
									),
									'slide_embed'       => array(
										'type'        => 'text',
										'label'       => __( 'Video Embed URL', 'tmcbbm' ),
										'placeholder' => __( 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'tmcbbm' ),
										'description' => __( 'The Youtube or Vimeo video must be publicly embeddable. "Private" videos will not embed.', 'tmcbbm' ),
									),
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Get the video ID from a YouTube or Vimeo video.
	 *
	 * @uses \BBSlickSlider::get_oembed_provider_name()
	 * @uses \BBSlickSlider::get_youtube_video_id(), \BBSlickSlider::get_vimeo_video_id()
	 *
	 * @param string $url URL of the content to be embedded.
	 *
	 * @return string
	 */
	public function get_embed_video_id( $url ) {

		$id = '';

		$provider = $this->get_oembed_provider_name( $url );

		switch ( $provider ) {
			case 'youtube':
				$id = $this->get_youtube_video_id( $url );
				break;
			case 'vimeo':
				$id = $this->get_vimeo_video_id( $url );
				break;
		}

		return $id;
	}

	/**
	 * Retrieve Video ID from supplied YouTube URL.
	 *
	 * Tested with the following YouTube Video URL formats:
	 * https://youtu.be/dQw4w9WgXcQ
	 * https://www.youtube.com/embed/dQw4w9WgXcQ
	 * https://www.youtube.com/watch?v=dQw4w9WgXcC
	 * https://www.youtube.com/?v=dQw4w9WgXcD
	 * https://www.youtube.com/v/dQw4w9WgXcE
	 * https://www.youtube.com/e/dQw4w9WgXcF
	 * https://www.youtube.com/user/username#p/u/11/dQw4w9WgXcG
	 * https://www.youtube.com/someyoutubepagename#p/c/54B8C800269D7C1B/0/dQw4w9WgXcH
	 * https://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcI
	 * https://www.youtube.com/?feature=player_embedded&v=dQw4w9WgXcJ
	 *
	 * @param string $url A YouTube Video URL in the supported format. Required.
	 *
	 * @return string|false The YouTube Video ID upon success. False on error.
	 */
	public function get_youtube_video_id( $url ) {

		$yt_pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';

		/** Determine if this is actually a YouTube URL and look for the YouTube VIDEO_ID */
		preg_match(
			$yt_pattern,
			$url,
			$matches
		);

		if ( empty( $matches[1] ) ) {
			return false;
		}

		return $matches[1];
	}

	/**
	 * Retrieve Video ID from supplied Vimeo URL.
	 *
	 * Tested with the following Vimeo Video URL formats:
	 * https://vimeo.com/157287763
	 * https://player.vimeo.com/video/157287763
	 * https://player.vimeo.com/video/157287763?title=0&amp;byline=0&amp;portrait=0
	 * https://vimeo.com/channels/staffpicks/157287763
	 *
	 * @param string $url A Vimeo Video URL in the supported format. Required.
	 *
	 * @return string|false The Vimeo Video ID upon success. False on error.
	 */
	public function get_vimeo_video_id( $url ) {

		$vimeo_pattern = '%(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*%i';

		/** Determine if this is actually a Vimeo URL and look for the Vimeo VIDEO_ID */
		preg_match(
			$vimeo_pattern,
			$url,
			$matches
		);

		if ( empty( $matches[1] ) ) {
			return false;
		}

		return $matches[1];
	}

	/**
	 * Get the supported oembed URL regex patterns.
	 *
	 * @return array
	 */
	public function get_oembed_url_patterns() {
		return $this->oembed_url_patterns;
	}

	/**
	 * Get the oembed provider name from a given oembed URL.
	 *
	 * @uses \BBSlickSlider::get_oembed_url_patterns()
	 *
	 * @param string $url URL of the content to be embedded.
	 *
	 * @return string
	 */
	public function get_oembed_provider_name( $url ) {

		foreach ( $this->get_oembed_url_patterns() as $provider_name => $oembed_url_pattern ) {

			if ( preg_match( $oembed_url_pattern, $url ) ) {
				return $provider_name;
			}
		}

		return '';
	}

	/**
	 * Get the default query args for adding to our oEmbed call.
	 *
	 * @uses \BBSlickSlider::is_vimeo_oembed_url(), \BBSlickSlider::get_vimeo_default_query_args()
	 * @uses \BBSlickSlider::is_youtube_oembed_url(), \BBSlickSlider::get_youtube_default_query_args()
	 *
	 * @param string $url URL of the content to be embedded. The original embed URL that was entered by user.
	 *
	 * @return array
	 */
	public function get_embed_video_default_query_args( $url = '' ) {
		if ( $this->is_vimeo_oembed_url( $url ) ) {
			return $this->get_vimeo_default_query_args();
		}
		if ( $this->is_youtube_oembed_url( $url ) ) {
			return $this->get_youtube_default_query_args();
		}

		return array();
	}

	/**
	 * Get the default query args for adding to our YouTube oEmbed call.
	 *
	 * @return array
	 */
	public function get_youtube_default_query_args() {
		return array(
			'controls'    => 0,
			'showinfo'    => 0,
			'loop'        => 1,
			'rel'         => 0,
			'enablejsapi' => 1,
		);
	}

	/**
	 * Get the default query args for adding to our Vimeo oEmbed call.
	 *
	 * @return array
	 */
	public function get_vimeo_default_query_args() {
		return array(
			'title'       => 0,
			'byline'      => 0,
			'portrait'    => 0,
			'badge'       => 0,
			'loop'        => 1,
			'transparent' => 0,
		);
	}

	/**
	 * Get the oembed regex pattern for matching against a URL.
	 *
	 * @uses \BBSlickSlider::get_oembed_url_patterns()
	 *
	 * @param string $provider The provider namme.
	 *
	 * @return string
	 */
	public function get_oembed_url_pattern_by_provider_name( $provider ) {

		if ( ! array_key_exists( $provider, $this->get_oembed_url_patterns() ) ) {
			return '';
		}

		// Array dereferencing syntax requires PHP 5.4+.
		return $this->get_oembed_url_patterns()[ $provider ];
	}

	/**
	 * Check if the given URL is from one of our supported oEmbed providers.
	 *
	 * @uses \BBSlickSlider::get_oembed_url_pattern_by_provider_name()
	 *
	 * @param string $url      URL to check against.
	 * @param string $provider Embed provider to check against.
	 *
	 * @return bool
	 */
	public function is_oembed_provider_url( $url, $provider = 'youtube' ) {

		if ( empty( $url ) || empty( $provider ) ) {
			return false;
		}

		$pattern = $this->get_oembed_url_pattern_by_provider_name( $provider );

		if ( empty( $pattern ) ) {
			return false;
		}

		$is_match = preg_match( $pattern, $url );

		if ( ! $is_match ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the given URL is a YouTube oembed URL.
	 *
	 * @uses \BBSlickSlider::is_oembed_provider_url()
	 *
	 * @param string $url URL to check against.
	 *
	 * @return bool
	 */
	public function is_youtube_oembed_url( $url = '' ) {
		return $this->is_oembed_provider_url( $url, 'youtube' );
	}

	/**
	 * Check if the given URL is a Vimeo oembed URL.
	 *
	 * @uses \BBSlickSlider::is_oembed_provider_url()
	 *
	 * @param string $url URL to check against.
	 *
	 * @return bool
	 */
	public function is_vimeo_oembed_url( $url = '' ) {
		return $this->is_oembed_provider_url( $url, 'vimeo' );
	}

	/**
	 * Conditional check to see if slides are set.
	 *
	 * @return bool
	 */
	public function has_slides() {

		if ( ! empty( $this->settings->slides ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Conditional check to see if slides of the given type exist.
	 *
	 * @uses \BBSlickSlider::has_slides(), \BBSlickSlider::get_slide_types()
	 *
	 * @param string $type A supported slide type string.
	 *
	 * @return bool
	 */
	public function has_slides_of_type( $type ) {

		if ( ! $this->has_slides() ) {
			return false;
		}

		$slide_types = $this->get_slide_types();

		if ( ! in_array( $type, $slide_types, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Conditional check to see if image slides exist.
	 *
	 * @uses \BBSlickSlider::has_slides_of_type()
	 *
	 * @return bool
	 */
	public function has_image_slides() {
		return $this->has_slides_of_type( 'image' );
	}

	/**
	 * Conditional check to see if video embed slides exist.
	 *
	 * @uses \BBSlickSlider::has_slides_of_type()
	 *
	 * @return bool
	 */
	public function has_embed_slides() {
		return $this->has_slides_of_type( 'embed' );
	}

	/**
	 * Get the slides from the module's settings.
	 *
	 * @uses \BBSlickSlider::has_slides()
	 *
	 * @return \stdClass[]|array An array of stdClass "slide" objects on success, else empty array.
	 */
	public function get_slides() {

		if ( $this->has_slides() ) {
			return $this->settings->slides;
		}

		return array();
	}

	/**
	 * Get the 'types' of slides that exist.
	 *
	 * Returns array of strings from 'slide_type_select'.
	 * Currently 'image' or 'embed'.
	 *
	 * @uses \BBSlickSlider::get_slides()
	 *
	 * @return array
	 */
	public function get_slide_types() {

		$slides = $this->get_slides();

		if ( empty( $slides ) ) {
			return array();
		}

		return wp_list_pluck( $slides, 'slide_type_select' );
	}

	/**
	 * Get the URLs of embed slides.
	 *
	 * @uses \BBSlickSlider::has_embed_slides(), \BBSlickSlider::get_slides()
	 *
	 * @return array
	 */
	public function get_embed_slides_urls() {

		if ( ! $this->has_embed_slides() ) {
			return array();
		}

		$slides = $this->get_slides();

		return wp_list_pluck( $slides, 'slide_embed' );
	}

	/**
	 * Conditional check if the provided slide is an image slide.
	 *
	 * @param \stdClass $slide A stdClass "slide" object to test.
	 *
	 * @return bool
	 */
	public function is_image_slide( $slide ) {

		if ( empty( $slide ) || empty( $slide->slide_type_select ) || 'image' !== $slide->slide_type_select ) {
			return false;
		}

		return true;
	}

	/**
	 * Conditional check if the provided slide is an ebmedded video slide.
	 *
	 * @param \stdClass $slide A stdClass "slide" object to test.
	 *
	 * @return bool
	 */
	public function is_embed_slide( $slide ) {

		if ( empty( $slide ) || empty( $slide->slide_type_select ) || 'embed' !== $slide->slide_type_select ) {
			return false;
		}

		return true;
	}

	/**
	 * Conditional check to see if the given slide is an oembed slide for the given provider.
	 *
	 * @uses \BBSlickSlider::is_embed_slide(), \BBSlickSlider::is_oembed_provider_url()
	 *
	 * @param stdClass $slide    A stdClass "slide" object to test.
	 * @param string   $provider A supported video embed provider.
	 *
	 * @return bool
	 */
	public function is_embed_provider_slide( $slide, $provider = 'youtube' ) {

		if ( empty( $slide ) || empty( $provider ) ) {
			return false;
		}

		if ( empty( $slide->slide_embed ) || ! $this->is_embed_slide( $slide ) ) {
			return false;
		}

		return $this->is_oembed_provider_url( $slide->slide_embed, $provider );
	}

	/**
	 * Conditional check if the provided slide is a YouTube oembed slide.
	 *
	 * @uses \BBSlickSlider::is_embed_provider_slide()
	 *
	 * @param stdClass $slide A stdClass "slide" object to test.
	 *
	 * @return bool
	 */
	public function is_youtube_slide( $slide ) {
		return $this->is_embed_provider_slide( $slide, 'youtube' );
	}

	/**
	 * Conditional check if the provided slide is a Vimeo oembed slide.
	 *
	 * @uses \BBSlickSlider::is_embed_provider_slide()
	 *
	 * @param stdClass $slide A stdClass "slide" object to test.
	 *
	 * @return bool
	 */
	public function is_vimeo_slide( $slide ) {
		return $this->is_embed_provider_slide( $slide, 'vimeo' );
	}

	/**
	 * Get the default Slick Slider arrow markup strings.
	 *
	 * @param string $vertical Whether to get the vertical or horizontal arrow strings. Valid values are 'false' or 'true' string.
	 *
	 * @return array
	 */
	public function get_slick_arrows( $vertical = 'false' ) {

		$template = '<button class="%1$s" aria-hidden="true"><span class="tmc_isVisibilyHidden">%2$s</span></button>';

		if ( empty( $vertical ) || 'false' === $vertical ) {
			return array(
				'nextArrow' => sprintf(
					$template,
					'fa fa-chevron-right slick-arrow slick-next',
					esc_html__( 'Next', 'tmcbbm' )
				),
				'prevArrow' => sprintf(
					$template,
					'fa fa-chevron-left slick-arrow slick-prev',
					esc_html__( 'Previous', 'tmcbbm' )
				),
			);
		}

		return array(
			'nextArrow' => sprintf(
				$template,
				'fa fa-chevron-down slick-arrow slick-next',
				esc_html__( 'Next', 'tmcbbm' )
			),
			'prevArrow' => sprintf(
				$template,
				'fa fa-chevron-up slick-arrow slick-prev',
				esc_html__( 'Previous', 'tmcbbm' )
			),
		);
	}

	/**
	 * Get a specific Slick Slider arrow markup string.
	 *
	 * @uses \BBSlickSlider::get_slick_arrows()
	 *
	 * @param string $arrow The desired arrow setting. Valid values are 'nextArrow' or 'prevArrow'.
	 * @param string $vertical Whether to get the vertical or horizontal arrow strings. Valid values are 'false' or 'true' string.
	 *
	 * @return string
	 */
	public function get_slick_arrow( $arrow = 'nextArrow', $vertical = 'false' ) {

		$arrows = $this->get_slick_arrows( $vertical );

		if ( ! empty( $arrows[ $arrow ] ) ) {
			return $arrows[ $arrow ];
		}

		return '';
	}

	/**
	 * Get an array of the default Slick slider settings.
	 *
	 * @return array
	 */
	public function get_slick_settings_defaults() {
		return array(
			'adaptiveHeight'   => 'false',
			'arrows'           => 'true',
			'autoplay'         => 'false',
			'autoplaySpeed'    => 3000,
			'centerMode'       => 'false',
			'dots'             => 'false',
			'fade'             => 'false',
			'infinite'         => 'true',
			'pauseOnDotsHover' => 'true',
			'pauseOnFocus'     => 'true',
			'pauseOnHover'     => 'true',
			'slidesToShow'     => 1,
			'slidesToScroll'   => 1,
			'variableWidth'    => 'false',
			'vertical'         => 'false',
			'prevArrow'        => $this->get_slick_arrow( 'prevArrow', 'false' ),
			'nextArrow'        => $this->get_slick_arrow( 'nextArrow', 'false' ),
		);
	}

	/**
	 * Merge user defined arguments into defaults array.
	 *
	 * NOTE: While *similar* to wp_parse_args, this is not the exact same thing.
	 * This uses $defaults as both an allowed key whitelist, and default values.
	 * This is done to prevent adding disallowed keys to the return merged array,
	 * whereas wp_parse_args has no such restriction.
	 *
	 * @param array $args     Value to merge with $defaults.
	 * @param array $defaults Optional. Array that serves as the defaults. Default empty.
	 *
	 * @return array Merged user defined values with defaults.
	 */
	public function parse_args( $args, $defaults ) {

		return array_replace_recursive(
			$defaults,
			array_intersect_key(
				$args,
				$defaults
			)
		);
	}

	/**
	 * Get the Slick Slider settings array.
	 *
	 * @uses \BBSlickSlider::get_slick_settings_defaults(), \BBSlickSlider::parse_args()
	 * @uses \BBSlickSlider::get_slick_arrow()
	 *
	 * @return array
	 */
	public function get_slick_settings() {

		$settings = $this->settings;

		if ( empty( $settings ) ) {
			$settings = $this->get_slick_settings_defaults();
		}

		$merged = $this->parse_args( json_decode( wp_json_encode( $settings ), true ), $this->get_slick_settings_defaults() );

		if ( ! empty( $merged['vertical'] ) && 'true' === $merged['vertical'] ) {
			$merged['prevArrow'] = $this->get_slick_arrow( 'prevArrow', 'true' );
			$merged['nextArrow'] = $this->get_slick_arrow( 'nextArrow', 'true' );
		}

		foreach ( $merged as $key => $value ) {

			if ( is_numeric( $value ) ) {
				$merged[ $key ] = (int) $value;
			}

			if ( 'true' === $value || 'false' === $value ) {
				$merged[ $key ] = wp_validate_boolean( $value );
			}
		}

		return $merged;
	}

	/**
	 * Print the Slick Slider settings object.
	 *
	 * @uses \BBSlickSlider::get_slick_settings()
	 */
	public function the_slick_settings_object() {
		echo wp_json_encode( $this->get_slick_settings() );
	}
}
