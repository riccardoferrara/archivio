window.onload = function() {

    // funct that extracts product name from the permalink
    const getProductName = (href) => {
        var x = href
        return (x = x.split('/'), x = x[x.length - 1].split('-')[0])
    }

    // here we have the list of the products that the customer want they appear on the hp
    let products = [{
            product: 'diamante-long-coat',
            color: 'lightpink',
            category: 'coast-and-jackets'
        },
        {
            product: 'diamante-coat',
            color: 'lightblue',
            category: 'coast-and-jackets'
        },
        {
            product: 'diamante-shor-coat',
            color: 'magenta',
            category: 'coast-and-jackets'
        },
        {
            product: 'diamante-long-coat',
            color: 'rosewood',
            category: 'coast-and-jackets'
        },
        {
            product: 'diamante-coat',
            color: 'black',
            category: 'coast-and-jackets'
        },
        {
            product: 'diamante-shor-coat',
            color: 'silver',
            category: 'coast-and-jackets'
        },
    ]

    let href_template = 'https://www.archiviowebsite.com/plp/[category]/[product]/?attribute_pa_color=[color]&attribute_size='

    var slider

    function ready(callback) {
        // in case the document is already rendered
        if (document.readyState != 'loading') callback();
        // modern browsers
        else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
        // IE <= 8
        else document.attachEvent('onreadystatechange', function() {
            if (document.readyState == 'complete') callback();
        });
    }

    ready(function() {
        //def slider
        slider = document.querySelectorAll('.selected-product-desktop')[0]

        // itera sugli elementi per cambiare href, src, srcset
        for (let i = 1; i < products.length; i++) {
            console.log('update slider elements')
            p = products[i - 1]

            //create href
            let href = href_template.replace('[category]', p['category']).replace('[product]', p['product']).replace('[color]', p['color'])
            console.log('href: ', href)

            //----------------------HREF--------------------------------------
            //change front href in <a> element (last child node with 5 index)
            //----------------------------------------------------------------
            slider.childNodes[1].childNodes[1].childNodes[1].childNodes[i * 2].childNodes[1].childNodes[1].childNodes[5].href = href
            slider.childNodes[1].childNodes[1].childNodes[1].childNodes[1 * 2].childNodes[1].childNodes[1].childNodes[5].href
                //HREF CHANGED

            //----------------------SRC+SRCSET--------------------------------
            // change front srcset of img element (list of sources plus width)
            //----------------------------------------------------------------
            console.log('i: ', i)
            let srcset = slider.childNodes[1].childNodes[1].childNodes[1].childNodes[i * 2].childNodes[1].childNodes[1].childNodes[1].srcset
            let src = slider.childNodes[1].childNodes[1].childNodes[1].childNodes[i * 2].childNodes[1].childNodes[1].childNodes[1].src

            // array containing all the sources and the width 
            srcset_array = srcset.split(/[,]/)
            s = srcset_array[0]

            // each element has a href and a width
            let link = s.split(' ')[0]
            let width = s.split(' ')[1]

            // now lets replace with the new one
            let old_product_name = getProductName(link)
            srcset = srcset.replaceAll(old_product_name, p['product'].replaceAll('-', '_'))
            src = src.replaceAll(old_product_name, p['product'].replaceAll('-', '_'))

            // give it to the element 
            slider.childNodes[1].childNodes[1].childNodes[1].childNodes[i * 2].childNodes[1].childNodes[1].childNodes[1].setAttribute('src', src)
            slider.childNodes[1].childNodes[1].childNodes[1].childNodes[i * 2].childNodes[1].childNodes[1].childNodes[1].setAttribute('srcset', srcset)

            //SRC+SRCSET CHANGED


        }
    });
}