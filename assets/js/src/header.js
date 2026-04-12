/**
 * Header interactivity
 *
 * @package Flavor
 */

/**
 * Refresh mini-cart contents via AJAX.
 */
function refreshMiniCart() {
    if (typeof window.flavorRefreshMiniCart === 'function') {
        window.flavorRefreshMiniCart();
    }
}

// WooCommerce added_to_cart event.
document.addEventListener('added_to_cart', refreshMiniCart);

// Also listen for jQuery WC events if jQuery is present.
if (typeof jQuery !== 'undefined') {
    jQuery(document.body).on('added_to_cart removed_from_cart', refreshMiniCart);
}

/**
 * Header scroll class toggle.
 */
(function () {
    let ticking = false;

    document.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                if (window.scrollY > 50) {
                    document.body.classList.add('header-scrolled');
                } else {
                    document.body.classList.remove('header-scrolled');
                }
                ticking = false;
            });
            ticking = true;
        }
    });
})();
