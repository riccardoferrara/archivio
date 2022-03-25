function addLinkToImgs(imgs) {
    imgs.forEach(img => {
        img.onclick = function() {
            location.href = img.src
        }
    });
}

var imgs = document.querySelectorAll('.wp-post-image')
addLinkToImgs(imgs)