<?php
// Include mobile logo
valeska_core_get_mobile_header_logo_image();

// Include mobile navigation opener
valeska_core_get_opener_icon_html(
	array(
		'option_name'  => 'mobile_menu',
		'custom_class' => 'qodef-side-area-mobile-header-opener',
	)
);

// Include mobile navigation
valeska_core_template_part( 'mobile-header', 'layouts/side-area/templates/navigation' );
