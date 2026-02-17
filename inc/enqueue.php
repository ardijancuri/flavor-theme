<?php
/**
 * Enqueue theme assets
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue all theme styles and scripts.
 */
function flavor_enqueue_assets() {
    // Inter font.
    wp_enqueue_style( 'flavor-inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null );

    // Main theme CSS (Tailwind compiled).
    wp_enqueue_style( 'flavor-style', FLAVOR_URI . '/assets/css/dist/style.css', array(), FLAVOR_VERSION );

    // Swiper CSS.
    wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), null );

    // GLightbox CSS.
    wp_enqueue_style( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', array(), null );

    // Alpine.js component definitions (must load BEFORE Alpine).
    wp_enqueue_script( 'flavor-alpine-components', FLAVOR_URI . '/assets/js/src/alpine-components.js', array(), FLAVOR_VERSION, true );

    // Alpine.js (defer).
    wp_enqueue_script( 'alpinejs-collapse', 'https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3/dist/cdn.min.js', array( 'flavor-alpine-components' ), null, true );
    wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js', array( 'alpinejs-collapse' ), null, true );

    // Swiper JS.
    wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true );

    // GLightbox JS.
    wp_enqueue_script( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', array(), null, true );

    // Theme JS files.
    wp_enqueue_script( 'flavor-header', FLAVOR_URI . '/assets/js/src/header.js', array(), FLAVOR_VERSION, true );
    wp_enqueue_script( 'flavor-app', FLAVOR_URI . '/assets/js/src/app.js', array( 'alpinejs' ), FLAVOR_VERSION, true );

    // Conditional scripts.
    if ( is_front_page() ) {
        wp_enqueue_script( 'flavor-slider', FLAVOR_URI . '/assets/js/src/slider.js', array( 'swiper' ), FLAVOR_VERSION, true );
        wp_enqueue_script( 'flavor-products', FLAVOR_URI . '/assets/js/src/products.js', array( 'flavor-app' ), FLAVOR_VERSION, true );
    }

    if ( function_exists( 'is_product' ) && is_product() ) {
        wp_enqueue_script( 'flavor-product-page', FLAVOR_URI . '/assets/js/src/product-page.js', array( 'swiper', 'glightbox' ), FLAVOR_VERSION, true );
    }

    if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_search() ) ) {
        wp_enqueue_script( 'flavor-filters', FLAVOR_URI . '/assets/js/src/filters.js', array(), FLAVOR_VERSION, true );
    }

    if ( function_exists( 'is_cart' ) && is_cart() ) {
        wp_enqueue_script( 'flavor-cart', FLAVOR_URI . '/assets/js/src/cart.js', array(), FLAVOR_VERSION, true );
    }

    if ( function_exists( 'is_checkout' ) && is_checkout() ) {
        wp_enqueue_script( 'flavor-checkout', FLAVOR_URI . '/assets/js/src/checkout.js', array(), FLAVOR_VERSION, true );
    }

    // Localize.
    $localize_data = array(
        'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
        'nonce'       => wp_create_nonce( 'flavor_ajax_nonce' ),
        'homeUrl'     => home_url( '/' ),
    );

    if ( function_exists( 'wc_get_cart_url' ) ) {
        $localize_data['cartUrl']     = wc_get_cart_url();
        $localize_data['checkoutUrl'] = wc_get_checkout_url();
    }

    wp_localize_script( 'flavor-app', 'flavorData', $localize_data );
}
add_action( 'wp_enqueue_scripts', 'flavor_enqueue_assets' );

/**
 * Add defer attribute to Alpine.js scripts.
 *
 * @param string $tag    Script HTML tag.
 * @param string $handle Script handle.
 * @return string
 */
function flavor_defer_alpine( $tag, $handle ) {
    if ( in_array( $handle, array( 'alpinejs', 'alpinejs-collapse' ), true ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'flavor_defer_alpine', 10, 2 );

/**
 * Preload Inter font.
 */
function flavor_preload_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'flavor_preload_fonts', 1 );
