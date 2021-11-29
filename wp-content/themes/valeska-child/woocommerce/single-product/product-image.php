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
		$fullsize_image_url = preg_split("/....[x]+/", $thumbnail_url)[0] .'.png';
		$position = filter_var(explode('-',end(explode('/',$thumbnail_url)))[0], FILTER_SANITIZE_NUMBER_INT);
		$images_colors_url["$color-$position"] = $fullsize_image_url;
	}
	return $images_colors_url;
};





?>

<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		if ( $post_thumbnail_id ) {
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			//questa parte inserisce le foto al primo giro
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			// $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			//recupera gli url delle foto
			$images_colors_url = ridev_product_images();
			// crea codice html con tutte le foto
			$i = 1;
			foreach(array_values($images_colors_url) as &$image_url){
				//se la foto è la terza o la quarta cambia stile (width 50%)
				// $img_index = abs(filter_var(array_keys($image_url,FILTER_SANITIZE_NUMBER_INT)));
				// $html .= sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image" />', $image_url, esc_html__( 'Awaiting product image', 'woocommerce' ) );

				switch($i) {
					case 1:
					case 2:
						$html .= sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image" />', $image_url, esc_html__( 'Awaiting product image', 'woocommerce' ) );
						break;
					case 3:
					case 4:
						$html .= sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image" style="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), 'max-width: 50%;' );
						break;
					case 5:
						$html .= sprintf( '<img loading=lazy src="%s" alt="%s" class="wp-post-image" style="%s"/>', $image_url, esc_html__( 'Awaiting product image', 'woocommerce'), 'max-width: 50%;' );
						$i = 0;
						break;
					}
			$i++;
			}	
		$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div>

