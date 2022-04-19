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
    // jQuery("main").toggleClass("move-to-left-partly");
    // jQuery(".arrow").toggleClass("active");
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
        openSidebar();
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
        // jQuery("main").removeClass("move-to-left-partly");
        jQuery("#sidebar-tab").removeClass("move-to-left");
        // jQuery(".arrow").removeClass("active");
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
    // console.log('color: ', colors[i].getAttribute('data-title'))
    // console.log('actual txt: ', el[1].text)
    el[1].text = colors[i].getAttribute('data-title')
    // console.log('new text: ', el[1].text)
    // console.log('\n')
})


//--------------------------------------
//         UNDERLINE BEHAVIOR
//--------------------------------------

var categories = document.querySelectorAll('[data-taxonomy="product_cat"]')

// underline the element when is clicked -> classes: active-filter underline
// (but before clear all the underlined filters)
//---------------------------------------------------------------------------
Object.entries(labels).map(el => {
    el[1].setAttribute("onclick", "return filterClick(this, 'colorLabel')")
})

Object.entries(categories).map(el => {
    el[1].setAttribute("onclick", "return filterClick(this, 'category')")
})

Object.entries(colors).map(el => {
    el[1].setAttribute("onclick", "return filterClick(this, 'colorCercle')")
})

function filterClick(el, filterType) {
    if (filterType=="colorLabel") {
        clearColorlabels()
        clearColorsCercleSelection()
        selectColor(el.parentElement)
    }
    if (filterType=="colorCercle") {
        clearColorlabels()
        clearColorsCercleSelection()
        selectColor(el)
    }
    if (filterType=="category") {
        clearCategories()
    } 
    activateFilter(el)
    underlineElement(el)
}

function activateFilter(el) {
    el.classList.add('active-filter')
}

function unactivateFilter(el) {
    el.classList.remove('active-filter')
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
    clearFilters(categories)
}

function clearColorlabels (){
    clearFilters(labels)
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
    })
}

// clear selected elements

// when there are no active filters "CLEAR ALL" button is grey

// when there is one active filter "CLEAR ALL" button is  underlined

// add the right class to the circle of the color when is active

//--------------------------------------
//         VIEW RESULTS BEHAVIOR
//--------------------------------------

// when the button is clicked look for active filters and go the href based on the request otherwise just close the sidebar