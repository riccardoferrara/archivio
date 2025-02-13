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
// RANDOM ORDER IN PLP
//---------------------------------------------

add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
 
function custom_woocommerce_get_catalog_ordering_args( $args ) {
$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
 
if ( 'random_list' == $orderby_value ) {
$args['orderby'] = 'rand';
$args['order'] = '';
$args['meta_key'] = '';
}
return $args;
}
 
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
 
function custom_woocommerce_catalog_orderby( $sortby ) {
$sortby['random_list'] = 'Random';
return $sortby;
}


//---------------------------------------------
// DECLARE SUPPORT THEME FOR WOOCOMMERCE
//---------------------------------------------
function ridev_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'ridev_add_woocommerce_support' );


//--------------------------------------------------------
// OVERRIDE ADD TO CART VARIATION SCRIPT FROM WOOCOMMERCE
//--------------------------------------------------------
add_action('wp_enqueue_scripts', 'override_woo_frontend_scripts');
function override_woo_frontend_scripts() {
    wp_deregister_script('wc-add-to-cart-variation');
    wp_enqueue_script('wc-add-to-cart-variation', '/wp-content/themes/valeska-child-server/woocommerce/assets/js/frontend/add-to-cart-variation.min.js', array('jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n'), null, true);
}


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
//hook javascript per spostare la scritta sizechart
add_action('woocommerce_after_add_to_cart_form', 'move_size_chart_title');
//hook javascript per cambiare la forma delle foto dei related products
add_action('woocommerce_after_add_to_cart_form', 'related_products');
//hook javascript per aggiungere links alle foto
add_action('woocommerce_after_add_to_cart_form', 'add_links');
//hook javascript per gestire il mobile carousel
add_action('woocommerce_after_add_to_cart_form', 'carousel');

//funzione che aggiunge js per gestire il comportamento del carousel mobile            
function carousel(){
    ?>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/pdp-carousel.js" type="text/javascript" defer></script>
    <?php
}

//funzione che aggiunge js per aggiungere i links alle immagini       
function add_links(){
    ?>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/pdp-add-links.js" type="text/javascript" defer></script>
    <?php
}

//funzione che aggiunge js per gestire i related_products della pdp            
function related_products(){
    ?>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/pdp-related-products.js" type="text/javascript" defer></script>
    <?php
}

