window.addEventListener('load', (event) => {
    setStillImgAsFirst()
});


//among the carousel imgs it gives the index of the still img
function getIndexOfStillImg() {
    var imgs = document.querySelectorAll('.swiper-slide.img-mobile .swiper-slide-image')
    for (let i = 0; i < imgs.length; i++) {
        if (imgs[i].src.split('/').slice(-1)[0].split('-')[2] == '0') {
            return i
        }
    }
    return 0; // Return 0 if not found
}

//among the carousel imgs it gives the index of the active img
function getIndexOfActiveImg() {
    var imgs = document.querySelectorAll('.swiper-slide.img-mobile')
    for (let i = 0; i < imgs.length; i++) {
        if (imgs[i].classList.contains('swiper-slide-active')) {
            return i
        }
    }
    return 0; // Return 0 if not found
}

//reset the still img as the first of the carousel
function setStillImgAsFirst() {
    let i_still = getIndexOfStillImg()
    let i_active = getIndexOfActiveImg()
    console.log('i_still: ', i_still)
    console.log('i_active: ', i_active)
    var imgs_front = document.querySelectorAll('.swiper-slide.img-mobile .swiper-slide-image')
    var imgs_copy = [...imgs_front]
    
    // Get actual length instead of hardcoding 6
    var numImages = imgs_copy.length
    if (numImages === 0) return; // Exit if no images found
    
    for (let i = 0; i < numImages; i++) {
        var srcIndex = (i + i_still) % numImages
        var dstIndex = (i + i_active) % numImages
        console.log('i_src: ', srcIndex)
        if (imgs_copy[srcIndex] && imgs_front[dstIndex]) {
            console.log('src: ', imgs_copy[srcIndex].src)
            console.log('dst: ', imgs_front[dstIndex].src)
            console.log('i dest:, ', dstIndex)
            imgs_front[(i + 1) % numImages].src = imgs_copy[srcIndex].src
        }
        console.log('\n')
    }
}