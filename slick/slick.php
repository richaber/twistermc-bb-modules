<?php

/**
 * @class BBSlickSlider
 */
class BBSlickSlider extends FLBuilderModule {

    /** 
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Slick', 'fl-builder'),
            'description'   => __('Slick Slider for BeaverBuilder', 'fl-builder'),
            'category'		=> __('Advanced Modules', 'fl-builder'),
            'dir'           => TMC_BB_DIR . 'slick/',
            'url'           => TMC_BB_URL . 'slick/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
        
        /** 
         * Use these methods to enqueue css and js already
         * registered or to register and enqueue your own.
         */
        // Already registered
        $this->add_css('font-awesome');
        $this->add_js('jquery-bxslider');
        
        // Register and enqueue your own
        $this->add_css( 'slick-slider-css-cdn', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css', array(), '' );
        $this->add_js( 'slick-slider-js-cdn', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array('jquery'), '', false );

    }

    /** 
     * Use this method to work with settings data before 
     * it is saved. You must return the settings object. 
     *
     * @method update
     * @param $settings {object}
     */      
    public function update($settings)
    {
        $settings->textarea_field .= ' - this text was appended in the update method.';
        
        return $settings;
    }

    /** 
     * This method will be called by the builder
     * right before the module is deleted. 
     *
     * @method delete
     */      
    public function delete()
    {
    
    }

