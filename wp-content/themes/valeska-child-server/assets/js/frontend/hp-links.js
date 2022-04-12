function addLinkToMainImg() {
    var main_section = document.querySelector('.main-section')

    // add the women-collection link to the section
    main_section.addEventListener('click', function() {
        location.href = 'https://www.archiviowebsite.com/women-collection/'
    }, false);
    // change the cursor to pointer
    main_section.style.cursor = 'pointer'
}

addLinkToMainImg()