<?php
/**
 * Template Tags - Helper functions
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display breadcrumbs via template part.
 */
function flavor_breadcrumbs() {
    get_template_part( 'template-parts/global/breadcrumbs' );
}

/**
 * Display the posted-on date.
 */
function flavor_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated hidden" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( DATE_W3C ) ),
        esc_html( get_the_modified_date() )
    );

    printf(
        '<span class="posted-on text-sm text-gray-500">%s</span>',
        '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );
}

/**
 * Display the post author.
 */
function flavor_posted_by() {
    printf(
        '<span class="byline text-sm text-gray-500">%s <a class="text-primary hover:underline" href="%s">%s</a></span>',
        esc_html__( 'by', 'flavor' ),
        esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        esc_html( get_the_author() )
    );
}

/**
 * Get WooCommerce cart item count.
 *
 * @return int
 */
function flavor_cart_count() {
    if ( function_exists( 'WC' ) && WC()->cart ) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}

/**
 * Get user wishlist count from user meta.
 *
 * @param int|null $user_id User ID. Defaults to current user.
 * @return int
 */
function flavor_wishlist_count( $user_id = null ) {
    if ( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    if ( ! $user_id ) {
        return 0;
    }

    $wishlist = get_user_meta( $user_id, 'flavor_wishlist', true );

    if ( is_array( $wishlist ) ) {
        return count( $wishlist );
    }

    return 0;
}
