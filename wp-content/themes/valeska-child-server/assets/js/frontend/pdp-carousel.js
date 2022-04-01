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
}

//among the carousel imgs it gives the index of the active img
function getIndexOfActiveImg() {
    var imgs = document.querySelectorAll('.swiper-slide.img-mobile')
    for (let i = 0; i < imgs.length; i++) {
        if (imgs[i].classList.contains('swiper-slide-active')) {
            return i
        }
    }
}

//reset the still img as the first of the carousel
function setStillImgAsFirst() {
    let i_still = getIndexOfStillImg()
    let i_active = getIndexOfActiveImg()
    console.log('i_still: ', i_still)
    console.log('i_active: ', i_active)
    var imgs_front = document.querySelectorAll('.swiper-slide.img-mobile .swiper-slide-image')
    var imgs_copy = [...imgs_front]
    for (let i = 0; i <= 5; i++) {
        console.log('i_src: ', (i + i_still) % 6)
        console.log('src: ', imgs_copy[(i + i_still) % 6].src)
        console.log('dst: ', imgs_front[(i + i_active) % 6].src)
        console.log('i dest:, ', (i + i_active) % 6)
        imgs_front[(i + 1) % 6].src = imgs_copy[(i + i_still) % 6].src
        console.log('\n')
    }
}