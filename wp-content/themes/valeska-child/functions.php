<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'swiper','valeska-main','valeska-style','valeska-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 25 );

// END ENQUEUE PARENT ACTION




//******************************************************************************/
// ----------------------------------------------------------------------------//
//                RIDEV                                                        //
// ----------------------------------------------------------------------------//
//******************************************************************************/

//---------------------------------------------
// DECLARE SUPPORT THEME FOR WOOCOMMERCE
//---------------------------------------------
function ridev_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'ridev_add_woocommerce_support' );


//---------------------------------------------
// RECUPERO IL COLORE SELEZIONATO DALLA PDP
//---------------------------------------------
global $product;

//hook javascript per lettura colori nella single-product page
add_action('woocommerce_after_single_variation','custom_jquery_shop_script');
//funzione che aggiunge javascript per la lettura dei colori
function custom_jquery_shop_script(){
        ?>
            <script type="text/javascript" defer>
            var colors
            var selected_color
            if ( document.querySelectorAll('[for="pa_size"]')[0]){
                document.querySelectorAll('[for="pa_size"]')[0].innerText = 'SIZE'
            }
            if (document.querySelectorAll('[for="size"]')[0]){
                document.querySelectorAll('[for="size"]')[0].innerText = 'SIZE'
            }
            if (document.querySelectorAll('[for="pa_color"]')[0]) {
                document.querySelectorAll('[for="pa_color"]')[0].innerText = 'COLOR'
            }



            //inserisci delle funzioni click per ogni colore
            function assignOnClickBehavoir(colors){
                for (var i=0, iLen=colors.length; i<iLen; i++) {
                    colors[i].onclick = function() {
                        selected_color = this.dataset.value
                        // alert(selected_color)
                        showSelectedColorVariation()
                        showSelectedColorDescription()
                    }
                }
            }

            //non appena l'elemento colors viene caricato sulla pagina viene generata la variabile colors
            function waitForColorsElements(){
                if(typeof document.querySelectorAll("form.variations_form.cart.wvs-loaded [name='color']")[0] !== "undefined"){
                    //l'elemento esiste sulla pagina ora carichiamola in "colors"
                    colors = document.querySelectorAll("form.variations_form.cart.wvs-loaded [name='color']")
                    assignOnClickBehavoir(colors)
                }
                else{
                    setTimeout(waitForColorsElements, 250);
                }
            }
            waitForColorsElements()
            //definisco una funzione che nasconde un elemento imagine cambiando la classe
            function hideImageElement(imgElement){
                console.log(imgElement)
                imgElement.classList.remove('selected-color')
                imgElement.classList.add('unselected-color')
            }
            function showImageElement(imgElement){
                // alert('start shoImageElement')
                console.log(imgElement)
                imgElement.classList.remove('unselected-color')
                imgElement.classList.add('selected-color')
            }
            //nascondo le foto del colore non selezionato e rendo visibili quelle del colore selezionato
            function showSelectedColorVariation(){
                // var imagesElementOfShownColor = []
                // imagesElementOfShownColor = document.getElementsByClassName("selected-color");
                // for (var i =0; i < imagesElementOfShownColor.length; i++)
                // {
                //     console.log(i)
                //     hideImageElement(imagesElementOfShownColor[i])
                // }
                console.log('start hide elements')
                while (document.getElementsByClassName("selected-color")[0] != undefined){
                    hideImageElement(document.getElementsByClassName("selected-color")[0])
                }
                console.log('end hide elements')
                var imagesElementOfTheColorToShow = []
                imagesElementOfTheColorToShow = document.querySelectorAll('[color="'+ selected_color + '"]')
                for (var i =0; i< imagesElementOfTheColorToShow.length; i++){
                    showImageElement(imagesElementOfTheColorToShow[i])
                }
            }
            // show the name of the selected color on the page
            function showSelectedColorDescription(){
                color_label = document.querySelectorAll('[value="'+ selected_color+'"]')[0].text
                document.querySelectorAll('[for="pa_color"]')[0].innerText = color_label           
            }
            </script>
        <?php
}

// function for alert in php
function alert($msg){
    ?>
    <script type="text/javascript">
        var msg = <?php echo json_encode($msg); ?>;
        alert(msg)
    </script>
<?php
};

// function for console_log in php
function console_log($msg){
    ?>
    <script type="text/javascript">
        var msg = <?php echo json_encode($msg); ?>;
        console.log(msg)
    </script>
<?php
};

//alert(wc_get_product( $product_id ));


//---------------------------------
// CREAZIONE IMMAGINI NELLA PDP
//---------------------------------

// global $product;

// function ridev_product_images (){
// 	$product_image_ids = $GLOBALS['product']->get_gallery_image_ids();
// 	$images_colors_url = [];
// 	foreach($product_image_ids as &$id) {
// 		$thumbnail_url = wp_get_attachment_image_url($id);
// 		$color = explode('-',end(explode('/',$thumbnail_url)))[1];
// 		$fullsize_image_url = preg_split("/....[x]+/", $thumbnail_url)[0] .'.png';
// 		$position = filter_var(explode('-',end(explode('/',$thumbnail_url)))[0], FILTER_SANITIZE_NUMBER_INT);
// 		$images_colors_url["$color-$position"] = $fullsize_image_url;
// 	}
// 	return $images_colors_url;
// };


// $post_thumbnail_id = $product->get_image_id();


// $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
// // $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
// $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', ridev_product_images()['black-1'], esc_html__( 'Awaiting product image', 'woocommerce' ) );
// $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', ridev_product_images()['black-2'], esc_html__( 'Awaiting product image', 'woocommerce' ) );
// $html .= '</div>';

// echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

// do_action( 'woocommerce_product_thumbnails' );
