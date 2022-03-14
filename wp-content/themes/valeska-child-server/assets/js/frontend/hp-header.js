//----------------------HEADER------------------------------------
//              HEADER MENU BEHAVIOUR
//----------------------------------------------------------------
// colora i bottoni del primo menu di nero quando il sub menu è attivo
jQuery(function() {
    jQuery('.sub-menu').hover(function() {
        jQuery('ul.menu > li > a > span.qodef-menu-item-text').css('color', 'black');
    }, function() {
        // on mouseout, reset the background colour
        jQuery('ul.menu > li > a > span.qodef-menu-item-text').css('color', '');
    });
});

jQuery(function() {
    jQuery('ul.menu > li > a > span.qodef-menu-item-text').hover(function() {
        jQuery('ul.menu > li > a > span.qodef-menu-item-text').css('color', 'black');
    }, function() {
        // on mouseout, reset the background colour
        jQuery('ul.menu > li > a > span.qodef-menu-item-text').css('color', '');
    });
});

jQuery(function() {
    jQuery('ul.sub-menu li a').hover(function() {
        jQuery('ul.menu > li > a > span.qodef-menu-item-text').css('color', 'black');
    }, function() {
        // on mouseout, reset the background colour
        jQuery('ul.menu > li > a > span.qodef-menu-item-text').css('color', '');
    });
});