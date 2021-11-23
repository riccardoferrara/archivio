<?php

if ( ! function_exists( 'valeska_core_add_contact_info_widget' ) ) {
	/**
	 * function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function valeska_core_add_contact_info_widget( $widgets ) {
		$widgets[] = 'ValeskaCore_Contact_Info_Widget';

		return $widgets;
	}

	add_filter( 'valeska_core_filter_register_widgets', 'valeska_core_add_contact_info_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class ValeskaCore_Contact_Info_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'valeska_core_contact_info' );
			$this->set_name( esc_html__( 'Valeska Contact Info', 'valeska-core' ) );
			$this->set_description( esc_html__( 'Use this widget to add a contact info link into widget area.', 'valeska-core' ) );
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'widget_title',
					'title'      => esc_html__( 'Title', 'valeska-core' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'    => 'select',
					'name'          => 'link_type',
					'title'         => esc_html__( 'Link Type', 'valeska-core' ),
					'options'       => array(
						'link'      => esc_html__( 'Custom Link', 'valeska-core' ),
						'telephone' => esc_html__( 'Telephone Number', 'valeska-core' ),
						'fax'       => esc_html__( 'Fax Number', 'valeska-core' ),
						'email'     => esc_html__( 'Email', 'valeska-core' ),
					),
					'default_value' => 'link',
				)
			);
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'link',
					'title'      => esc_html__( 'Link', 'valeska-core' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'    => 'select',
					'name'          => 'target',
					'title'         => esc_html__( 'Link Target', 'valeska-core' ),
					'options'       => valeska_core_get_select_type_options_pool( 'link_target' ),
					'default_value' => '_self',
					'dependency'    => array(
						'show' => array(
							'link_type' => array(
								'values'        => 'link',
								'default_value' => 'link',
							),
						),
					),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'    => 'select',
					'name'          => 'wrapper_tag',
					'title'         => esc_html__( 'Link Wrapper Tag', 'valeska-core' ),
					'options'       => valeska_core_get_select_type_options_pool( 'title_tag', false, array(), array( 'li' => esc_attr__( 'List Item', 'valeska-core' ) ) ),
					'default_value' => 'p',
				)
			);
		}

		public function render( $atts ) {
			$link_meta = array();

			if ( ! empty( $atts['link'] ) ) {
				$link_meta['text'] = $atts['link'];

				switch ( $atts['link_type'] ) {
					case 'link':
						$link_meta['itemprop'] = 'url';
						$link_meta['link']     = $atts['link'];
						$link_meta['target']   = $atts['target'];
						$link_meta['text']     = $atts['link'];
						break;
					case 'telephone':
						// preserve plus sign
						if ( '+' === $atts['link'][0] ) {
							$plus = '+';
						} else {
							$plus = '';
						}

						$link_meta['itemprop'] = 'telephone';
						$link_meta['link']     = 'tel:' . esc_attr( $plus ) . preg_replace( '/[^0-9]/', '', $atts['link'] );
						break;
					case 'fax':
						// preserve plus sign
						if ( '+' === $atts['link'][0] ) {
							$plus = '+';
						} else {
							$plus = '';
						}

						$link_meta['itemprop'] = 'faxNumber';
						$link_meta['link']     = 'fax:' . esc_attr( $plus ) . preg_replace( '/[^0-9]/', '', $atts['link'] );
						break;
					case 'email':
						$link_meta['itemprop'] = 'email';
						$link_meta['link']     = 'mailto:' . str_replace( 'mailto:', '', $atts['link'] );
						break;
				}
			}

			if ( ! empty( $link_meta ) ) {
				echo sprintf(
					'%s %s %s',
					'<' . esc_attr( $atts['wrapper_tag'] ) . ' class="qodef-contact-info-widget qodef--' . esc_attr( $atts['link_type'] ) . '">',
					'<a itemprop="' . esc_attr( $link_meta['itemprop'] ) . '" href="' . esc_url( $link_meta['link'] ) . '">' . esc_html( $link_meta['text'] ) . '</a>',
					'</' . esc_attr( $atts['wrapper_tag'] ) . '>'
				);
			}
		}
	}
}
