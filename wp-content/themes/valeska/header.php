<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
	<meta name="facebook-domain-verification" content="18wg10ezjnp5j0zv2ezeq1ly44foek" />
	<!-- Facebook Pixel Code -->
	<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window,document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '1082846658971344'); 
		fbq('track', 'PageView');
	</script>
	<noscript>
		<img height="1" width="1" 
		src="https://www.facebook.com/tr?id=1082846658971344&ev=PageView&noscript=1"/>
	</noscript>
	<!-- End Facebook Pixel Code -->
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
