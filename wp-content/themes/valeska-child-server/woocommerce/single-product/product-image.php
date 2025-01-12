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
		$fullsize_image_url = preg_split("/....[x]+/", $url)[0] .'-1535x2048'.'.'.$extension;
		$position = explode('-',end(explode('/',$url)))[2];
		$images_colors_url["$color-$position"] = $fullsize_image_url;
	}
	return $images_colors_url;
};


$images_colors_url = ridev_product_images(); 
// $images_colors_url["black-0"] = "URL immagine big" ecc.

// Se preferisci un numero preciso di colonne puoi assegnarlo direttamente (es: $columns = 4;)
$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );

$wrapper_classes = array(
    'woocommerce-product-gallery',
    'woocommerce-product-gallery--columns-' . absint( $columns ),
    'images',
);

// Prepariamo i contenitori
$thumbnails_html = '';
$big_images_html = '';

// Variabili di controllo
$selected_color = true;
$first_loop     = true;
$loop_color     = '';

// Variabili dove accumuliamo le immagini in base alle posizioni
$html_0  = '';
$html_00 = '';
$html_1  = '';
$html_2  = '';
$html_3  = '';

foreach ( $images_colors_url as $color_position => $image_url ) {

    list($color, $pos) = explode( '-', $color_position );

    // Se “pos” è '00', lo rinominiamo 'still2' (come nel tuo codice originale)
    if ( $pos === '00' ) {
        $pos = 'still2';
    }

    // Se cambia il colore (es. da black a taupe), “chiudiamo” il blocco del colore precedente
    if ( $loop_color !== $color ) {
        if ( ! $first_loop ) {
            // Aggiungiamo le immagini grandi accumulate per il colore precedente
            $big_images_html .= $html_0 . $html_00 . $html_1 . $html_2 . $html_3;

            // Reset
            $html_0  = '';
            $html_00 = '';
            $html_1  = '';
            $html_2  = '';
            $html_3  = '';
        }
        $loop_color = $color;
        $first_loop = false;
    }

    // ID univoco per ogni immagine “grande”
    $img_id = 'img-' . $color . '-' . $pos;

    // Creiamo la miniatura, che linka con un anchor a `#{$img_id}`
    $thumbnails_html .= sprintf(
        '<div class="product-thumbnail">
            <a href="#%1$s">
                <img loading="lazy" src="%2$s" alt="Thumbnail for %3$s" />
            </a>
        </div>',
        esc_attr( $img_id ),
        esc_url( $image_url ),
        esc_html( $color_position )
    );

    // Classe per indicare se è la prima immagine del primo colore
    $visibility_class = ( $selected_color ) ? 'selected-color' : 'unselected-color';

    // Creiamo l’immagine grande come <img> (senza div)
    $big_image_element = sprintf(
        '<img 
            id="%1$s" 
            class="wp-post-image %2$s" 
            color="%3$s" 
            loading="lazy" 
            src="%4$s" 
            alt="%5$s" 
        />',
        esc_attr( $img_id ),
        esc_attr( $visibility_class ),
        esc_attr( $color ),
        esc_url( $image_url ),
        esc_html__( 'Awaiting product image', 'woocommerce' )
    );

    // In base al “pos” scegliamo dove accumularlo
    switch ( $pos ) {
        case '0':
            $html_0  = $big_image_element;
            break;
        case 'still2':
            $html_00 = $big_image_element;
            break;
        case '1':
            $html_1  = $big_image_element;
            break;
        case '2':
            $html_2  = $big_image_element;
            break;
        case '3':
            $html_3  = $big_image_element;
            break;
    }

    // Dopo la prima immagine, la classe “selected-color” non verrà più aggiunta
    $selected_color = false;
}

// Aggiungiamo gli ultimi blocchi di immagini (per l’ultimo colore in coda)
$big_images_html .= $html_0 . $html_00 . $html_1 . $html_2 . $html_3;

// Creiamo l’output finale
// 1) colonna delle miniature
// 2) colonna delle immagini grandi
// 3) eventuale SKU nascosto
$html  = '<div class="woocommerce-product-gallery__thumbnails">';
$html .= $thumbnails_html;
$html .= '</div>';

$html .= '<div class="woocommerce-product-gallery__large-images">';
$html .= $big_images_html;
$html .= '</div>';

$sku = $product->get_sku();
$html .= sprintf('<div class="sku not-selected">%s</div>', esc_html( $sku ));

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>"
     data-columns="<?php echo esc_attr( $columns ); ?>"
     style="opacity: 0; transition: opacity .25s ease-in-out;">
    <figure class="woocommerce-product-gallery__wrapper">
        <?php
            // Stampa l’HTML delle miniature + immagini grandi
            echo $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        ?>
    </figure>
</div>
