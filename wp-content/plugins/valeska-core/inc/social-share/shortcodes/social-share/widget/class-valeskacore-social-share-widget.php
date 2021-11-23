<?php

if ( ! function_exists( 'valeska_core_add_social_share_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function valeska_core_add_social_share_widget( $widgets ) {
		$widgets[] = 'ValeskaCore_Social_Share_Widget';

		return $widgets;
	}

	add_filter( 'valeska_core_filter_register_widgets', 'valeska_core_add_social_share_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class ValeskaCore_Social_Share_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'valeska_core_social_share',
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'valeska_core_social_share' );
				$this->set_name( esc_html__( 'Valeska Social Share', 'valeska-core' ) );
				$this->set_description( esc_html__( 'Add a social share element into widget areas', 'valeska-core' ) );
			}
		}

		public function render( $atts ) {
			echo ValeskaCore_Social_Share_Shortcode::call_shortcode( $atts ); // XSS OK
		}
	}
}
