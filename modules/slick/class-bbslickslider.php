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
	 * Get the slides from the module's settings.
	 *
	 * @return stdClass[]|array An array of stdClass "slide" objects on success, else empty array.
	 */
	public function get_slides() {

		if ( $this->has_slides() ) {
			return $this->settings->slides;
		}

		return array();
	}

	/**
	 * Conditional check if the provided slide is an image slide.
	 *
	 * @param stdClass $slide A stdClass "slide" object to test.
	 *
	 * @return bool
	 */
	public function is_image_slide( $slide ) {

		if ( empty( $slide ) || empty( $slide->slide_type_select ) || 'image' !== $slide->slide_type_select ) {
			return false;
		}

		return true;
	}
}
