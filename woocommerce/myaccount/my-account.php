<?php
/**
 * My Account page - Flavor Theme
 *
 * @package suspended WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$menu_items = wc_get_account_menu_items();
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<div class="container-site py-8 lg:py-12" x-data="{ mobileNav: false }">
    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Sidebar Navigation -->
        <aside class="lg:w-1/4 shrink-0">
            <!-- Mobile toggle -->
            <button @click="mobileNav = !mobileNav"
                    class="flex items-center justify-between w-full lg:hidden bg-white rounded-lg p-4 shadow-sm border border-gray-200 mb-4">
                <span class="font-semibold text-gray-800"><?php esc_html_e( 'Account Menu', 'flavor' ); ?></span>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': mobileNav }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>

            <nav class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                 :class="{ 'hidden lg:block': !mobileNav }" x-cloak>
                <!-- User greeting -->
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <?php echo get_avatar( $current_user->ID, 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo esc_html( $current_user->display_name ); ?></p>
                            <p class="text-sm text-gray-500"><?php echo esc_html( $current_user->user_email ); ?></p>
                        </div>
                    </div>
                </div>

                <ul class="py-2">
                    <?php foreach ( $menu_items as $endpoint => $label ) :
                        $url = wc_get_account_endpoint_url( $endpoint );
                        $is_active = $endpoint === wc_get_current_account_endpoint() || ( 'dashboard' === $endpoint && ! wc_get_current_account_endpoint() );
                        $icon = '';
                        switch ( $endpoint ) {
                            case 'dashboard':
                                $icon = '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>';
                                break;
                            case 'orders':
                                $icon = '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>';
                                break;
                            case 'edit-address':
                                $icon = '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>';
                                break;
                            case 'edit-account':
                                $icon = '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>';
                                break;
                            case 'customer-logout':
                                $icon = '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>';
                                break;
                            default:
                                $icon = '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>';
                                break;
                        }
                    ?>
                        <li>
                            <a href="<?php echo esc_url( $url ); ?>"
                               class="flex items-center gap-3 px-5 py-3 text-sm transition-colors <?php echo $is_active ? 'bg-orange-50 text-[var(--color-primary,#E15726)] font-semibold border-l-3 border-[var(--color-primary,#E15726)]' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?>">
                                <?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                <?php echo esc_html( $label ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="lg:w-3/4 min-w-0">
            <?php do_action( 'woocommerce_account_content' ); ?>
        </main>

    </div>
</div>

<?php do_action( 'woocommerce_after_main_content' ); ?>
