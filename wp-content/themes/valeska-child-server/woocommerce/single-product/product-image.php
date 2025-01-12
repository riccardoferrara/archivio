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
// Esempio di output: $images_colors_url["black-0"] = "http://....jpg" ecc.

// Puoi decidere tu quante colonne: se vuoi, lascia pure la filter come nel tuo codice
$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );

// Classi wrapper standard WooCommerce (le tue)
$wrapper_classes = array(
    'woocommerce-product-gallery',
    'woocommerce-product-gallery--columns-' . absint( $columns ),
    'images',
);

// Stringhe in cui accumuliamo l’HTML delle miniature e delle immagini grandi
$thumbnails_html = '';
$big_images_html = '';

// Variabili di controllo per la logica
$selected_color = true; // per marcare la prima immagine di default
$first_loop     = true;
$loop_color     = '';

// Variabili dove accumuliamo le immagini di ogni colore
$html_0  = '';
$html_00 = '';
$html_1  = '';
$html_2  = '';
$html_3  = '';

foreach ( $images_colors_url as $color_position => $image_url ) {

    // Separiamo la parte "colore" dalla parte "posizione"
    list($color, $pos) = explode( '-', $color_position );

    // Se la posizione è “00”, la rinominiamo “still2” (come nel tuo codice)
    if ( $pos === '00' ) {
        $pos = 'still2';
    }

    // Se stiamo passando a un nuovo colore, chiudiamo il blocco del precedente
    if ( $loop_color !== $color ) {
        if ( ! $first_loop ) {
            // Concateno le immagini grandi raccolte finora (del colore precedente)
            $big_images_html .= $html_0 . $html_00 . $html_1 . $html_2 . $html_3;

            // Reset variabili
            $html_0  = '';
            $html_00 = '';
            $html_1  = '';
            $html_2  = '';
            $html_3  = '';
        }
        $loop_color  = $color;
        $first_loop  = false;
    }

    // Definiamo un ID univoco per l’immagine grande, che useremo anche come anchor target
    $img_id = 'img-' . $color . '-' . $pos;

    // Stabiliamo la “visibility class” (solo la prima immagine globale sarà `selected-color`)
    $visibility_class = $selected_color ? 'selected-color' : 'unselected-color';

    // -----------------------------
    //  A) Creazione della MINIATURA
    // -----------------------------
    // Oltre al link di ancoraggio, aggiungiamo la stessa visibility_class e l’attributo color
    $thumbnails_html .= sprintf(
        '<div class="product-thumbnail">
            <a href="#%1$s">
                <img 
                    loading="lazy" 
                    class="%2$s" 
                    color="%3$s"
                    src="%4$s" 
                    alt="Thumbnail for %5$s" 
                />
            </a>
        </div>',
        esc_attr( $img_id ),             // #id di destinazione
        esc_attr( $visibility_class ),   // selected/unselected
        esc_attr( $color ),             // attributo color="xxx"
        esc_url( $image_url ),           
        esc_html( $color_position )      // alt text
    );

    // ---------------------------
    //  B) Creazione IMMAGINE GRANDE
    // ---------------------------
    // Anche qui aggiungiamo la stessa visibility_class e l’attributo color
    $big_image_element = sprintf(
        '<img 
            id="%1$s"
            class="wp-post-image %2$s" 
            color="%3$s"
            loading="lazy" 
            src="%4$s" 
            alt="%5$s" 
        />',
        esc_attr( $img_id ),           // ID per l’ancoraggio
        esc_attr( $visibility_class ), // es. “selected-color”
        esc_attr( $color ),            // attributo color="xxx"
        esc_url( $image_url ),
        esc_html__( 'Awaiting product image', 'woocommerce' )
    );

    // In base alla posizione, lo accumuliamo nella variabile corrispondente
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

    // Dopo la prima immagine, la prossima sarà "unselected-color"
    $selected_color = false;
}

// Chiudiamo l’ultimo blocco (per l’ultimo colore incontrato)
$big_images_html .= $html_0 . $html_00 . $html_1 . $html_2 . $html_3;

// Componiamo l'HTML finale
// 1) colonna thumbnails
// 2) colonna immagini grandi
// 3) SKU nascosto
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
            // Stampa l’HTML costruito
            echo $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        ?>
    </figure>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Seleziona tutte le miniature
    const thumbnails = document.querySelectorAll('.product-thumbnail a');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function (event) {
            // Evita il comportamento predefinito del link
            event.preventDefault();

            // Prendi l'ID di destinazione dal link
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                // Scorri all'immagine corrispondente in modo fluido
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center', // Posiziona l'immagine al centro
                });
            }

            // Rimuovi la classe 'active-thumbnail' da tutte le miniature
            thumbnails.forEach(thumb => thumb.parentElement.classList.remove('active-thumbnail'));

            // Aggiungi la classe 'active-thumbnail' solo alla miniatura cliccata
            this.parentElement.classList.add('active-thumbnail');
        });
    });
});
</script>