    /** 
     * Add additional methods to this class for use in the 
     * other module files such as preview.php, frontend.php
     * and frontend.css.php.
     * 
     *
     * @method example_method
     */   
    public function example_method()
    {
    
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('BBSlickSlider', array(
    'general'       => array( // Tab
        'title'         => __('Photos', 'fl-builder'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => __('SlickSlider Settings', 'fl-builder'), // Section Title
                'fields'        => array( // Section Fields
                    'multiple_photos_field'     => array(
                        'type'          => 'multiple-photos',
                        'label'         => __('Photos', 'fl-builder'),
                    ),
                    'forceImageSize'   => array(
                        'type'          => 'select',
                        'label'         => __('Force Images to Full Width', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'showCaptions'   => array(
                        'type'          => 'select',
                        'label'         => __('Show Captions', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'autoPlay'   => array(
                        'type'          => 'select',
                        'label'         => __('Auto Play', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'autoPlaySpeed'   => array(
                        'type'          => 'text',
                        'label'         => __('Auto Play Speed', 'fl-builder'),
                        'default'       => '3000',
                        'description'   => 'milliseconds'
                    ),
                    'adaptiveHeight'   => array(
                        'type'          => 'select',
                        'label'         => __('Adaptive Height', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'fixedHeight'   => array(
                        'type'          => 'select',
                        'label'         => __('Fixed Height', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        ),
                        'toggle'        => array(
                            'true'      => array(
                                'fields'        => array( 'fixedHeightSize' ),
                            ),
                        )
                    ),
                    'fixedHeightSize'   => array(
                        'type'          => 'text',
                        'label'         => __('Fixed Height Size', 'fl-builder'),
                        'default'       => '500',
                        'description'   => 'pixels',
                    ),
                    'arrows'   => array(
                        'type'          => 'select',
                        'label'         => __('Show Arrows', 'fl-builder'),
                        'default'       => 'true',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'dots'   => array(
                        'type'          => 'select',
                        'label'         => __('Show Dots', 'fl-builder'),
                        'default'       => 'true',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'pauseOnHover'   => array(
                        'type'          => 'select',
                        'label'         => __('Pause on Hover', 'fl-builder'),
                        'default'       => 'true',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'pauseOnDotsHover'   => array(
                        'type'          => 'select',
                        'label'         => __('Pause on Dots Hover', 'fl-builder'),
                        'default'       => 'true',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'variableWidth'   => array(
                        'type'          => 'select',
                        'label'         => __('Variable Width', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'centerMode'   => array(
                        'type'          => 'select',
                        'label'         => __('Center Mode', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'fade'   => array(
                        'type'          => 'select',
                        'label'         => __('Fade', 'fl-builder'),
                        'default'       => 'false',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'infinite'   => array(
                        'type'          => 'select',
                        'label'         => __('Infinite Loop', 'fl-builder'),
                        'default'       => 'true',
                        'options'       => array(
                            'true'      => __('Yes', 'fl-builder'),
                            'false'      => __('No', 'fl-builder')
                        )
                    ),
                    'slidesToShow'   => array(
                        'type'          => 'text',
                        'label'         => __('Slides to Show', 'fl-builder'),
                        'default'       => '1'
                    ),
                    'slidesToScroll'   => array(
                        'type'          => 'text',
                        'label'         => __('Slides to Scroll', 'fl-builder'),
                        'default'       => '1'
                    ),
                )
            )
        )
    ),
    'toggle'       => array( // Tab
        'title'         => __('Toggle', 'fl-builder'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => __('Toggle Example', 'fl-builder'), // Section Title
                'fields'        => array( // Section Fields
                    'toggle_me'     => array(
                        'type'          => 'select',
                        'label'         => __('Toggle Me!', 'fl-builder'),
                        'default'       => 'option-1',
                        'options'       => array(
                            'option-1'      => __('Option 1', 'fl-builder'),
                            'option-2'      => __('Option 2', 'fl-builder')
                        ),
                        'toggle'        => array(
                            'option-1'      => array(
                                'fields'        => array('toggle_text', 'toggle_text2'),
                                'sections'      => array('toggle_section')
                            ),
                            'option-2'      => array()
                        )
                    ),
                    'toggle_text'   => array(
                        'type'          => 'text',
                        'label'         => __('Hide Me!', 'fl-builder'),
                        'default'       => '',
                        'description'   => 'I get hidden when you toggle the select above.'
                    ),
                    'toggle_text2'   => array(
                        'type'          => 'text',
                        'label'         => __('Me Too!', 'fl-builder'),
                        'default'       => ''
                    )
                )
            ),
            'toggle_section' => array( // Section
                'title'         => __('Hide This Section!', 'fl-builder'), // Section Title
                'fields'        => array( // Section Fields
                    'some_text'     => array(
                        'type'          => 'text',
                        'label'         => __('Text', 'fl-builder'),
                        'default'       => ''
                    )
                )
            )
        )
    ),
    'multiple'      => array( // Tab
        'title'         => __('Multiple', 'fl-builder'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => __('Multiple Example', 'fl-builder'), // Section Title
                'fields'        => array( // Section Fields
                    'test'          => array(
                        'type'          => 'text',
                        'label'         => __('Multiple Test', 'fl-builder'),
                        'multiple'      => true // Doesn't work with editor or photo fields
                    )
                )
            )
        )
    ),
    'include'       => array( // Tab
        'title'         => __('Include', 'fl-builder'), // Tab title
        'file'          => TMC_BB_DIR . 'slick/includes/settings-example.php'
    )
));

/**
 * Register a settings form to use in the "form" field type above.
 */
FLBuilder::register_settings_form('example_settings_form', array(
    'title' => __('Example Form Settings', 'fl-builder'),
    'tabs'  => array(
        'general'      => array( // Tab
            'title'         => __('General', 'fl-builder'), // Tab title
            'sections'      => array( // Tab Sections
                'general'       => array( // Section
                    'title'         => '', // Section Title
                    'fields'        => array( // Section Fields
                        'example'       => array(
                            'type'          => 'text',
                            'label'         => __('Example', 'fl-builder'),
                            'default'       => 'Some example text'
                        )
                    )
                )
            )
        ),
        'another'       => array( // Tab
            'title'         => __('Another Tab', 'fl-builder'), // Tab title
            'sections'      => array( // Tab Sections
                'general'       => array( // Section
                    'title'         => '', // Section Title
                    'fields'        => array( // Section Fields
                        'another_example' => array(
                            'type'          => 'text',
                            'label'         => __('Another Example', 'fl-builder')
                        )
                    )
                )
            )
        )
    )
));