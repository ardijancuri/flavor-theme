<?php
/**
 * AJAX Handlers — Product loading endpoints
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load products by category/context (tabbed products, special offers)
 */
function flavor_load_products_handler() {
	check_ajax_referer( 'flavor_ajax_nonce', 'nonce' );

	$context  = sanitize_text_field( wp_unslash( $_POST['context'] ?? '' ) );
	$category = absint( $_POST['category'] ?? 0 );
	$per_page = absint( $_POST['per_page'] ?? 10 );
	$per_page = min( $per_page, 20 );

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	// Special offers: only on-sale products
	if ( 'special_offers' === $context ) {
		$on_sale_ids = wc_get_product_ids_on_sale();
		if ( empty( $on_sale_ids ) ) {
			wp_send_json_success( array( 'html' => '', 'products' => array() ) );
		}
		$args['post__in'] = $on_sale_ids;
		$args['orderby']  = 'rand';
	}

	// Category filter
	if ( $category > 0 ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	// For special offers, return structured data (used by Alpine.js)
	if ( 'special_offers' === $context ) {
		$products = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$product = wc_get_product( get_the_ID() );
			if ( ! $product ) {
				continue;
			}

			$regular = (float) $product->get_regular_price();
			$sale    = (float) $product->get_sale_price();
			$price   = (float) $product->get_price();
			$discount = ( $regular > 0 && $sale ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;

			$products[] = array(
				'id'            => $product->get_id(),
				'name'          => $product->get_name(),
				'url'           => $product->get_permalink(),
				'image'         => wp_get_attachment_image_url( $product->get_image_id(), 'flavor-product-card' ) ?: wc_placeholder_img_src(),
				'regular_price' => number_format( $regular, 2, ',', '.' ),
				'sale_price'    => number_format( $price, 2, ',', '.' ),
				'discount'      => $discount,
			);
		}
		wp_reset_postdata();
		wp_send_json_success( array( 'products' => $products ) );
	}

	// For tabbed products, return HTML cards
	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		$GLOBALS['product'] = wc_get_product( get_the_ID() );
		get_template_part( 'template-parts/product/product-card' );
	}
	$html = ob_get_clean();
	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_flavor_load_products', 'flavor_load_products_handler' );
add_action( 'wp_ajax_nopriv_flavor_load_products', 'flavor_load_products_handler' );

/**
 * Load more products — infinite scroll
 */
function flavor_load_more_products_handler() {
	check_ajax_referer( 'flavor_ajax_nonce', 'nonce' );

	$page     = absint( $_POST['page'] ?? 1 );
	$per_page = absint( $_POST['per_page'] ?? 10 );
	$per_page = min( $per_page, 20 );

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$query = new WP_Query( $args );

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		$GLOBALS['product'] = wc_get_product( get_the_ID() );
		get_template_part( 'template-parts/product/product-card' );
	}
	$html = ob_get_clean();
	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_flavor_load_more_products', 'flavor_load_more_products_handler' );
add_action( 'wp_ajax_nopriv_flavor_load_more_products', 'flavor_load_more_products_handler' );

/**
 * Toggle wishlist (simple session-based implementation)
 */
function flavor_toggle_wishlist_handler() {
	check_ajax_referer( 'flavor_ajax_nonce', 'nonce' );

	$product_id = absint( $_POST['product_id'] ?? 0 );
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Invalid product' ) );
	}

	if ( ! is_user_logged_in() ) {
		// For guests, just acknowledge (frontend handles visual state)
		wp_send_json_success( array( 'status' => 'toggled' ) );
	}

	$user_id  = get_current_user_id();
	$wishlist = get_user_meta( $user_id, 'flavor_wishlist', true );
	if ( ! is_array( $wishlist ) ) {
		$wishlist = array();
	}

	$key = array_search( $product_id, $wishlist, true );
	if ( false !== $key ) {
		unset( $wishlist[ $key ] );
		$status = 'removed';
	} else {
		$wishlist[] = $product_id;
		$status     = 'added';
	}

	update_user_meta( $user_id, 'flavor_wishlist', array_values( $wishlist ) );

	wp_send_json_success( array( 'status' => $status ) );
}
add_action( 'wp_ajax_flavor_toggle_wishlist', 'flavor_toggle_wishlist_handler' );
add_action( 'wp_ajax_nopriv_flavor_toggle_wishlist', 'flavor_toggle_wishlist_handler' );

/**
 * Localize AJAX data for frontend
 */
function flavor_ajax_localize_scripts() {
	wp_localize_script( 'flavor-app', 'flavorAjax', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'flavor_ajax_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'flavor_ajax_localize_scripts', 20 );
