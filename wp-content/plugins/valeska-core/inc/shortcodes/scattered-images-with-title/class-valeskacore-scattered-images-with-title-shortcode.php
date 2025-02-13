<?php

if ( ! function_exists( 'valeska_core_add_scattered_images_with_title_showcase_shortcode' ) ) {
    /**
     * Function that add shortcode into shortcodes list for registration
     *
     * @param array $shortcodes
     *
     * @return array
     */
    function valeska_core_add_scattered_images_with_title_showcase_shortcode( $shortcodes ) {
        $shortcodes[] = 'ValeskaCore_Scattered_Images_With_Title_Shortcode';

        return $shortcodes;
    }

    add_filter( 'valeska_core_filter_register_shortcodes', 'valeska_core_add_scattered_images_with_title_showcase_shortcode' );
}

if ( class_exists( 'ValeskaCore_Shortcode' ) ) {
    class ValeskaCore_Scattered_Images_With_Title_Shortcode extends ValeskaCore_Shortcode {

        public function __construct() {
            $this->set_layouts( apply_filters( 'valeska_core_filter_scattered_images_with_title_layouts', array() ) );
            $this->set_extra_options( apply_filters( 'valeska_core_filter_scattered_images_with_title_extra_options', array() ) );

            parent::__construct();
        }

        public function map_shortcode() {
            $this->set_shortcode_path( VALESKA_CORE_SHORTCODES_URL_PATH . '/scattered-images-with-title' );
            $this->set_base( 'valeska_core_scattered_images_with_title' );
            $this->set_name( esc_html__( 'Scattered Images With Title', 'valeska-core' ) );
            $this->set_description( esc_html__( 'Shortcode that adds scattered images with title holder', 'valeska-core' ) );
            $this->set_category( esc_html__( 'Valeska Core', 'valeska-core' ) );
            $this->set_scripts(
                array(
                    'jquery-easing' => array(
                        'registered' => true,
                    ),
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'text',
                    'name'       => 'custom_class',
                    'title'      => esc_html__( 'Custom Class', 'valeska-core' ),
                    'group'      => esc_html__( 'Images Options', 'valeska-core' )
                )
            );

            $this->set_option(
                array(
                    'field_type'  => 'image',
                    'name'        => 'bottom_layer_images',
                    'multiple'    => 'yes',
                    'title'       => esc_html__( 'Bottom Layer Images', 'valeska-core' ),
                    'description' => esc_html__( 'For best results, please upload up to 6 images.', 'valeska-core' ),
                    'group'       => esc_html__( 'Images Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'bottom_layer_image_size',
                    'title'       => esc_html__( 'Bottom Layer Image Size', 'valeska-core' ),
                    'description' => esc_html__( 'For predefined image sizes input thumbnail, medium, large or full. If you wish to set a custom image size, type in the desired image dimensions in pixels (e.g. 400x400).', 'valeska-core' ),
                    'group'       => esc_html__( 'Images Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'image',
                    'name'        => 'top_layer_images',
                    'multiple'    => 'yes',
                    'title'       => esc_html__( 'Top Layer Images', 'valeska-core' ),
                    'description' => esc_html__( 'For best results, please upload up to 7 images.', 'valeska-core' ),
                    'group'       => esc_html__( 'Images Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'top_layer_image_size',
                    'title'       => esc_html__( 'Top Layer Image Size', 'valeska-core' ),
                    'description' => esc_html__( 'For predefined image sizes input thumbnail, medium, large or full. If you wish to set a custom image size, type in the desired image dimensions in pixels (e.g. 400x400).', 'valeska-core' ),
                    'group'       => esc_html__( 'Images Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'textarea',
                    'name'       => 'title',
                    'title'      => esc_html__( 'Title Text', 'valeska-core' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'title_tag',
                    'title'         => esc_html__( 'Title Tag', 'valeska-core' ),
                    'options'       => valeska_core_get_select_type_options_pool( 'title_tag' ),
                    'default_value' => 'p',
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'color',
                    'name'       => 'color',
                    'title'      => esc_html__( 'Color', 'valeska-core' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'text',
                    'name'       => 'font_family',
                    'title'      => esc_html__( 'Font Family', 'valeska-core' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'text',
                    'name'       => 'font_size',
                    'title'      => esc_html__( 'Font Size', 'valeska-core' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'text',
                    'name'       => 'line_height',
                    'title'      => esc_html__( 'Line Height', 'valeska-core' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'text',
                    'name'       => 'letter_spacing',
                    'title'      => esc_html__( 'Letter Spacing', 'valeska-core' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'select',
                    'name'       => 'font_weight',
                    'title'      => esc_html__( 'Font Weight', 'valeska-core' ),
                    'options'    => valeska_core_get_select_type_options_pool( 'font_weight' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'select',
                    'name'       => 'font_style',
                    'title'      => esc_html__( 'Font Style', 'valeska-core' ),
                    'options'    => valeska_core_get_select_type_options_pool( 'font_style' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type' => 'select',
                    'name'       => 'text_transform',
                    'title'      => esc_html__( 'Text Transform', 'valeska-core' ),
                    'options'    => valeska_core_get_select_type_options_pool( 'text_transform' ),
                    'group'      => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'text',
                    'name'          => 'line_break_positions',
                    'title'         => esc_html__( 'Positions of Line Break', 'valeska-core' ),
                    'description'   => esc_html__( 'Enter the positions of the words after which you would like to create a line break. Separate the positions with commas (e.g. if you would like the first, third, and fourth word to have a line break, you would enter "1,3,4")', 'valeska-core' ),
                    'default_value' => '-1',
                    'group'         => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'disable_title_break_words',
                    'title'         => esc_html__( 'Disable Title Line Break', 'valeska-core' ),
                    'description'   => esc_html__( 'Enabling this option will disable title line breaks for screen size 1024 and lower', 'valeska-core' ),
                    'options'       => valeska_core_get_select_type_options_pool( 'no_yes', false ),
                    'default_value' => 'no',
                    'group'         => esc_html__( 'Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'font_size_1366',
                    'title'       => esc_html__( 'Font Size', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 1366', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 1366 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'line_height_1366',
                    'title'       => esc_html__( 'Line Height', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 1366', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 1366 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'letter_spacing_1366',
                    'title'       => esc_html__( 'Letter Spacing', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 1366', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 1366 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'font_size_1024',
                    'title'       => esc_html__( 'Font Size', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 1024', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 1024 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'line_height_1024',
                    'title'       => esc_html__( 'Line Height', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 1024', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 1024 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'letter_spacing_1024',
                    'title'       => esc_html__( 'Letter Spacing', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 1024', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 1024 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'font_size_768',
                    'title'       => esc_html__( 'Font Size', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 768', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 768 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'line_height_768',
                    'title'       => esc_html__( 'Line Height', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 768', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 768 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'letter_spacing_768',
                    'title'       => esc_html__( 'Letter Spacing', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 768', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 768 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'font_size_680',
                    'title'       => esc_html__( 'Font Size', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 680', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 680 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'line_height_680',
                    'title'       => esc_html__( 'Line Height', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 680', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 680 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'  => 'text',
                    'name'        => 'letter_spacing_680',
                    'title'       => esc_html__( 'Letter Spacing', 'valeska-core' ),
                    'description' => esc_html__( 'Set responsive style value for screen size 680', 'valeska-core' ),
                    'group'       => esc_html__( 'Screen Size 680 Title Options', 'valeska-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'wait_for_trigger',
                    'title'         => esc_html__( 'Wait For Trigger', 'valeska-core' ),
                    'description'   => esc_html__( 'Enabling this option will delay animation until external trigger is activated.', 'valeska-core' ),
                    'options'       => valeska_core_get_select_type_options_pool( 'yes_no', false ),
                    'default_value' => 'no',
                    'group'         => esc_html__( 'Animation Options', 'valeska-core' )
                )
            );

            $this->map_extra_options();
        }

        public function render( $options, $content = null ) {
            parent::render( $options );
            $atts = $this->get_atts();

            $atts['unique_class']        = 'qodef-m-siwt-title-' . rand( 0, 1000 );
            $atts['holder_classes']      = $this->get_holder_classes( $atts );
            $atts['item_classes']        = $this->get_item_classes( $atts );
            $atts['bottom_layer_images'] = $this->generate_bottom_layer_images_params( $atts );
            $atts['top_layer_images']    = $this->generate_top_layer_images_params( $atts );
            $atts['title']               = $this->get_modified_title( $atts );
            $atts['title_classes']       = $this->get_title_classes( $atts );
            $atts['title_styles']        = $this->get_title_styles( $atts );
            $this->set_responsive_styles( $atts );

            return valeska_core_get_template_part( 'shortcodes/scattered-images-with-title', 'templates/scattered-images-with-title', '', $atts );
        }

        private function get_holder_classes( $atts ) {
            $holder_classes = $this->init_holder_classes();

            $holder_classes[] = 'qodef-scattered-images-with-title';
            $holder_classes[] = ! empty( $atts['wait_for_trigger'] ) && 'yes' === $atts['wait_for_trigger'] ? 'qodef-wait-for-trigger' : '';

            return implode( ' ', $holder_classes );
        }

        public function get_item_classes( $atts ) {
            $item_classes   = $this->init_item_classes();

            $item_classes[] = 'qodef-m-image';

            return implode( ' ', $item_classes );
        }

        private function generate_bottom_layer_images_params( $atts ) {
            $image_ids = array();
            $images    = array();
            $i         = 0;

            if ( '' !== $atts['bottom_layer_images'] ) {
                $image_ids = explode( ',', $atts['bottom_layer_images'] );
            }

            $image_size = $this->generate_bottom_layer_image_size( $atts );

            foreach ( $image_ids as $id ) {
                $image['image_id']   = intval( $id );
                $image_original      = wp_get_attachment_image_src( $id, 'full' );
                $image['url']        = $image_original[0];
                $image['alt']        = get_post_meta( $id, '_wp_attachment_image_alt', true );
                $image['image_size'] = $image_size;

                $images[ $i ] = $image;
                $i ++;
            }

            return $images;
        }

        private function generate_bottom_layer_image_size( $atts ) {
            $image_size = trim( $atts['bottom_layer_image_size'] );
            preg_match_all( '/\d+/', $image_size, $matches ); /* check if numeral width and height are entered */
            if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ), true ) ) {
                return $image_size;
            } elseif ( ! empty( $matches[0] ) ) {
                return array(
                    $matches[0][0],
                    $matches[0][1],
                );
            } else {
                return 'full';
            }
        }

        private function generate_top_layer_images_params( $atts ) {
            $image_ids = array();
            $images    = array();
            $i         = 0;

            if ( '' !== $atts['top_layer_images'] ) {
                $image_ids = explode( ',', $atts['top_layer_images'] );
            }

            $image_size = $this->generate_top_layer_image_size( $atts );

            foreach ( $image_ids as $id ) {
                $image['image_id']   = intval( $id );
                $image_original      = wp_get_attachment_image_src( $id, 'full' );
                $image['url']        = $image_original[0];
                $image['alt']        = get_post_meta( $id, '_wp_attachment_image_alt', true );
                $image['image_size'] = $image_size;

                $images[ $i ] = $image;
                $i ++;
            }

            return $images;
        }

        private function generate_top_layer_image_size( $atts ) {
            $image_size = trim( $atts['top_layer_image_size'] );
            preg_match_all( '/\d+/', $image_size, $matches ); /* check if numeral width and height are entered */
            if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ), true ) ) {
                return $image_size;
            } elseif ( ! empty( $matches[0] ) ) {
                return array(
                    $matches[0][0],
                    $matches[0][1],
                );
            } else {
                return 'full';
            }
        }

        private function get_modified_title( $atts ) {
            $title = $atts['title'];

            if ( ! empty( $title ) ) {
                if ( ( ! empty( $atts['line_break_positions'] ) ) ) {
                    $split_title          = explode( ' ', $title );
                    $line_break_positions = explode( ',', str_replace( ' ', '', $atts['line_break_positions'] ) );

                    foreach ( $line_break_positions as $position ) {
                        $position = intval( $position );

                        if ( isset( $split_title[ $position - 1 ] ) && ! empty( $split_title[ $position - 1 ] ) ) {
                            $split_title[ $position - 1 ] = $split_title[ $position - 1 ] . '</span><br /><span class="qodef-m-title-part">';
                        }
                    }
                }

                $title = '<span class="qodef-m-title-part">' . implode( ' ', $split_title ) . '</span>';
            }

            return $title;
        }

        private function get_title_classes( $atts ) {
            $title_classes[] = 'qodef-m-title';
            $title_classes[] = $atts['unique_class'];

            return implode( ' ', $title_classes );
        }

        private function get_title_styles( $atts ) {
            $styles = array();

            if ( ! empty( $atts['color'] ) ) {
                $styles[] = 'color: ' . $atts['color'];
            }

            if ( ! empty( $atts['font_family'] ) ) {
                $styles[] = 'font-family: ' . $atts['font_family'];
            }

            $font_size = $atts['font_size'];
            if ( ! empty( $font_size ) ) {
                if ( qode_framework_string_ends_with_typography_units( $font_size ) ) {
                    $styles[] = 'font-size: ' . $font_size;
                } else {
                    $styles[] = 'font-size: ' . intval( $font_size ) . 'px';
                }
            }

            $line_height = $atts['line_height'];
            if ( ! empty( $line_height ) ) {
                if ( qode_framework_string_ends_with_typography_units( $line_height ) ) {
                    $styles[] = 'line-height: ' . $line_height;
                } else {
                    $styles[] = 'line-height: ' . intval( $line_height ) . 'px';
                }
            }

            $letter_spacing = $atts['letter_spacing'];
            if ( '' !== $letter_spacing ) {
                if ( qode_framework_string_ends_with_typography_units( $letter_spacing ) ) {
                    $styles[] = 'letter-spacing: ' . $letter_spacing;
                } else {
                    $styles[] = 'letter-spacing: ' . intval( $letter_spacing ) . 'px';
                }
            }

            if ( ! empty( $atts['font_weight'] ) ) {
                $styles[] = 'font-weight: ' . $atts['font_weight'];
            }

            if ( ! empty( $atts['font_style'] ) ) {
                $styles[] = 'font-style: ' . $atts['font_style'];
            }

            if ( ! empty( $atts['text_transform'] ) ) {
                $styles[] = 'text-transform: ' . $atts['text_transform'];
            }

            return $styles;
        }

        private function set_responsive_styles( $atts ) {
            $unique_class = '.' . $atts['unique_class'];
            $screen_sizes = array( '1366', '1024', '768', '680' );
            $option_keys  = array( 'font_size', 'line_height', 'letter_spacing' );

            foreach ( $screen_sizes as $screen_size ) {
                $styles = array();

                foreach ( $option_keys as $option_key ) {
                    $option_value = $atts[ $option_key . '_' . $screen_size ];
                    $style_key    = str_replace( '_', '-', $option_key );

                    if ( '' !== $option_value ) {
                        if ( qode_framework_string_ends_with_typography_units( $option_value ) ) {
                            $styles[ $style_key ] = $option_value . '!important';
                        } else {
                            $styles[ $style_key ] = intval( $option_value ) . 'px !important';
                        }
                    }
                }

                if ( ! empty( $styles ) ) {
                    add_filter(
                        'valeska_core_filter_add_responsive_' . $screen_size . '_inline_style_in_footer',
                        function ( $style ) use ( $unique_class, $styles ) {
                            $style .= qode_framework_dynamic_style( $unique_class, $styles );

                            return $style;
                        }
                    );
                }
            }
        }
    }
}
