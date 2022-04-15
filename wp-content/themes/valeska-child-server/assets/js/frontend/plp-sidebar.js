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