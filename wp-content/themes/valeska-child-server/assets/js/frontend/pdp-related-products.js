//----------------------PRODUCTS----------------------------------
//                  RELATED PRODUCTS
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

let href_template = 'https://www.archiviowebsite.com/plp/[category]/[product]/?attribute_pa_color=[color]&attribute_size='

const updateRelatedProducts = () => {

    //def <img> elements inside the section
    var imgs_elements = document.querySelectorAll('.up-sells.upsells.products > * img')

    // update imgs
    imgs_elements.forEach(img => {
        updateSrc(img)
    })
}

const updateSrc = (img) => {
    src = img.src.replace('300x300', '1535x2048')
    img.setAttribute('src', src)
}

window.onload = (event) => {
    updateRelatedProducts()
}