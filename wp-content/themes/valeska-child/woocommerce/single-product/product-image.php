<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);


function ridev_product_images (){
	$product_image_ids = $GLOBALS['product']->get_gallery_image_ids();
	$images_colors_url = [];
	foreach($product_image_ids as &$id) {
		$thumbnail_url = wp_get_attachment_image_url($id);
		$color = explode('-',end(explode('/',$thumbnail_url)))[1];
		$extension = end(explode('.',$thumbnail_url));
		$fullsize_image_url = preg_split("/....[x]+/", $thumbnail_url)[0] .'-768x1024'.'.'.$extension;
		$position = explode('-',end(explode('/',$thumbnail_url)))[2];
		$images_colors_url["$color-$position"] = $fullsize_image_url;
	}
	return $images_colors_url;
};





?>

<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		// if ( $post_thumbnail_id ) {
		if ( false ) {

			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			//questa parte inserisce le foto al primo giro
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			//recupera gli url delle foto
			$images_colors_url = ridev_product_images();
			// crea codice html con tutte le foto
			$i = 1;
			$selected_color = TRUE;
			$change_color = FALSE;
			$loop_color = "";
			$first_loop = TRUE;
			foreach($images_colors_url as $color_position => $image_url){
				//se la foto è la terza o la quarta cambia stile (width 50%)
				$visibility_class = $selected_color ? 'selected-color':'unselected-color';
				$color = explode('-', $color_position)[0];
				$i = explode('-', $color_position)[1];
				// se si cambia di colore prima scrivere l'html con le 3 immagini
				if ($loop_color != $color) {
					$loop_color = $color;
					if (!$first_loop) {
						$html .= $html_0 . $html_1 . $html_2 . $html_3;
					}
					$first_loop = FALSE;
				}
				switch($i) {
					// scrivi html
					case "0":
						$html_0 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "1":
						$html_1 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image secondary-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "2":
						$html_2 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image secondary-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "3":
						$html_3 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image secondary-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
				}
				// deselect color
				$selected_color= FALSE;
			}	
		$html .= $html_0 . $html_1 . $html_2 . $html_3;
		$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );

		// do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div>
