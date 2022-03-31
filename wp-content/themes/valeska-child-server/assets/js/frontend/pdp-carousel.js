// the still image will be the first
function setFirstImgOfCarousel() {
    document.querySelector('.elementor-image-carousel.swiper-wrapper').style.transform = "translate3d(-650px, 0px, 0px)"
}

window.addEventListener('load', (event) => {
    setFirstImgOfCarousel()
});