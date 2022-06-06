/* Unsure how to center this without JS :/*/
jQuery(function() {
    jQuery("#sidebar-tab-text").width(jQuery("#sidebar").height());
});
jQuery(window).resize(function() {
    jQuery("#sidebar-tab-text").width(jQuery("#sidebar").height());
});
/* End of unsure centering */

//The only necessary piece of code lol
function toggleSidebar() {
    jQuery("#sidebar").toggleClass("move-to-left");
    jQuery("#sidebar-tab").toggleClass("move-to-left");
    jQuery(".grey-div").toggleClass("not-displayed");
}

/* Totally unncessary swyping gestures*/
var gestureZone = document;
var touchstartX = 0,
    touchstartY = 0;
gestureZone.addEventListener('touchstart', function(event) {
    touchstartX = event.changedTouches[0].screenX;
    touchstartY = event.changedTouches[0].screenY;
}, false);

gestureZone.addEventListener('touchend', function(event) {
    var touchendX = event.changedTouches[0].screenX;
    var touchendY = event.changedTouches[0].screenY;
    handleGesure(touchendX, touchendY);
}, false);

function handleGesure(touchendX, touchendY) {
    var acceptableYTravel = (touchendY - touchstartY) < 15 && (touchendY - touchstartY) > -15;

    var swiped = 'swiped: ';
    if (touchendX < touchstartX && acceptableYTravel) {
        // openSidebar();
        console.log(swiped + 'left!');
    }
    if (touchendX > touchstartX && acceptableYTravel) {
        closeSidebar();
        console.log(swiped + 'right!');
    }
}

// function openSidebar() {
    //     jQuery("#sidebar").addClass("move-to-left");
    //     jQuery("main").addClass("move-to-left-partly");
    //     jQuery("#sidebar-tab").addClass("move-to-left");
    //     jQuery(".arrow").addClass("active");
    //     obscure();
    // }
    
    function closeSidebar() {
        jQuery("#sidebar").removeClass("move-to-left");
        jQuery("#sidebar-tab").removeClass("move-to-left");
        unobscure();
    }
    
/* End of totally unncessary swyping gesture s*/

// function to obscure the window when sidebar is opened
function obscure() {
    let grey_div = document.querySelector('.grey-div')
    grey_div.classList.remove('not-displayed')
    grey_div.classList.add('displayed')
}
// function to unobscure the window when sidebar is opened
function unobscure() {
    let grey_div = document.querySelector('.grey-div')
    grey_div.classList.add('not-displayed')
    grey_div.classList.remove('displayed')
}


//--------------------------------------
//         ADD COLOR LABELS
//--------------------------------------

//find colors
var colors = document.querySelectorAll('#sidebar-color-filters li')
var labels = document.querySelectorAll('.color-label')

Object.entries(labels).map((el, i) => {
    el[1].text = colors[i].getAttribute('data-title')
})


//--------------------------------------
//         UNDERLINE BEHAVIOR
//--------------------------------------

var categoryElements = document.querySelectorAll('.sidebar [data-taxonomy="product_cat"]')

// underline the element when is clicked -> classes: active-filter underline
// (but before clear all the underlined filters)
//---------------------------------------------------------------------------
Object.entries(labels).map(el => {
    el[1].setAttribute("onclick", "return filterClick(this, 'colorLabel')")
})

Object.entries(categoryElements).map(el => {
    el[1].setAttribute("onclick", "return filterClick(this, 'category')")
})

Object.entries(colors).map(el => {
    el[1].setAttribute("onclick", "return filterClick(this, 'colorCercle')")
})

function filterClick(el, filterType) {
    if (filterType=="colorLabel") {
        clearColors()
        clearColorsCercleSelection()
        selectColor(el.parentElement)
    }
    if (filterType=="colorCercle") {
        clearColors()
        clearColorsCercleSelection()
        selectColor(el)
    }
    if (filterType=="category") {
        clearCategories()
    } 
    activateFilter(el)
    underlineElement(el)
    
    updateViewResultsHref()

    return false
}

function activateFilter(el) {
    el.classList.add('active-filter')
}

