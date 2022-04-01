//----------------------------------------------------
// script per dare condizioni al pulsante "ADD TO CART"
// tale pulsante, nei casi in cui esiste la taglia,  
// deve indicare "SELECT A SIZE" ed eventualmente non
// essere cliccabile. --------------------------------
//funzione che checka se almeno uno dei radiobuttons is checked
let radiogroupAriaLabelIsSelected = (size_radiogroup) => {
    checked = Object.values(size_radiogroup.childNodes).find(li => li.ariaChecked == "true") ? true : false
    return checked
}

// update da fare al bottone quand una taglia sarà selezionata dall'user
function sizeIsSelected() {
    cartButton.style.fontSize = "0px"
    console.log("size selected")
    cartButton.classList.add("single_add_to_cart_button")
    cartButton.classList.add("size_selected")
    cartButton.classList.remove("no_size_selected")
    jQuery('#add_to_cart').prop("disabled", false)
}


//funzione che fa l'update del cart button
function updateCartButton() {
    // se esiste allora fai l'update
    if (size_radiogroup) {
        sizeIsSelected()
    }
}

// cerca il radiogroup 
let size_radiogroup = document.querySelectorAll('[role="radiogroup"][aria-label="Size"]')[0]

//cerca il bottone "add to cart"
let cartButton = document.querySelector('.single_add_to_cart_button.button.alt')
cartButton.id = "add_to_cart"

// se c è almeno un size e nessuna taglia è selezionata aggiungo msg
if (size_radiogroup && !radiogroupAriaLabelIsSelected(size_radiogroup)) {
    cartButton.classList.remove("single_add_to_cart_button")
    cartButton.classList.add("no_size_selected")
    jQuery('#add_to_cart').prop("disabled", true)
    cartButton.style.fontSize = "0px"
} else {
    cartButton.style.fontSize = "16px"
}

// se una taglia è selezionata aggiungi la classe corrispondente
if (size_radiogroup && radiogroupAriaLabelIsSelected(size_radiogroup)) {
    sizeIsSelected()
}

//aggiuni la funzione di update al click
size_radiogroup.addEventListener("click", updateCartButton)