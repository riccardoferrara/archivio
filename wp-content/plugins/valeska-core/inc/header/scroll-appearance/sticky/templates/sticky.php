<div class="qodef-header-sticky qodef-custom-header-layout <?php echo implode( ' ', apply_filters( 'valeska_core_filter_sticky_header_class', array() ) ); ?>">
	<div class="qodef-header-sticky-inner <?php echo implode( ' ', apply_filters( 'valeska_filter_header_inner_class', array(), 'sticky' ) ); ?>">
		<?php
		// Include logo
		valeska_core_get_header_logo_image( array( 'sticky_logo' => true ) );

		// Include main navigation
		valeska_core_template_part( 'header', 'templates/parts/navigation', '', array( 'menu_id' => 'qodef-sticky-navigation-menu' ) );

		// Include widget area one
		valeska_core_get_header_widget_area( 'one', 'sticky-header-widget-area', 'sticky' );

		do_action( 'valeska_core_action_after_sticky_header' );
		?>
	</div>
</div>
