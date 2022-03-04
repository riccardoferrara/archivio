// find the filter element
var p_filters = document.querySelectorAll('.qodef-m-filter-item');

// update class for underline
function updateUnderline(event) {
    event.preventDefault();
    old_selected_button = document.querySelector('.qodef--active')
    old_selected_button.classList.remove('qodef--active')
    this.classList.add('qodef--active')
}

// add onclick listener to all filters test test test 
Object.entries(p_filters).map(f => { f[1].addEventListener("click", updateUnderline) })