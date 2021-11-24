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


// ----------------------------------------------------------------------------//
//                RIDEV                                                        //
// ----------------------------------------------------------------------------//

//here some test 
add_action('woocommerce_before_single_product','print_color');
function print_color(){
    echo '<h2>blue</h2>';
}

//here some test
add_action( 'woocommerce_before_single_product', 'quadlayers_woocommerce_hooks');
function quadlayers_woocommerce_hooks() {
echo '<img src="https://kokohai.com/wp-content/uploads/2020/02/logo-kokohai-tienda-de-merchandising-de-anime-y-maga-e1584570981420.png">'; // Change to desired image url
}


//hook javascript per lettura colori nella single-product page
add_action('woocommerce_after_single_variation','custom_jquery_shop_script');
//funzione che aggiunge javascript per la lettura dei colori
function custom_jquery_shop_script(){
        ?>
            <script type="text/javascript">
            var colors

            //inserisci delle funzioni click per ogni colore
            function assignOnClickBehavoir(colors){
                for (var i=0, iLen=colors.length; i<iLen; i++) {
                    colors[i].onclick = function() {
                        alert(this.dataset.value)
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
            </script>
        <?php
}

