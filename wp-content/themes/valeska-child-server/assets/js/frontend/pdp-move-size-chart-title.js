console.log('move size chart title')

function relocateElementBySelector(elementSelector, destSelector) {
    let element = document.querySelector(elementSelector);
    let elementParent = element.parentElement;
    let destElement = document.querySelector(destSelector);
    elementParent.removeChild(element);
    destElement.appendChild(element);
}


function relocateSizingChartNearSizeLabel() {
    if (document.querySelector('[for="size"]')) {
        relocateElementBySelector(('.sizing-chart-title'), ('[for="size"]'))
    } else if (document.querySelector('[for="pa_size"]')) {
        relocateElementBySelector(('.sizing-chart-title'), ('[for="pa_size"]'))
    } else {
        // relocateElementBySelector(('.sizing-chart-title'), ('[for="pa_color"]'))
    }
}

relocateSizingChartNearSizeLabel()