/* --------------------------------------------------------------------------
 * Product listing enhancements (WooCommerce)
 * --------------------------------------------------------------------------
 * Goals
 *   1. Serve 768 × 1024 thumbnails (change **both** `src` and `srcset`).
 *   2. Wrap prices with the product link so they’re clickable.
 *   3. On hover, swap to the “-MouseOver-” variant (only if it exists).
 *   4. Process images lazily with IntersectionObserver.
 *   5. **Console insight** – log how many thumbnails are detected and how many
 *      get enhanced during scrolling, so you can monitor behaviour in DevTools.
 *
 * Author : Riscaldo  ·  Updated : 24‑May‑2025                                    
 * --------------------------------------------------------------------------*/

(() => {
  "use strict";

  /* ------------------------------------------------------------------ */
  /*  Config & helpers                                                  */
  /* ------------------------------------------------------------------ */

  const THUMBNAIL_SELECTOR = ".attachment-woocommerce_thumbnail.size-woocommerce_thumbnail";
  const PRICE_SELECTOR     = ".woocommerce-Price-amount.amount";
  const HIGHRES_SUFFIX     = "-768x1024"; // WordPress hi‑res size to swap in

  /** Replace the WP size suffix (e.g. -150x150) with the desired one. */
  const swapSizeSuffix = (url, newSuffix = HIGHRES_SUFFIX) =>
    url.replace(/-\d+x\d+(?=\.[a-z]+$)/i, newSuffix);

  /** Build the MouseOver filename: replace “-0-” with “-MouseOver-”. */
  const buildHoverUrl = url => url.replace("-0-", "-MouseOver-");

  /** Lightweight HEAD probe (falls back to <img> if HEAD blocked). */
  const imageExists = async url => {
    try {
      const res = await fetch(url, { method: "HEAD" });
      return res.ok;
    } catch {
      return new Promise(resolve => {
        const probe = new Image();
        probe.onload  = () => resolve(true);
        probe.onerror = () => resolve(false);
        probe.src = url;
      });
    }
  };

  /* ------------------------------------------------------------------ */
  /* 1. Upgrade a single thumbnail (src & srcset)                       */
  /* ------------------------------------------------------------------ */

  function upgradeThumbnail (img) {
    const original = img.getAttribute("src");
    if (!original) return;
    const highRes  = swapSizeSuffix(original);

    img.src    = highRes;
    img.srcset = `${highRes} 1x`;
    img.sizes  = "(max-width: 300px) 100vw, 300px";
  }

  /* ------------------------------------------------------------------ */
  /* 2. Attach hover overlay for a single thumbnail                     */
  /* ------------------------------------------------------------------ */

  async function attachHover (img) {
    const hoverUrl = buildHoverUrl(img.getAttribute("src"));
    if (hoverUrl === img.src) return;           // no “-0-” segment found
    if (!(await imageExists(hoverUrl))) return; // counterpart missing

    const wrapper = document.createElement("div");
    wrapper.className = "img-hover-wrapper";
    img.parentNode.insertBefore(wrapper, img);
    wrapper.appendChild(img);

    const hoverImg = img.cloneNode();
    hoverImg.src = hoverUrl;
    hoverImg.srcset = `${hoverUrl} 1x`;
    hoverImg.classList.add("hover");
    wrapper.appendChild(hoverImg);
  }

  /* ------------------------------------------------------------------ */
  /* 3. Observe thumbnails lazily                                       */
  /* ------------------------------------------------------------------ */

  function enhanceThumbnailsLazily () {
    const thumbnails = document.querySelectorAll(THUMBNAIL_SELECTOR);
    console.log(`[ProductEnhancements] Found ${thumbnails.length} thumbnails to observe`);

    let processed = 0;

    const io = new IntersectionObserver(entries => {
      entries.forEach(async entry => {
        if (!entry.isIntersecting) return;
        io.unobserve(entry.target); // enhance once, then stop observing

        upgradeThumbnail(entry.target);
        await attachHover(entry.target);

        processed += 1;
        console.log(`[ProductEnhancements] Enhanced ${processed}/${thumbnails.length}`);
      });
    }, { rootMargin: "200px" });

    thumbnails.forEach(img => io.observe(img));
  }

  /* ------------------------------------------------------------------ */
  /* 4. Make prices clickable                                           */
  /* ------------------------------------------------------------------ */

  function addLinkToPrices () {
    document.querySelectorAll(PRICE_SELECTOR).forEach(priceEl => {
      const productAnchor = priceEl.closest(".product")?.querySelector("a.woocommerce-LoopProduct-link");
      if (!productAnchor) return;
      if (priceEl.parentElement?.tagName === "A") return; // already wrapped

      const link = productAnchor.cloneNode(false); // shallow clone keeps href
      link.className = "price-link";
      priceEl.parentNode.insertBefore(link, priceEl);
      link.appendChild(priceEl);
    });
  }

  /* ------------------------------------------------------------------ */
  /* 5. Bootstrap on DOM ready                                          */
  /* ------------------------------------------------------------------ */

  document.addEventListener("DOMContentLoaded", () => {
    addLinkToPrices();
    enhanceThumbnailsLazily();
  });
})();

/* ----------------------------------------------------------------------
 * Minimal CSS (enqueue separately or add to theme):
 * ----------------------------------------------------------------------
 * .img-hover-wrapper { position: relative; display: inline-block; }
 * .img-hover-wrapper img.hover {
 *   position: absolute; inset: 0; width: 100%; height: 100%;
 *   opacity: 0; transition: opacity .25s ease-in-out;
 * }
 * .img-hover-wrapper:hover img.hover { opacity: 1; }
 * -------------------------------------------------------------------- */