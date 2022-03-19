//----------------------PRODUCTS----------------------------------
//              OUR SELECTION PRODUCTS
//----------------------------------------------------------------

// funct that extracts product name from the permalink
const getProductName = (href) => {
    var x = href
    return (x = x.split('/'), x = x[x.length - 1].split('-')[0])
}

const getColor = (href) => {
    var x = href
    return (x = x.split('/'), x = x[x.length - 1].split('-')[1])
}

// here we have the list of the products that the customer want they appear on the hp
let products = [{
        product: 'Diamante-Long-Coat',
        color: 'LightPink',
        category: 'coast-and-jackets',
        title: 'DIAMANTE LONG COAT',
        price: '720.00'
    },
    {
        product: 'Diamante-Short-Coat',
        color: 'Magenta',
        category: 'coast-and-jackets',
        title: 'DIAMANTE SHORT COAT',
        price: '620.00'
    },
    {
        product: 'Diamante-Coat',
        color: 'Acqua',
        category: 'coast-and-jackets',
        title: 'DIAMANTE COAT',
        price: '660.00'
    },
    {
        product: 'Diamante-Long-Coat',
        color: 'Rosewood',
        category: 'coast-and-jackets',
        title: 'DIAMANTE LONG COAT',
        price: '720'
    },
    {
        product: 'Diamante-Short-Coat',
        color: 'Silver',
        category: 'coast-and-jackets',
        title: 'DIAMANTE SHORT COAT',
        price: '620.00'
    },
    {
        product: 'Diamante-Coat',
        color: 'Black',
        category: 'coast-and-jackets',
        title: 'DIAMANTE COAT',
        price: '660.00'
    },
]

let href_template = 'https://www.archiviowebsite.com/plp/[category]/[product]/?attribute_pa_color=[color]&attribute_size='

const updateOurSelectionProducts = () => {
    //def slider
    var slider = document.querySelectorAll('.selected-product-desktop')[0]

    //def <a> elements inside the slider
    var a_elements = document.querySelectorAll('.woocommerce-LoopProduct-link.woocommerce-loop-product__link')

    //def <img> elements inside the slider
    var imgs_elements = document.querySelectorAll('.attachment-full.size-full.wp-post-image')

    //def <a> elements containing the product title
    var titles_elements = document.querySelectorAll('h6.qodef-woo-product-title a')

    //def <span> elements containint the product price
    var prices_elements = document.querySelectorAll('.selected-product-desktop .woocommerce-Price-amount.amount')

    // update slider standard imgs
    // there are 12 elements in the slider, the first 2 are the last two so we start with the product n5 (index 4):
    let i = 4 //the index of the product while j is the index of the slider element
    for (let j = 0; j < 11; j++) {
        let slider_index = j
        console.log('product index: ', i)
        console.log('slider index: ', j)
        updateOurSelectionProduct(products[i], slider_index, a_elements, imgs_elements, titles_elements, prices_elements)
        i++
        if (i == 6) { i = 0 }
    }
}

const updateOurSelectionProduct = (p, index, a_elements, imgs_elements, titles_elements, prices_elements) => {
    console.log('update slider elements')

    //create href
    let href = href_template.replace('[category]', p['category']).replace('[product]', p['product']).replace('[color]', p['color'])
    console.log('href: ', href)

    //----------------------HREF--------------------------------------
    //change front href of <a> element 
    //----------------------------------------------------------------
    a_elements[index].href = href

    //HREF CHANGED

    //----------------------SRC+SRCSET--------------------------------
    // change front srcset of img element (list of sources plus width)
    //----------------------------------------------------------------
    console.log('i: ', index)
    let srcset = imgs_elements[index].srcset
    let src = imgs_elements[index].src

    // array containing all the sources and the width 
    srcset_array = srcset.split(/[,]/)
    s = srcset_array[0]

    // each element has a href and a width
    let link = s.split(' ')[0]
        // let width = s.split(' ')[1]

    // now lets replace with the new one
    let old_product_name = getProductName(link)
    let old_color = getColor(link)
    srcset = srcset.replaceAll(old_product_name, p['product'].replaceAll('-', '_')).replaceAll(old_color, p['color'])
    src = src.replaceAll(old_product_name, p['product'].replaceAll('-', '_')).replaceAll(old_color, p['color'])

    // give it to the element 
    imgs_elements[index].setAttribute('src', src)
    imgs_elements[index].setAttribute('srcset', srcset)

    //SRC+SRCSET CHANGED

    //----------TITLE+PRICE+TITLE_href--------------------------------
    // update the product title, the price and the title href
    //----------------------------------------------------------------
    titles_elements[index].innerHTML = p['title']
    titles_elements[index].href = href
    prices_elements[index].innerHTML = `<a href="${href}"><span class=\"woocommerce-Price-currencySymbol\">$</span>${p['price']}</a>`

}

// window.onload = function() {
//     function ready(callback) {
//         // in case the document is already rendered
//         if (document.readyState != 'loading') callback();
//         // modern browsers
//         else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
//         // IE <= 8
//         else document.attachEvent('onreadystatechange', function() {
//             if (document.readyState == 'complete') callback();
//         });
//     }


//     ready(function() {

//         //non appena l'elemento colors viene caricato sulla pagina viene generata la variabile colors
//         function waitForElements() {
//             if (document.querySelectorAll('.woocommerce-LoopProduct-link.woocommerce-loop-product__link').length >= 12) {
//                 updateOurSelectionProducts()
//             } else {
//                 console.log('length: ', document.querySelector('.woocommerce-LoopProduct-link.woocommerce-loop-product__link').length)
//                 setTimeout(waitForElements, 250);
//             }
//         }
//         waitForElements()

//     })

// }

window.onload = (event) => {
    updateOurSelectionProducts()
}