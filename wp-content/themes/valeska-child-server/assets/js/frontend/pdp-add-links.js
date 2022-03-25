function addLinkToImgs(imgs) {
    imgs.forEach(img => {
        img.onclick = function() {
            window.open(img.src.replace('-1535x2048', ''))
        }
    });
}

var imgs = document.querySelectorAll('.wp-post-image')
addLinkToImgs(imgs)