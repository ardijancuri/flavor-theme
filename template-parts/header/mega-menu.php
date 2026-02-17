<?php
/**
 * Mega Menu - Category Sidebar + Flyout
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;

$product_categories = get_terms(
    array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'exclude'    => get_option( 'default_product_cat' ),
    )
);

if ( is_wp_error( $product_categories ) ) {
    $product_categories = array();
}
?>

<div
    x-data="{ open: false, activeCategory: null }"
    @toggle-mega-menu.window="open = !open"
    @keydown.escape.window="open = false"
    x-show="open"
    @click.outside="open = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="hidden tablet-sm:block absolute left-0 right-0 z-40 bg-white shadow-xl rounded-b-lg"
>
    <div class="container-site">
        <div class="flex min-h-[338px]">
            <!-- Sidebar: Category List -->
            <div class="w-64 border-r border-gray-100 py-4">
                <ul class="space-y-0.5">
                    <?php foreach ( $product_categories as $index => $cat ) : ?>
                        <li
                            @mouseenter="activeCategory = <?php echo (int) $cat->term_id; ?>"
                            class="relative"
                        >
                            <a
                                href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors rounded-md"
                                :class="{ 'bg-gray-50 text-primary': activeCategory === <?php echo (int) $cat->term_id; ?> }"
                            >
                                <!-- Icon placeholder -->
                                <span class="w-6 h-6 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </span>
                                <span class="flex-1"><?php echo esc_html( $cat->name ); ?></span>
                                <!-- Chevron -->
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Flyout: Subcategories -->
            <div class="flex-1 py-4 px-6">
                <?php foreach ( $product_categories as $cat ) :
                    $children = get_terms(
                        array(
                            'taxonomy'   => 'product_cat',
                            'hide_empty' => false,
                            'parent'     => $cat->term_id,
                        )
                    );
                    if ( is_wp_error( $children ) ) {
                        $children = array();
                    }
                    ?>
                    <div
                        x-show="activeCategory === <?php echo (int) $cat->term_id; ?>"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        class="grid grid-cols-4 gap-6"
                    >
                        <?php if ( ! empty( $children ) ) : ?>
                            <?php
                            $chunks = array_chunk( $children, (int) ceil( count( $children ) / 3 ) );
                            foreach ( $chunks as $chunk ) :
                                ?>
                                <div>
                                    <?php foreach ( $chunk as $child ) : ?>
                                        <div class="mb-4">
                                            <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="block font-semibold text-sm text-gray-900 hover:text-primary mb-2">
                                                <?php echo esc_html( $child->name ); ?>
                                            </a>
                                            <?php
                                            $grandchildren = get_terms(
                                                array(
                                                    'taxonomy'   => 'product_cat',
                                                    'hide_empty' => false,
                                                    'parent'     => $child->term_id,
                                                    'number'     => 5,
                                                )
                                            );
                                            if ( ! is_wp_error( $grandchildren ) && ! empty( $grandchildren ) ) :
                                                ?>
                                                <ul class="space-y-1.5">
                                                    <?php foreach ( $grandchildren as $gc ) : ?>
                                                        <li>
                                                            <a href="<?php echo esc_url( get_term_link( $gc ) ); ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">
                                                                <?php echo esc_html( $gc->name ); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                    <li>
                                                        <a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="text-sm text-primary font-medium hover:underline">
                                                            <?php esc_html_e( 'Show more', 'flavor' ); ?>
                                                        </a>
                                                    </li>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Promo Image Area -->
                        <div class="flex items-center justify-center bg-gray-50 rounded-lg overflow-hidden">
                            <?php
                            $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                            if ( $thumbnail_id ) :
                                echo wp_get_attachment_image( $thumbnail_id, 'medium', false, array( 'class' => 'w-full h-full object-cover' ) );
                            else :
                                ?>
                                <div class="text-center p-6">
                                    <p class="text-sm font-semibold text-gray-900 mb-1"><?php echo esc_html( $cat->name ); ?></p>
                                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="text-sm text-primary hover:underline">
                                        <?php esc_html_e( 'Shop now', 'flavor' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Default state -->
                <div x-show="!activeCategory" class="flex items-center justify-center h-full text-gray-400 text-sm">
                    <?php esc_html_e( 'Hover over a category to explore', 'flavor' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
