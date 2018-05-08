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
	 * BBSlickSlider constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'name'          => __( 'Slick', 'tmcbb' ),
				'description'   => __( 'Slick Slider for BeaverBuilder', 'tmcbb' ),
				'category'      => __( 'Advanced Modules', 'tmcbb' ),
				'dir'           => TMC_BB_DIR . 'slick/',
				'url'           => TMC_BB_URL . 'slick/',
			)
		);

		$this->add_css( 'font-awesome' );

		$this->add_js( 'jquery-bxslider' );

		$this->add_css( 'slick-slider-css-cdn', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css', array(), '' );

		$this->add_js( 'slick-slider-js-cdn', TMC_BB_URL . '/slick/js/slick.js', array( 'jquery' ), '', false );
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
}

/**
 * Register the module and its form settings.
 *
 * @todo Break out Photo vs Video settings.
 */
FLBuilder::register_module(
	'BBSlickSlider',
	array(
		'general'       => array(
			'title'    => __( 'Media', 'tmcbb' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'Media Settings', 'tmcbb' ),
					'fields' => array(
						'photoVideo' => array(
							'type'    => 'select',
							'label'   => __( 'Type of Media', 'tmcbb' ),
							'default' => 'photo',
							'options' => array(
								'photo' => __( 'Photo', 'tmcbb' ),
								'video' => __( 'Video - YouTube/Vimeo *Beta*', 'tmcbb' ),
							),
							'toggle'  => array(
								'photo' => array(
									'tabs'   => array( 'imageSettings' ),
									'fields' => array( 'fade' ),
								),
								'video' => array(
									'tabs' => array( 'videoSettings' ),
								),
							),
						),
					),
				),
			),
		),
		'imageSettings' => array(
			'title'    => __( 'Images', 'tmcbb' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'Image Settings', 'tmcbb' ),
					'fields' => array(
						'multiple_photos_field' => array(
							'type'  => 'multiple-photos',
							'label' => __( 'Photos', 'tmcbb' ),
						),
						'showCaptions'          => array(
							'type'    => 'select',
							'label'   => __( 'Show Captions', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
						'oneSlide'              => array(
							'type'    => 'select',
							'label'   => __( 'Show # Slides', 'tmcbb' ),
							'options' => array(
								'true'  => __( 'One', 'tmcbb' ),
								'false' => __( 'Multiple', 'tmcbb' ),
							),
							'toggle'  => array(
								'false' => array(
									'sections' => array( 'multiplePhotoSettings' ),
								),
								'true'  => array(
									'fields' => array( 'adaptiveHeight', 'forceImageSize' ),
								),
							),
						),
						'forceImageSize'        => array(
							'type'    => 'select',
							'label'   => __( 'Force Images to Full Width', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
					),
				),
			),
		),
		'videoSettings' => array(
			'title'    => __( 'Videos', 'tmcbb' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'Video Controls', 'tmcbb' ),
					'fields' => array(
						'multiple_video_field' => array(
							'type'     => 'text',
							'label'    => __( 'Video URL', 'tmcbb' ),
							'multiple' => true,
						),
						'autoplay_videos'      => array(
							'type'    => 'select',
							'label'   => __( 'Auto play videos?', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
					),
				),
			),
		),
		'toggle'        => array(
			'title'    => __( 'Controls', 'tmcbb' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'Slideshow Controls', 'tmcbb' ),
					'fields' => array(
						'autoPlay'         => array(
							'type'    => 'select',
							'label'   => __( 'Auto Play', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
							'toggle'  => array(
								'true' => array(
									'fields' => array( 'autoPlaySpeed' ),
								),
							),
						),
						'autoPlaySpeed'    => array(
							'type'        => 'text',
							'label'       => __( 'Auto Play Speed', 'tmcbb' ),
							'default'     => '3000',
							'description' => 'milliseconds',
						),
						'arrows'           => array(
							'type'    => 'select',
							'label'   => __( 'Show Arrows', 'tmcbb' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
						'pauseOnHover'     => array(
							'type'    => 'select',
							'label'   => __( 'Pause on Hover', 'tmcbb' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
						'dots'             => array(
							'type'    => 'select',
							'label'   => __( 'Show Dots', 'tmcbb' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
							'toggle'  => array(
								'true' => array(
									'fields' => array( 'pauseOnDotsHover' ),
								),
							),
						),
						'pauseOnDotsHover' => array(
							'type'    => 'select',
							'label'   => __( 'Pause on Dots Hover', 'tmcbb' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
					),
				),
			),
		),
		'design'        => array(
			'title'    => __( 'Design', 'tmcbb' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'Tweak the Design', 'tmcbb' ),
					'fields' => array(
						'arrowSize'                 => array(
							'type'        => 'text',
							'label'       => __( 'Arrow Size', 'tmcbb' ),
							'default'     => '20',
							'description' => __( 'pixels', 'tmcbb' ),
						),
						'arrowColor'                => array(
							'type'    => 'color',
							'label'   => __( 'Arrow Color', 'tmcbb' ),
							'default' => 'ffffff',
						),
						'arrowBackgroundColor'      => array(
							'type'       => 'color',
							'label'      => __( 'Arrow Background Color', 'tmcbb' ),
							'default'    => '333333',
							'show_reset' => true,
						),
						'arrowHoverColor'           => array(
							'type'    => 'color',
							'label'   => __( 'Arrow Hover Color', 'tmcbb' ),
							'default' => 'ffffff',
						),
						'arrowHoverBackgroundColor' => array(
							'type'       => 'color',
							'label'      => __( 'Arrow Hover Background Color', 'tmcbb' ),
							'default'    => '689BCA',
							'show_reset' => true,
						),
						'dotSize'                   => array(
							'type'        => 'text',
							'label'       => __( 'Dot Size', 'tmcbb' ),
							'default'     => '14',
							'description' => __( 'pixels', 'tmcbb' ),
						),
						'dotColor'                  => array(
							'type'    => 'color',
							'label'   => __( 'Dot Color', 'tmcbb' ),
							'default' => '000000',
						),
						'dotBackgroundColor'        => array(
							'type'       => 'color',
							'label'      => __( 'Dot Background Color', 'tmcbb' ),
							'show_reset' => true,
						),
						'dotActiveColor'            => array(
							'type'    => 'color',
							'label'   => __( 'Dot Active Color', 'tmcbb' ),
							'default' => 'ffffff',
						),
						'dotActiveBackgroundColor'  => array(
							'type'       => 'color',
							'label'      => __( 'Dot Active Background Color', 'tmcbb' ),
							'default'    => '333333',
							'show_reset' => true,
						),
						'dotHoverColor'             => array(
							'type'    => 'color',
							'label'   => __( 'Dot Hover Color', 'tmcbb' ),
							'default' => 'ffffff',
						),
						'dotHoverBackgroundColor'   => array(
							'type'       => 'color',
							'label'      => __( 'Dot Hover Background Color', 'tmcbb' ),
							'default'    => '689BCA',
							'show_reset' => true,
						),
					),
				),
			),
		),
		'multiple'      => array(
			'title'    => __( 'Settings', 'tmcbb' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'Overall Settings', 'tmcbb' ),
					'fields' => array(
						'adaptiveHeight'   => array(
							'type'    => 'select',
							'label'   => __( 'Adaptive Height', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
							'toggle'  => array(
								'false' => array(
									'fields' => array( 'fixedHeightSize' ),
								),
							),
						),
						'fixedHeightSize'  => array(
							'type'        => 'text',
							'label'       => __( 'Fixed Height Size', 'tmcbb' ),
							'default'     => '500',
							'description' => __( 'pixels', 'tmcbb' ),
						),
						'fade'             => array(
							'type'    => 'select',
							'label'   => __( 'Fade', 'tmcbb' ),
							'help'    => __( 'For images only.', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
						'infinite'         => array(
							'type'    => 'select',
							'label'   => __( 'Infinite Loop', 'tmcbb' ),
							'default' => 'true',
							'options' => array(
								'true'  => __( 'Yes', 'tmcbb' ),
								'false' => __( 'No', 'tmcbb' ),
							),
						),
						'verticalCarousel' => array(
							'type'    => 'select',
							'label'   => __( 'Scroll', 'tmcbb' ),
							'default' => 'false',
							'options' => array(
								'true'  => __( 'Vertical', 'tmcbb' ),
								'false' => __( 'Horizontal', 'tmcbb' ),
							),
							'toggle'  => array(
								'false' => array(
									'fields' => array( 'fade' ),
								),
							),
						),
					),
				),
			),
		),
	)
);
