<div class="qodef-e-media"><!--
	--><?php
	switch ( get_post_format() ) {
		case 'gallery':
			valeska_core_theme_template_part( 'blog', 'templates/parts/post-format/gallery', '', $params );
			break;
		case 'video':
			valeska_core_theme_template_part( 'blog', 'templates/parts/post-format/video', '', $params );
			break;
		case 'audio':
			valeska_core_theme_template_part( 'blog', 'templates/parts/post-format/audio', '', $params );
			break;
		default:
			valeska_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/image', '', $params );
			break;
	}
	?><!--
--></div>
