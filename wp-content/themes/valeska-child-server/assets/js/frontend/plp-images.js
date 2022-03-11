// change imgs resolution to load page faster
function changeImgsResolution() {
    var imgs = document.querySelectorAll('.attachment-woocommerce_thumbnail.size-woocommerce_thumbnail')
    Object.entries(imgs).map(img => {
        // console.log(img[1])
        img[1].sizes = "(max-width: 300px)"
        img[1].srcset = img[1].src.split('-').slice(0, -1).join('-') + '-768x1024' + '.' + img[1].src.split('.').pop()
    })
}

// add link to prices that links to the pdp page
function addLinkToPrices() {
    var prices = document.querySelectorAll('.woocommerce-Price-amount.amount')
}


changeImgsResolution()