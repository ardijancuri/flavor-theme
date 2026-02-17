<?php
/**
 * Top Bar - Announcement/Promo Bar
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;

$topbar_text = get_theme_mod( 'flavor_topbar_text', esc_html__( 'Free shipping on orders over €50!', 'flavor' ) );
?>

<div
    x-data="{ show: !localStorage.getItem('topbar_dismissed') }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-full"
    class="bg-[#2C2C2C] text-white text-xs relative z-40"
>
    <div class="container-site flex items-center justify-start tablet-sm:justify-center py-2 px-4">
        <!-- Megaphone Icon -->
        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
        </svg>

        <span><?php echo esc_html( $topbar_text ); ?></span>

        <!-- Dismiss Button -->
        <button
            @click="show = false; localStorage.setItem('topbar_dismissed', '1')"
            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:opacity-70 transition-opacity"
            aria-label="<?php esc_attr_e( 'Dismiss announcement', 'flavor' ); ?>"
        >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