function unactivateFilter(el) {
    el.classList.remove('active-filter')
    enableA(clearAll[0]) //mobile
    enableA(clearAll[1]) //desktop
    enableA(viewResults[0]) //mobile
    enableA(viewResults[1]) //desktop
}
function underlineElement(el) {
    el.classList.add('underline')
}

function ununderlineElement(el){
    el.classList.remove('underline')
}

function unselectColor(el){
    el.classList.remove('selected')
}

function selectColor(el){
    el.classList.add('selected')
}

// when another element of the same list is clicked clear others (functions)
//---------------------------------------------------------------------------

function clearCategories (){
    clearFilters(categoryElements)
}

function clearColors() {
    //  clear colors labels
    clearFilters(labels)
    // lear cercles
    clearColorsCercleSelection()
}

function clearFilters(elements){
    Object.entries(elements).map(el => {
        unactivateFilter(el[1])
        ununderlineElement(el[1])
    })
}

function clearColorsCercleSelection(){
    Object.entries(colors).map(el => {
        unselectColor(el[1])
        unactivateFilter(el[1])
    })
}


//--------------------------------------
//   VIEW RESULTS & CLEAR ALL BEHAVIOR
//--------------------------------------

var viewResults = document.querySelectorAll('[behaviour="view_results"]')
var clearAll = document.querySelectorAll('[behaviour="clear_all"]')

// when a filter is activted "CLEAR ALL" button from disabled (default) becomes active
function enableA(el){
    el.classList.remove('disabled')
}

function disableA(el){
    el.classList.add('disabled')
}

var categories
categories = []
// get all available categories except "ALL"
function getAllCategories(){
    categoryElements = document.querySelectorAll('.sidebar [data-taxonomy="product_cat"]')
    categoryElements.forEach(el => {
        // console.log(el.getAttribute('data-filter'))
        if (el.getAttribute('data-filter') != '*'){
            categories.push(el.getAttribute('data-filter'))
        }
    })
}
getAllCategories()

// when click on "clear all" => clear all fiters
clearAll[0].setAttribute("onclick", "return clearAllFilters()")
clearAll[1].setAttribute("onclick", "return clearAllFilters()")


function clearAllFilters () {
    clearColors()
    clearCategories()
    disableA(clearAll[0])
    disableA(clearAll[1])
    disableA(viewResults[0])
    disableA(viewResults[1])
}

// when the button is clicked look for active filters and go the href based on the request otherwise just close the sidebar
function updateViewResultsHref(){
    var activeFilters = getActiveFilters()
    var href = getQueryHref (activeFilters['colorFilter'], activeFilters['categoryFilter'])
    viewResults[0].setAttribute("href", href)
    viewResults[1].setAttribute("href", href)
}

var colorFilter
var categoryFilter 

// look for active filters
function getActiveFilters() {
    colorFilter = document.querySelector('.selected.color-variable-item')
    if (colorFilter) {colorFilter = colorFilter.getAttribute('data-value')}
    categoryFilter  = document.querySelector('.active-filter, [data-taxonomy="product-cat"]')
    if (categoryFilter) {
        categoryFilter = categoryFilter.getAttribute('data-filter')
        categoryFilter = categoryFilter == "*" ? categories.join(','):categoryFilter
    }
    return {colorFilter, categoryFilter}
}

//create the href
function getQueryHref (colorFilter, categoryFilter) {
    // href = "window.location.href"
    href = "https://www.archiviowebsite.com/women-collection"
    console.log('colorFilter: ', colorFilter)
    console.log('categoryFilter: ', categoryFilter)

    //both filter are active
    if (categoryFilter && colorFilter) {
        href += `/?wlfilter=1&filter_color=${colorFilter}&product_cat=${categoryFilter}`
    }

    //only category filter is active
    if (categoryFilter && !colorFilter) {
        href += `/?wlfilter=1&product_cat=${categoryFilter}`
    }

    //only color filter is active
    if (!categoryFilter && colorFilter) {
        href += `/?wlfilter=1&filter_color=${colorFilter}`
    }

    return href
}