//funzione per aggiunge il js spostare il label size_chart  
function move_size_chart_title(){
    ?>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/pdp-move-size-chart-title.js" type="text/javascript" defer></script>
    <?php
}

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

            // if page is loaded from query, color is in the address
            selected_color = window.location.href.split('/').slice(-1)[0].split(/[=?&]/)[2]

            // if page is loaded after add to cart, color is just selected but not in the address
            if (!selected_color) {
                selected_color = document.querySelectorAll("[role='radio'][name='color'][aria-checked='true']")[0].dataset.value
            }

            let has_size = false

            if ( document.querySelectorAll('[for="pa_size"]')[0]){
                document.querySelectorAll('[for="pa_size"]')[0].innerText = 'SIZE'
                has_size = true
            }
            if (document.querySelectorAll('[for="size"]')[0]){
                document.querySelectorAll('[for="size"]')[0].innerText = 'SIZE'
                has_size = true
            }
            if (document.querySelectorAll('[for="pa_color"]')[0]) {
                document.querySelectorAll('[for="pa_color"]')[0].innerText = 'COLOR'
            }

            if (has_size){
                //muovi le etichette COLOR and SIZE da accanto a sopra nelle tabelle
                //recupera la tabella
                let var_table = document.querySelector('table.variations')
                //recupera la cella contenente il colore e la taglia per ricopiarla dopo
                var color_th = var_table.childNodes[1].childNodes[1].childNodes[1]
                var size_th = var_table.childNodes[1].childNodes[3].childNodes[1]
                // rimuovi vecchie etichette
                var_table.childNodes[1].childNodes[1].deleteCell(0)
                var_table.childNodes[1].childNodes[3].deleteCell(0)
                // inserisci due nuove righe
                var color_row = var_table.insertRow(0)
                var size_row = var_table.insertRow(2)
                // inserisci una cella per riga
                var color = color_row.insertCell(0)
                var size = size_row.insertCell(0)
                color.outerHTML = color_th.outerHTML
                size.outerHTML = size_th.outerHTML         
            } else {
                //muovi l'etichetta COLOR da accanto a sopra nelle tabelle
                //recupera la tabella
                let var_table = document.querySelector('table.variations')
                //color:
                //recupera la cella contenente il colore e la taglia per ricopiarla dopo
                var color_th = var_table.childNodes[1].childNodes[1].childNodes[1]
                // rimuovi vecchie etichette
                var_table.childNodes[1].childNodes[1].deleteCell(0)
                // inserisci due nuove righe
                var color_row = var_table.insertRow(0)
                // inserisci una cella per riga
                var color = color_row.insertCell(0)
                color.outerHTML = color_th.outerHTML
            }

            //inserisci delle funzioni click per ogni colore
            function assignOnClickBehavoir(colors){
                for (var i=0, iLen=colors.length; i<iLen; i++) {
                    colors[i].onclick = function() {
                        selected_color = this.dataset.value
                        // alert(selected_color)
                        // console.log('selected_color: ', selected_color)
                        showSelectedColorVariation()
                        showSelectedColorDescription()
                        showSelectedColorVariationMobile()
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
                // console.log(imgElement)
                imgElement.classList.remove('selected-color')
                imgElement.classList.add('unselected-color')
            }
            function showImageElement(imgElement){
                // alert('start shoImageElement')
                // console.log(imgElement)
                imgElement.classList.remove('unselected-color')
                imgElement.classList.add('selected-color')
            }
            //nascondo le foto del colore non selezionato e rendo visibili quelle del colore selezionato
            function showSelectedColorVariation() {
                // var imagesElementOfShownColor = []
                // imagesElementOfShownColor = document.getElementsByClassName("selected-color");
                // for (var i =0; i < imagesElementOfShownColor.length; i++)
                // {
                //     console.log(i)
                //     hideImageElement(imagesElementOfShownColor[i])
                // }
                // console.log('start hide elements')
                while (document.getElementsByClassName("selected-color")[0] != undefined) {
                    hideImageElement(document.getElementsByClassName("selected-color")[0]);
                }

                // Ottieni le immagini del colore selezionato
                var imagesElementOfTheColorToShow = [];
                imagesElementOfTheColorToShow = document.querySelectorAll('[color="' + selected_color + '"]');
                
                // Mostra le immagini del colore selezionato
                for (var i = 0; i < imagesElementOfTheColorToShow.length; i++) {
                    showImageElement(imagesElementOfTheColorToShow[i]);
                }

                // Rimuovi la classe `active-thumbnail` da tutti i div `.product-thumbnail`
                const activeThumbnails = document.querySelectorAll('.product-thumbnail.active-thumbnail');
                activeThumbnails.forEach((thumbnail) => {
                    thumbnail.classList.remove('active-thumbnail');
                });

                // Aggiungi la classe `active-thumbnail` al div contenente la prima immagine del colore selezionato
                if (imagesElementOfTheColorToShow.length > 0) {
                    const firstImageParent = imagesElementOfTheColorToShow[0].closest('.product-thumbnail');
                    if (firstImageParent) {
                        firstImageParent.classList.add('active-thumbnail');
                    }

                    // SCROLL alla prima immagine grande della variazione selezionata
                    imagesElementOfTheColorToShow[0].scrollIntoView({
                        behavior: 'smooth', // Scorrimento fluido
                        block: 'center'     // Centra la prima immagine nello schermo
                    });
                }
            }

            // show the name of the selected color on the page
            function showSelectedColorDescription(){
                color_label = document.querySelectorAll('[value="'+ selected_color+'"]')[0].text
                document.querySelectorAll('[for="pa_color"]')[0].innerText = color_label           
            }

            //update photos as requested product
            function updatePhotos(){
                // console.log('req_color: '. selected_color)
                if (selected_color) {
                    showSelectedColorVariation()
                    showSelectedColorDescription()
                    showSelectedColorVariationMobile()
                }
            }
            updatePhotos()


            //------------------------------
            //       MOBILE                 
            //------------------------------

            // 1) this first part prepare the page removing all imgs *

            //Update name attribute with color for all mobile images
            function addColorAttribute(){
                var imgs = document.querySelectorAll('.swiper-slide-image')
                Object.entries(imgs).map(img => {
                    img[1].parentElement.parentElement.setAttribute('color', img[1].alt.split('-')[1].toLowerCase())
                    img[1].parentElement.parentElement.classList.add('img-mobile')
                })
            }
            addColorAttribute()

            // 2) these are the functions used when user clicks on a color
            function showSelectedColorVariationMobile(){
                var desktop_imgs = document.querySelectorAll('.selected-color')
                var mobile_imgs = document.querySelectorAll('.swiper-slide-image')
                let data_swiper_slide_index = [3, 0, 1, 2, 3, 0]
                Object.entries(mobile_imgs).forEach((mobile_img, i) => {
                    mobile_imgs[i].src = desktop_imgs[data_swiper_slide_index[i]].src
                    mobile_imgs[i].alt = desktop_imgs[data_swiper_slide_index[i]].src
                })
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
        <!-- The grey div to show when the sidebar is opened -->
        <div class="grey-div not-displayed" onclick="toggleSidebar()"></div>
        <!--The acutal content of the sidebar-->
        <div class="sidebar" id="sidebar">
            <div class="container-liner">
                <div class="row">
                    <h4 class="sc-title size"> <strong>SIZE CHART</strong> </h4>
                </div>
                <div class="row">
                    <table class="sc-table">
                        <tr>
                            <td><strong>UNI</strong></td>
                            <td><strong>XXS</strong></td>
                            <td><strong>XS-S</strong></td>
                            <td><strong>S-M</strong></td>
                            <td><strong>M-L</strong></td>
                            <td><strong>L-XL</strong></td>
                            <td><strong>XL</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Germania</strong></td>
                            <td>32</td>
                            <td>34</td>
                            <td>36</td>
                            <td>38</td>
                            <td>40</td>
                            <td>42</td>
                        </tr>
                        <tr>
                            <td><strong>Italia</strong></td>
                            <td>38</td>
                            <td>40</td>
                            <td>42</td>
                            <td>44</td>
                            <td>46</td>
                            <td>48</td>
                        </tr>
                        <tr>
                            <td><strong>Francia</strong></td>
                            <td>34</td>
                            <td>36</td>
                            <td>38</td>
                            <td>40</td>
                            <td>42</td>
                            <td>44</td>
                        </tr>
                        <tr>
                            <td><strong>Regno Unito</strong></td>
                            <td>6</td>
                            <td>8</td>
                            <td>10</td>
                            <td>12</td>
                            <td>14</td>
                            <td>16</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>USA</strong></td>
                            <td>2</td>
                            <td>4</td>
                            <td>6</td>
                            <td>8</td>
                            <td>10</td>
                            <td>12</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <h4 class="sc-title measurement not-displayed"> <span><strong>MEASUREMENT</strong></span> <span class="sc-subtitle">All mesaurements are listed in centimetres.</span> </h4>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed CDD-22C">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                            <td>E</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Sleeve</strong></td>
                            <td><strong>Shoulder width</strong></td>
                            <td><strong>Chest</strong></td>
                            <td><strong>Waist</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>72,5</td>
                            <td>64,2</td>
                            <td>42,4</td>
                            <td>59,5</td>
                            <td>52,4</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>74</td>
                            <td>64,6</td>
                            <td>43,6</td>
                            <td>61,2</td>
                            <td>54,2</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>75,5</td>
                            <td>65</td>
                            <td>44,6</td>
                            <td>63</td>
                            <td>56</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>77</td>
                            <td>65,4</td>
                            <td>45,8</td>
                            <td>64,7</td>
                            <td>57,8</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>78,5</td>
                            <td>65,8</td>
                            <td>47</td>
                            <td>66,6</td>
                            <td>59,7</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>80</td>
                            <td>66,2</td>
                            <td>48,2</td>
                            <td>68,5</td>
                            <td>61,7</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed CDD-22L">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                            <td>E</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Sleeve</strong></td>
                            <td><strong>Shoulder width</strong></td>
                            <td><strong>Chest</strong></td>
                            <td><strong>Waist</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>134</td>
                            <td>64,2</td>
                            <td>42,4</td>
                            <td>59,5</td>
                            <td>52,4</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>135,5</td>
                            <td>64,6</td>
                            <td>43,6</td>
                            <td>61,2</td>
                            <td>54,2</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>137</td>
                            <td>65</td>
                            <td>44,6</td>
                            <td>63</td>
                            <td>56</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>138,5</td>
                            <td>65,4</td>
                            <td>45,8</td>
                            <td>64,7</td>
                            <td>57,8</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>140</td>
                            <td>65,8</td>
                            <td>47</td>
                            <td>66,6</td>
                            <td>59,7</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>141,5</td>
                            <td>66,2</td>
                            <td>48,2</td>
                            <td>68,5</td>
                            <td>61,7</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed CDD-22M">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                            <td>E</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Sleeve</strong></td>
                            <td><strong>Shoulder width</strong></td>
                            <td><strong>Chest</strong></td>
                            <td><strong>Waist</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>96,5</td>
                            <td>64,2</td>
                            <td>42,4</td>
                            <td>59,5</td>
                            <td>52,4</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>98</td>
                            <td>64,6</td>
                            <td>43,6</td>
                            <td>61,2</td>
                            <td>54,2</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>99,5</td>
                            <td>65</td>
                            <td>44,6</td>
                            <td>63</td>
                            <td>56</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>101</td>
                            <td>65,4</td>
                            <td>45,8</td>
                            <td>64,7</td>
                            <td>57,8</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>102,5</td>
                            <td>65,8</td>
                            <td>47</td>
                            <td>66,6</td>
                            <td>59,7</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>104</td>
                            <td>66,2</td>
                            <td>48,2</td>
                            <td>68,5</td>
                            <td>61,7</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed FOULARD-01">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Width</strong></td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>OS</strong></td>
                            <td>60</td>
                            <td>50</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed PANT-03">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                            <td><strong>Half thigh</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>114</td>
                            <td>32</td>
                            <td>48</td>
                            <td>31,5</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>115</td>
                            <td>34</td>
                            <td>50</td>
                            <td>32,7</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>116</td>
                            <td>36</td>
                            <td>52</td>
                            <td>34</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>117</td>
                            <td>38</td>
                            <td>54</td>
                            <td>35,3</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>118</td>
                            <td>40</td>
                            <td>56</td>
                            <td>36,6</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>119</td>
                            <td>42</td>
                            <td>58</td>
                            <td>38</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed PANT-U04">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                            <td><strong>Half thigh</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>115</td>
                            <td>42</td>
                            <td>56</td>
                            <td>35</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>118</td>
                            <td>43,5</td>
                            <td>58</td>
                            <td>36,2</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>118</td>
                            <td>43,5</td>
                            <td>60</td>
                            <td>37,5</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>121</td>
                            <td>45</td>
                            <td>62</td>
                            <td>38,7</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>121</td>
                            <td>45</td>
                            <td>64</td>
                            <td>39.9</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>122</td>
                            <td>46</td>
                            <td>66</td>
                            <td>41</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed PANT-W01">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>C</td>
                            <td>D</td>
                            <td>E</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                            <td><strong>Half thigh</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>83,5</td>
                            <td>35</td>
                            <td>48</td>
                            <td>35</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>85</td>
                            <td>37</td>
                            <td>50</td>
                            <td>36,25</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>85</td>
                            <td>39</td>
                            <td>52</td>
                            <td>37,5</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>86,5</td>
                            <td>41</td>
                            <td>54</td>
                            <td>38,75</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>86,5</td>
                            <td>43</td>
                            <td>656</td>
                            <td>40</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>46</strong></td>
                            <td>86,5</td>
                            <td>43</td>
                            <td>56</td>
                            <td>40</td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table class="sc-table not-displayed SKIRT-02">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>72,5</td>
                            <td>30</td>
                            <td>46</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>74,5</td>
                            <td>32</td>
                            <td>48</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>74,5</td>
                            <td>34</td>
                            <td>50</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>74,5</td>
                            <td>36</td>
                            <td>52</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>74,5</td>
                            <td>38</td>
                            <td>54</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>74,5</td>
                            <td>40</td>
                            <td>56</td>
                        </tr>
                    </table>
                </div> 
                <div class="row">
                    <table class="sc-table not-displayed SKIRT-04">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                            <td><strong>Back slit</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>110</td>
                            <td>32</td>
                            <td>46</td>
                            <td>74</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>112</td>
                            <td>34</td>
                            <td>48</td>
                            <td>74</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>114</td>
                            <td>36</td>
                            <td>50</td>
                            <td>74</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>116</td>
                            <td>38</td>
                            <td>52</td>
                            <td>74</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>118</td>
                            <td>40</td>
                            <td>54</td>
                            <td>74</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>118</td>
                            <td>42</td>
                            <td>56</td>
                            <td>74</td>
                        </tr>
                    </table>
                </div> 
                <div class="row">
                    <table class="sc-table not-displayed SKIRT-03">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                            <td><strong>Front slit</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>72,5</td>
                            <td>30</td>
                            <td>46</td>
                            <td>42,5</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>74,5</td>
                            <td>32</td>
                            <td>48</td>
                            <td>44,5</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>74,5</td>
                            <td>34</td>
                            <td>50</td>
                            <td>44,5</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>74,5</td>
                            <td>36</td>
                            <td>52</td>
                            <td>44,5</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>74,5</td>
                            <td>38</td>
                            <td>54</td>
                            <td>44,5</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>74,5</td>
                            <td>40</td>
                            <td>56</td>
                            <td>44,5</td>
                        </tr>
                    </table>
                </div>  
                <div class="row">
                    <table class="sc-table not-displayed PANEL-01">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Width</strong></td>
                        </tr>
                        <tr>
                            <td><strong>70/38</strong></td>
                            <td>68</td>
                            <td>37</td>
                        </tr>
                        <tr>
                            <td><strong>75/40</strong></td>
                            <td>68</td>
                            <td>39</td>
                        </tr>
                        <tr>
                            <td><strong>80/42</strong></td>
                            <td>68</td>
                            <td>41</td>
                        </tr>
                        <tr>
                            <td><strong>85/44</strong></td>
                            <td>68</td>
                            <td>43</td>
                        </tr>
                        <tr>
                            <td><strong>90/46</strong></td>
                            <td>68</td>
                            <td>45</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>95/48</strong></td>
                            <td>68</td>
                            <td>47</td>
                        </tr>
                    </table>
                </div> 
                <div class="row">
                    <table class="sc-table not-displayed SHIRT-01">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                            <td>D</td>
                            <td>E</td>
                            <td>F</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Lenght</strong></td>
                            <td><strong>Sleeve</strong></td>
                            <td><strong>Shoulder width</strong></td>
                            <td><strong>Chest</strong></td>
                            <td><strong>Waist</strong></td>
                            <td><strong>Hip</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>72</td>
                            <td>64</td>
                            <td>40</td>
                            <td>43</td>
                            <td>35</td>
                            <td>45</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>73</td>
                            <td>65</td>
                            <td>41</td>
                            <td>45</td>
                            <td>37</td>
                            <td>47</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>74</td>
                            <td>66</td>
                            <td>42</td>
                            <td>47</td>
                            <td>39</td>
                            <td>49</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>75</td>
                            <td>67</td>
                            <td>43</td>
                            <td>49</td>
                            <td>41</td>
                            <td>51</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>76</td>
                            <td>68</td>
                            <td>44</td>
                            <td>51</td>
                            <td>43</td>
                            <td>53</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>78</td>
                            <td>69</td>
                            <td>45</td>
                            <td>53</td>
                            <td>44</td>
                            <td>54</td>
                        </tr>
                    </table>
                </div>  
                <div class="row">
                    <table class="sc-table not-displayed SHIRT-02">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                            <td>C</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Back lenght</strong></td>
                            <td><strong>Sleeve</strong></td>
                            <td><strong>Chest</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>104</td>
                            <td>62</td>
                            <td>64</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>105</td>
                            <td>63</td>
                            <td>66</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>106</td>
                            <td>64</td>
                            <td>68</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>106</td>
                            <td>64</td>
                            <td>70</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>106</td>
                            <td>64</td>
                            <td>72</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>106</td>
                            <td>64</td>
                            <td>74</td>
                        </tr>
                    </table>
                </div>  
                <div class="row">
                    <table class="sc-table not-displayed TOP-01">
                        <tr class="red">
                            <td></td>
                            <td>A</td>
                            <td>B</td>
                        </tr>
                        <tr>
                            <td><strong>Size</strong></td>
                            <td><strong>Back lenght</strong></td>
                            <td><strong>Chest</strong></td>
                        </tr>
                        <tr>
                            <td><strong>38</strong></td>
                            <td>50,50</td>
                            <td>42</td>
                        </tr>
                        <tr>
                            <td><strong>40</strong></td>
                            <td>51</td>
                            <td>44</td>
                        </tr>
                        <tr>
                            <td><strong>42</strong></td>
                            <td>51,5</td>
                            <td>46</td>
                        </tr>
                        <tr>
                            <td><strong>44</strong></td>
                            <td>52</td>
                            <td>48</td>
                        </tr>
                        <tr>
                            <td><strong>46</strong></td>
                            <td>52,5</td>
                            <td>50</td>
                        </tr>
                        <tr class="last-row">
                            <td><strong>48</strong></td>
                            <td>53</td>
                            <td>52</td>
                        </tr>
                    </table>
                </div>  
                <div class="row">
                    <div class="column">
                        <img class="schema CDD-22C not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/CDD-22C_schema.svg">
                        <img class="schema CDD-22L not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/CDD-22L_schema.svg">
                        <img class="schema CDD-22M not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/CDD-22M_schema.svg">
                        <img class="schema PANEL-01 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/PANEL-01_schema.svg">
                        <img class="schema PANT-03 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/PANT-03_schema.svg">
                        <img class="schema PANT-U04 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/PANT-U04_schema.svg">
                        <img class="schema PANT-W01 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/PANT-W01_schema.svg">
                        <img class="schema SHIRT-11 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/SHIRT-11_schema.svg">
                        <img class="schema SHIRT-01 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/SHIRT-01_schema.svg">
                        <img class="schema SHIRT-02 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/SHIRT-02_schema.svg">
                        <img class="schema SKIRT-02 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/SKIRT-02_schema.svg">
                        <img class="schema SKIRT-03 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/SKIRT-03_schema.svg">
                        <img class="schema SKIRT-04 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/SKIRT-04_schema.svg">
                        <img class="schema TOP-01 not-displayed" src="https://www.archiviowebsite.com/wp-content/uploads/2022/03/TOP-01_schema.svg">
                    </div>
                    <div class="column">
                        <p class="size-description">Size, fit and measurements may vary from one style to another, which means there might be slight differences in items fit.</p>
                    </div>
                </div>
            </div>
        </div>
        <script src="/wp-content/themes/valeska-child-server/assets/js/frontend/pdp-sidebar.js" type="text/javascript" defer></script>
        <style>
            /* table style */
            .sc-title {
                font-family: lato;
                font-size: 12px;
            }
            .sc-subtitle {
                text-transform: none;
                margin-left: 30px;
            }
            table.sc-table {
                font-family: lato;
                font-size: 10px;
                color: black; 
                line-height: 0.7;
                width: 320px;
            }
            table.sc-table tr {
                border-bottom: 1px solid #ddd !important;
            }
            .last-row {
                border-bottom: none !important;
            }
            table.sc-table td {
                border: none;
            }
            .red {
                color: red;
            }
            .not-displayed {
                display: none;
            }
            .displayed {
                display: initial;
            }
            /* end table style */
            /* schema style */
            .row {
                display: flex;
            }
            .column {
                flex: 50%;
                position: relative;
            }
            .schema {
                height: 12rem !important;
                margin: 1.5rem;
            }
            /* end schema style */
            /* size description style */
            .size-description {
                font-family: lato;
                font-size: 10px;
                position: absolute;
                bottom: 0px;
                right: 1rem;
                color: black;
                line-height: normal;
            }
            /* end size description style */
            /* measurement description */
            @media screen and (max-width: 767px){
                .sc-title.measurement.displayed{
                    display: flex;
                }
                .size-description {
                    right: 2rem;
                }
            }

            /* end measurement description */
            /* grey div style */
            .grey-div {
                z-index: 1;
                background-color: rgba(50, 50, 50, 0.5);
                height: 100vh;
                width: 100vw;
                position: fixed;
                left: 0;
                top: 0;
            }
            /* end grey div style */
            .move-to-left {
                transform: translateX(-400px);
            }
            @media screen and (max-width: 767px){
                .move-to-left {
                    transform: translateX(-90%);
                }
            }
            .move-to-left-partly {
                transform: translateX(-200px);
            }
            .sidebar {
                height: 100%;
                width: 400px;
                position: fixed;
                top: 0;
                padding-top: 54px;
                z-index: 2;
                right: -400px;
                background-color: #FFF;
                transition: transform .7s ease-in-out; 
            }
            @media screen and (max-width: 767px){
                .sidebar{
                    width: 394px !important;
                    z-index: 2;
                    top: 0px;
                    right: -380px;
                }
            }
            @media screen and (max-width: 1023px){
                .sidebar{
                    top: 0px;
                    padding-top: 40px;
                    z-index: 2;
                }
            }
            
            .container-liner{
                margin-left:1rem;
            }
            .sidebar-tab {
                display: none;
                height: 100%;
                width: 2rem;
                position: fixed;
                top: 90px;
                z-index: 2;
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