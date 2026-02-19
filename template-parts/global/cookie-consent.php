<?php
/**
 * Cookie consent bar
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;
?>

<div
    x-data="{ show: !localStorage.getItem('cookie_accepted') }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    x-cloak
    class="fixed bottom-16 lg:bottom-0 left-0 right-0 z-50 bg-[#333333] text-white px-4 py-3"
>
    <div class="container-site flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-sm text-gray-300">
            <?php esc_html_e( 'We use cookies to enhance your experience. By continuing to browse, you agree to our use of cookies.', 'flavor' ); ?>
        </p>
        <button
            @click="localStorage.setItem('cookie_accepted', '1'); show = false"
            class="shrink-0 px-4 py-1.5 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 transition-colors"
        >
            <?php esc_html_e( 'Close', 'flavor' ); ?>
        </button>
    </div>
</div>
