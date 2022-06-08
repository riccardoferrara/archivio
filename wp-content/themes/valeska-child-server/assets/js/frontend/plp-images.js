// change imgs resolution to load page faster
function changeImgsResolution() {
    var imgs = document.querySelectorAll('.attachment-woocommerce_thumbnail.size-woocommerce_thumbnail')
    Object.entries(imgs).map(img => {
        // console.log(img[1])
        img[1].sizes = "(max-width: 300px)"
        img[1].srcset = img[1].src.split('-').slice(0, -1).join('-') + '-768x1024' + '.' + img[1].src.split('.').pop()
    })
}

// add link to prices that links to the pdp page
function addLinkToPrices() {
    var prices = document.querySelectorAll('.woocommerce-Price-amount.amount')
}

//function to insert after in html
function insertAfter(newNode, existingNode) {
    existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
}

//function to replace element in DOM
function replaceElement(oldElement, newElement) {
    let elementParent = oldElement.parentElement;
    elementParent.removeChild(oldElement);
    elementParent.appendChild(newElement);
}


//add mouse over picture
function addMouseOverImg() {
    //for each img we are going to build a new element like this:
    // 
    // <div class="container">
    //     <img src="standard_img.png" alt="Avatar" class="image">
    //     <div class="overlay">
    //         <img src="overlay_img.png" alt="Avatar" class="image">
    //     </div>
    // </div>
    //
    // find all images
    var imgs = document.querySelectorAll('.attachment-woocommerce_thumbnail.size-woocommerce_thumbnail')

    imgs.forEach(img => {
        //use the template of the img to create a new img and change the src with the mouse over src
        let newMouseOverImg = img.cloneNode(true)
        newMouseOverImg.srcset = newMouseOverImg.srcset.replace('-0-', '-MouseOver-')
        newMouseOverImg.classList.add('hover')

        // because many src are not present onerror do nothing
        newMouseOverImg.setAttribute("onerror", `this.onerror=null;this.srcset='${img.srcset}';`)

        //inser the new hover img after the normal img
        insertAfter(newMouseOverImg, img)

    })
}


changeImgsResolution()
addMouseOverImg()