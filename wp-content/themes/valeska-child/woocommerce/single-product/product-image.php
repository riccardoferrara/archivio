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
function get_product_images_urls($product_image_ids){
	$product_images_urls = [];
	$i = 0;
	foreach($product_image_ids as &$id) {
		$product_images_urls[$i] = wp_get_attachment_image_url($id);
		$i++;
	}
	return $product_images_urls;
}

function ridev_product_images (){
	$product_image_ids = $GLOBALS['product']->get_gallery_image_ids();
	$images_colors_url = [];
	$product_images_urls = get_product_images_urls($product_image_ids);
	foreach($product_images_urls as &$url) {
		$color = explode('-',end(explode('/',$url)))[1];
		$extension = end(explode('.',$url));
		$fullsize_image_url = preg_split("/....[x]+/", $url)[0] .'-768x1024'.'.'.$extension;
		$position = explode('-',end(explode('/',$url)))[2];
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
			$i = "";
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
						$html .= $html_0 . $html_00 . $html_1 . $html_2 . $html_3;
						// console_log($html);
						$html_0 = "";
						$html_00 = "";
						$html_1 = "";
						$html_2 = "";
						$html_3 = "";
					}
					$first_loop = FALSE;
				}
				// se è "00" cambio nome altrimenti lo swtich lo contempla nel casso "0" non so perché
				if ($i === "00"){
					$i = "still2";
				}
				switch($i) {
					// scrivi html
					case "0": //prima immagine "still"
						$html_0 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "still2": //seconda immagine eventuale "still"
						$html_00 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "1": //indossato uno (piu' piccola)
						$html_1 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image secondary-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "2": //indossato due (piu' piccola)
						$html_2 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image secondary-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
					case "3": //indossato tre (piu' piccola)
						$html_3 = sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image secondary-image %s" color="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), $visibility_class, $color );
						break;
				}
				// deselect color
				$selected_color= FALSE;
			}	
		$html .= $html_0 . $html_00 . $html_1 . $html_2 . $html_3;
		$html .= '</div>';
		$sku = $product->get_sku();
		$html .= sprintf('<div class="sku not-selected">%s</div>', $sku);
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		// do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div>
