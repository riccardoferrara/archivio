//----------------------PRODUCTS----------------------------------
//                  RELATED PRODUCTS
//----------------------------------------------------------------

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

window.addEventListener('load', (event) => {
    updateRelatedProducts()
});