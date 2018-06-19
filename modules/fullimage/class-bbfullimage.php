<?php
/**
 * BBFullImage Class file.
 *
 * @package TwisterMcBBModules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BBFullImage
 */
class BBFullImage extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {

		parent::__construct(
			array(
				'name'          => __( 'Full Image', 'tmcbbm' ),
				'description'   => __( 'Full Width Image', 'tmcbbm' ),
				'category'      => __( 'Advanced Modules', 'tmcbbm' ),
				'dir'           => TMCBBM_DIR . 'modules/fullimage/',
				'url'           => TMCBBM_URL . 'modules/fullimage/',
			)
		);

		$this->add_css( 'font-awesome' );
	}

	/**
	 * Register the module and its form settings.
	 *
	 * What's implied, but not specifically stated, is that Beaver Builder itself handles instantiation on demand.
	 * An unusual side effect of that approach, is that hooks in constructor appear to fire more than once.
	 *
	 * @action init
	 */
	public static function register() {
		self::register_module();
	}

	/**
	 * Register the module and it's form settings with Beaver Builder.
	 *
	 * @see \FLBuilderModel::register_module()
	 */
	public static function register_module() {

		/**
		 * Register the module and its form settings.
		 */
		FLBuilder::register_module(
			'BBFullImage',
			array(
				'general' => array(
					'title'    => __( 'Full Image', 'tmcbbm' ),
					'sections' => array(
						'general'      => array(
							'title'  => __( 'Photo Settings', 'tmcbbm' ),
							'fields' => array(
								'photo_field'    => array(
									'type'  => 'photo',
									'label' => __( 'Photo', 'tmcbbm' ),
								),
								'forceImageSize' => array(
									'type'    => 'select',
									'label'   => __( 'Force Image to Full Width', 'tmcbbm' ),
									'default' => 'true',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
									'toggle'  => array(
										'false' => array(
											'fields' => array( 'imageAlign' ),
										),
									),
								),
								'imageAlign'     => array(
									'type'    => 'select',
									'label'   => __( 'Image Align', 'tmcbbm' ),
									'default' => 'left',
									'options' => array(
										'left'   => __( 'Left', 'tmcbbm' ),
										'center' => __( 'Center', 'tmcbbm' ),
										'right'  => __( 'Right', 'tmcbbm' ),
									),
								),
								'showCaption'    => array(
									'type'    => 'select',
									'label'   => __( 'Show Caption', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
									'toggle'  => array(
										'true' => array(
											'sections' => array( 'captionStyle' ),
										),
									),
								),
							),
						),
						'captionStyle' => array(
							'title'  => __( 'Caption Style', 'tmcbbm' ),
							'fields' => array(
								'captionSize'            => array(
									'type'        => 'text',
									'size'        => 3,
									'label'       => __( 'Caption Text Size', 'tmcbbm' ),
									'default'     => 14,
									'description' => 'px',
								),
								'captionAlign'           => array(
									'type'    => 'select',
									'label'   => __( 'Text Align', 'tmcbbm' ),
									'default' => 'center',
									'options' => array(
										'left'   => __( 'Left', 'tmcbbm' ),
										'center' => __( 'Center', 'tmcbbm' ),
										'right'  => __( 'Right', 'tmcbbm' ),
									),
								),
								'captionColor'           => array(
									'type'    => 'color',
									'label'   => __( 'Caption Text Color', 'tmcbbm' ),
									'default' => 'ffffff',
								),
								'captionBackgroundColor' => array(
									'type'       => 'color',
									'label'      => __( 'Caption Background Color', 'tmcbbm' ),
									'default'    => '333333',
									'show_reset' => true,
								),
								'captionPadding'         => array(
									'type'        => 'text',
									'size'        => 3,
									'label'       => __( 'Caption Padding', 'tmcbbm' ),
									'default'     => 5,
									'description' => 'px',
								),
							),
						),
					),
				),
				'link'    => array(
					'title'    => __( 'Link', 'tmcbbm' ),
					'sections' => array(
						'general-link' => array(
							'title'  => __( 'Link', 'tmcbbm' ),
							'fields' => array(
								'linkImage' => array(
									'type'    => 'select',
									'label'   => __( 'Link Image', 'tmcbbm' ),
									'default' => 'false',
									'options' => array(
										'true'  => __( 'Yes', 'tmcbbm' ),
										'false' => __( 'No', 'tmcbbm' ),
									),
									'toggle'  => array(
										'true' => array(
											'sections' => array( 'linkdetails' ),
										),
									),
								),
							),
						),
						'linkdetails'  => array(
							'title'  => __( 'Link Details', 'tmcbbm' ),
							'fields' => array(
								'linkurl'    => array(
									'type'  => 'link',
									'label' => __( 'Link', 'tmcbbm' ),
								),
								'linktarget' => array(
									'type'    => 'select',
									'label'   => __( 'Target', 'tmcbbm' ),
									'default' => '_self',
									'options' => array(
										'_self'  => __( 'Same Window', 'tmcbbm' ),
										'_blank' => __( 'New Window', 'tmcbbm' ),
									),
								),
							),
						),
					),
				),
			)
		);
	}
}
