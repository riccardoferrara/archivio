<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
	<meta name="facebook-domain-verification" content="18wg10ezjnp5j0zv2ezeq1ly44foek" />
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<script src="https://kit.fontawesome.com/09760c552a.js" crossorigin="anonymous"></script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">
	<?php
	// Hook to include default WordPress hook after body tag open
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	}

	// Hook to include additional content after body tag open
	do_action( 'valeska_action_after_body_tag_open' );
	?>
	<div id="qodef-page-wrapper" class="<?php echo esc_attr( valeska_get_page_wrapper_classes() ); ?>">
		<?php
		// Hook to include page header template
		do_action( 'valeska_action_page_header_template' );
		?>
		<div id="qodef-page-outer">
			<?php
			// Include title template
			get_template_part( 'title' );

			// Hook to include additional content before page inner content
			do_action( 'valeska_action_before_page_inner' );
			?>
			<div id="qodef-page-inner" class="<?php echo esc_attr( valeska_get_page_inner_classes() ); ?>">
