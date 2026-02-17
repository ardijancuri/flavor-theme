<?php
/**
 * AJAX handlers for Flavor theme
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter products via AJAX
 */
add_action( 'wp_ajax_flavor_filter_products', 'flavor_filter_products' );
add_action( 'wp_ajax_nopriv_flavor_filter_products', 'flavor_filter_products' );

function flavor_filter_products() {
	check_ajax_referer( 'flavor_ajax_nonce', 'nonce' );

	$price_min   = isset( $_POST['price_min'] ) ? floatval( $_POST['price_min'] ) : 0;
	$price_max   = isset( $_POST['price_max'] ) ? floatval( $_POST['price_max'] ) : 999999;
	$brands      = ! empty( $_POST['brands'] ) ? array_map( 'sanitize_text_field', explode( ',', sanitize_text_field( wp_unslash( $_POST['brands'] ) ) ) ) : array();
	$rating      = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
	$in_stock    = isset( $_POST['in_stock'] ) && $_POST['in_stock'] === '1';
	$sort        = isset( $_POST['sort'] ) ? sanitize_text_field( wp_unslash( $_POST['sort'] ) ) : 'default';
	$page        = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
	$product_cat = isset( $_POST['product_cat'] ) ? sanitize_text_field( wp_unslash( $_POST['product_cat'] ) ) : '';
	$per_page    = get_theme_mod( 'flavor_products_per_page', 12 );

	$meta_query = array( 'relation' => 'AND' );
	$tax_query  = array( 'relation' => 'AND' );

	// Price
	$meta_query[] = array(
		'key'     => '_price',
		'value'   => array( $price_min, $price_max ),
		'type'    => 'NUMERIC',
		'compare' => 'BETWEEN',
	);

	// Stock
	if ( $in_stock ) {
		$meta_query[] = array(
			'key'   => '_stock_status',
			'value' => 'instock',
		);
	}

	// Brands
	if ( ! empty( $brands ) ) {
		$tax_query[] = array(
			'taxonomy' => 'pa_brand',
			'field'    => 'name',
			'terms'    => $brands,
		);
	}

	// Category
	if ( $product_cat ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $product_cat,
		);
	}

	// Sort
	$orderby  = 'date';
	$order    = 'DESC';
	$meta_key = '';

	switch ( $sort ) {
		case 'price_asc':
			$orderby  = 'meta_value_num';
			$order    = 'ASC';
			$meta_key = '_price';
			break;
		case 'price_desc':
			$orderby  = 'meta_value_num';
			$order    = 'DESC';
			$meta_key = '_price';
			break;
		case 'popularity':
			$orderby  = 'meta_value_num';
			$order    = 'DESC';
			$meta_key = 'total_sales';
			break;
		case 'rating':
			$orderby  = 'meta_value_num';
			$order    = 'DESC';
			$meta_key = '_wc_average_rating';
			break;
		case 'name_asc':
			$orderby = 'title';
			$order   = 'ASC';
			break;
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $page,
		'meta_query'     => $meta_query, // phpcs:ignore
		'tax_query'      => $tax_query,  // phpcs:ignore
		'orderby'        => $orderby,
		'order'          => $order,
	);

	if ( $meta_key ) {
		$args['meta_key'] = $meta_key; // phpcs:ignore
	}

	// Rating filter via post IDs
	if ( $rating > 0 ) {
		$rated_ids = flavor_get_products_by_min_rating( $rating );
		if ( empty( $rated_ids ) ) {
			wp_send_json_success( array( 'html' => '<p class="text-center text-gray-500 col-span-full py-8">' . esc_html__( 'No products found.', 'flavor' ) . '</p>', 'total' => 0, 'pagination' => '' ) );
			return;
		}
		$args['post__in'] = $rated_ids;
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/product/product-card' );
		}
	} else {
		echo '<p class="text-center text-gray-500 col-span-full py-8">' . esc_html__( 'No products found.', 'flavor' ) . '</p>';
	}
	$html = ob_get_clean();

	// Pagination
	$pagination = '';
	if ( $query->max_num_pages > 1 ) {
		ob_start();
		echo paginate_links( array(
			'total'   => $query->max_num_pages,
			'current' => $page,
			'format'  => '?paged=%#%',
			'type'    => 'list',
		) );
		$pagination = ob_get_clean();
	}

	wp_reset_postdata();

	wp_send_json_success( array(
		'html'       => $html,
		'total'      => $query->found_posts,
		'pagination' => $pagination,
	) );
}

