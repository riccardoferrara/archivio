<?php
// Hook to include additional content before page content holder
do_action( 'valeska_action_before_page_content_holder' );
?>
<main id="qodef-page-content" class="qodef-grid qodef-layout--template <?php echo esc_attr( valeska_get_grid_gutter_classes() ); ?>" role="main">
	<div class="qodef-grid-inner clear">
		<?php
		// Include page content loop
		valeska_template_part( 'content', 'templates/parts/loop' );

		// Include page content sidebar
		valeska_template_part( 'sidebar', 'templates/sidebar' );
		?>
	</div>
</main>
<?php
// Hook to include additional content after main page content holder
do_action( 'valeska_action_after_page_content_holder' );
?>
