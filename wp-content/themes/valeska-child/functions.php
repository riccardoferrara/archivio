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
//hook javascript per customizzare il comportamento del bottone add_to_cart
add_action('woocommerce_after_add_to_cart_form', 'custom_add_to_cart_button');
//hook javascript per creazione sidebar
add_action('woocommerce_after_add_to_cart_form', 'custom_side_bar');

//funzione che aggiunge js per i comportamenti del bottone "ADD TO CART"            
function custom_add_to_cart_button(){
    ?>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/select-a-size.js" type="text/javascript" defer></script>
    <?php
}

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


//funzione che aggiunge js per i comportamenti del bottone "ADD TO CART"            
function custom_side_bar(){
    ?>
        <!--The pull tab used to open and close the sidebar-->
        <div class="sidebar-tab" id="sidebar-tab">
        <div class="vertical-text text-center" id="sidebar-tab-text" onclick="toggleSidebar()">THIS IS SIDEBAR<span class="arrow vertical-text"></span></div>
        </div>
        <!--The acutal content of the sidebar-->
        <div class="sidebar" id="sidebar">
        <div class="container-liner">
            <h4> Example Sidebar Content</h4>
            <div> This is an example of some sidebar content! Yay! :D</div>
        </div>
        </div>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/pdp-sidebar.js" type="text/javascript" defer></script>
        <style>
            .move-to-left {
  transform: translateX(-400px);
}
.move-to-left-partly {
  transform: translateX(-200px);
}
.sidebar {
  height: 100%;
  width: 400px;
  position: fixed;
  top: 90px;
  z-index: 1;
  right: -400px;
  background-color: #FFF;
  transition: transform .7s ease-in-out;
  
}
.container-liner{
  margin-left:1rem;
}
.sidebar-tab {
  height: 100%;
  width: 2rem;
  position: fixed;
  top: 90px;
  z-index: 1;
  right: -2rem;
  background-color: white;
  transition: transform .7s ease-in-out;
}
#sidebar-tab-text{ 
    width: 400px;
}
.vertical-text {
	transform: rotate(90deg);
	transform-origin: left 2rem;
  vertical-align:middle;
}
.arrow {
  box-sizing: border-box;
  display: inline-block;
  cursor: pointer;
  position: relative;
  transform: rotate(0deg);
  transition: all 0.5s ease-in-out;
  width: 32px;
  height: 32px;
  z-index: 1;
}
.arrow:after, .arrow:before {
  content: "";
  box-sizing: border-box;
  display: block;
  position: absolute;
  transition: all 0.25s ease-in-out;
  border-radius: 10px;
  background: #000;
  width: 16px;
  height: 3.2px;
  top: 14.4px;
}
.arrow:after {
  transform: rotate(44deg);
  left: 3.2px;
}
.arrow:before {
  right: 3.2px;
  transform: rotate(-44deg);
}
.arrow.active:after {
  transform: rotate(-44deg);
}
.arrow.active:before {
  transform: rotate(44deg);
}


.card {
  margin-right: 260px;
  margin-left: 225px;
  box-shadow: .5rem .5rem 2rem rgba(0, 0, 0, 0.4);
}
.card-text {
  color: #000;
}
.btn.btn-primary {
  background-color: #F50057;
  border-color: #F50057;
}
        </style>
    <?php
}