/**
 * Quick filter tabs — bestseller, most-viewed, top-rated
 */
add_action( 'wp_ajax_flavor_quick_filter', 'flavor_quick_filter' );
add_action( 'wp_ajax_nopriv_flavor_quick_filter', 'flavor_quick_filter' );

function flavor_quick_filter() {
	check_ajax_referer( 'flavor_ajax_nonce', 'nonce' );

	$tab         = isset( $_POST['tab'] ) ? sanitize_text_field( wp_unslash( $_POST['tab'] ) ) : 'bestseller';
	$product_cat = isset( $_POST['product_cat'] ) ? sanitize_text_field( wp_unslash( $_POST['product_cat'] ) ) : '';
	$per_page    = get_theme_mod( 'flavor_products_per_page', 12 );

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
	);

	if ( $product_cat ) {
		$args['tax_query'] = array( // phpcs:ignore
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $product_cat,
			),
		);
	}

	switch ( $tab ) {
		case 'bestseller':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;
		case 'most-viewed':
			$args['meta_key'] = 'flavor_views_count'; // phpcs:ignore
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;
		case 'top-rated':
			$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/product/product-card' );
		}
	} else {
		echo '<p class="text-center text-gray-500 col-span-full py-8">' . esc_html__( 'No products found.', 'flavor' ) . '</p>';
	}
	$html = ob_get_clean();
	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
}

/**
 * Helper: get product IDs with minimum average rating
 */
function flavor_get_products_by_min_rating( $min_rating ) {
	global $wpdb;
	$results = $wpdb->get_col( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND meta_value >= %f",
		(float) $min_rating
	) );
	return array_map( 'absint', $results );
}

/**
 * Add to cart with optional warranty (AJAX)
 */
add_action( 'wp_ajax_flavor_add_to_cart', 'flavor_add_to_cart_handler' );
add_action( 'wp_ajax_nopriv_flavor_add_to_cart', 'flavor_add_to_cart_handler' );

function flavor_add_to_cart_handler() {
	check_ajax_referer( 'flavor_ajax_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	$warranty   = isset( $_POST['warranty'] ) && $_POST['warranty'] === '1';

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'flavor' ) ) );
		return;
	}

	$cart_item_data = array();
	if ( $warranty ) {
		$warranty_price = (float) get_post_meta( $product_id, '_flavor_warranty_price', true );
		if ( $warranty_price > 0 ) {
			$cart_item_data['flavor_warranty']       = true;
			$cart_item_data['flavor_warranty_price'] = $warranty_price;
		}
	}

	$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );

	if ( $cart_item_key ) {
		ob_start();
		wc_print_notices();
		$notices = ob_get_clean();

		// Get updated fragments
		ob_start();
		woocommerce_mini_cart();
		$mini_cart = ob_get_clean();

		wp_send_json_success( array(
			'cart_hash'  => WC()->cart->get_cart_hash(),
			'cart_count' => WC()->cart->get_cart_contents_count(),
			'fragments'  => apply_filters( 'woocommerce_add_to_cart_fragments', array(
				'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
			) ),
		) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Could not add to cart.', 'flavor' ) ) );
	}
}

/**
 * Handle buy-now redirect to checkout
 */
add_action( 'woocommerce_add_to_cart_redirect', 'flavor_buy_now_redirect' );

function flavor_buy_now_redirect( $url ) {
	if ( isset( $_POST['flavor_redirect_checkout'] ) && $_POST['flavor_redirect_checkout'] === '1' ) {
		return wc_get_checkout_url();
	}
	return $url;
}
