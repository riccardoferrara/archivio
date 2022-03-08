//add href to nice filters
function addHrefToNiceFilters() {
    Object.entries(p_filters).map(f => {
        let category = f[1].getAttribute('data-filter')
        if (category == '*') {
            f[1].href = window.location.href.split('plp')[0] + 'plp'
        } else {
            f[1].href = window.location.href.split('plp')[0] + 'plp/' + '?' + 'wlfilter=1' + '&' + 'product_cat=' + category
        }
    })
}
//get category from url
const getGategoryFromUrl = () => {
    let category = window.location.href.split('product_cat=')[1]
    return category
}

//-----------------------

//deselect ALL filter
document.querySelector('a[data-filter="*"]').classList.remove('qodef--active');

//unshow both all-products and query-products
document.querySelector('.all-products').classList.add('not-displayed')
document.querySelector('.query-products').classList.add('not-displayed')

//check if we are in case all or case category
let category = getGategoryFromUrl()

if (category) {
    document.querySelector('.query-products').classList.remove('not-displayed')
    document.querySelector('.all-products').classList.add('not-displayed')
} else {
    document.querySelector('.all-products').classList.remove('not-displayed')
    document.querySelector('.query-products').classList.add('not-displayed')

}

// find the filter element
var p_filters = document.querySelectorAll('.qodef-m-filter-item');

//add href to all fliters 
addHrefToNiceFilters()

if (category) {
    //deselect ALL
    document.querySelector('a[data-filter="*"]').classList.remove('qodef--active');
    //select REQUESTED
    document.querySelector(`a[data-filter="${getGategoryFromUrl()}"]`).classList.add('qodef--active')
} else {
    //select ALL
    document.querySelector('a[data-filter="*"]').classList.add('qodef--active');
}