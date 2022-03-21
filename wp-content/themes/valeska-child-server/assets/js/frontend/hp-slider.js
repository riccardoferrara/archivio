function relocateElementBySelector(elementSelector, destSelector) {
    let element = document.querySelector(elementSelector);
    let elementParent = element.parentElement;
    let destElement = document.querySelector(destSelector);
    elementParent.removeChild(element);
    destElement.appendChild(element);
}


window.addEventListener('load', (event) => {
    relocateElementBySelector(('.swiper-button-prev'), ('.swiper-button-prev-moved > .elementor-widget-wrap'))
    relocateElementBySelector(('.swiper-button-next'), ('.swiper-button-next-moved > .elementor-widget-wrap'))
});