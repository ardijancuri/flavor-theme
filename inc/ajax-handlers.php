<?php
/**
 * AJAX handlers for Flavor theme
 *
 * @package Flavor
 */

defined('ABSPATH') || exit;

/* ================================================================
 * Cart AJAX Handlers
 * ================================================================ */

/**
 * Update cart item quantity.
 */
function flavor_update_cart_quantity() {
    check_ajax_referer('flavor_nonce');

    $key = sanitize_text_field($_POST['cart_item_key'] ?? '');
    $qty = absint($_POST['quantity'] ?? 1);

    if (empty($key)) {
        wp_send_json_error(['message' => __('Invalid cart item.', 'flavor')]);
    }

    $cart = WC()->cart;
    $result = $cart->set_quantity($key, $qty);

    if ($result === false) {
        wp_send_json_error(['message' => __('Could not update quantity.', 'flavor')]);
    }

    $cart->calculate_totals();
    $item = $cart->get_cart_item($key);

    ob_start();
    woocommerce_cart_totals();
    $totals_html = ob_get_clean();

    wp_send_json_success([
        'line_total'  => wc_price($item['line_total'] ?? 0),
        'totals_html' => $totals_html,
    ]);
}
add_action('wp_ajax_flavor_update_cart_quantity', 'flavor_update_cart_quantity');
add_action('wp_ajax_nopriv_flavor_update_cart_quantity', 'flavor_update_cart_quantity');

/**
 * Remove cart item.
 */
function flavor_remove_cart_item() {
    check_ajax_referer('flavor_nonce');

    $key = sanitize_text_field($_POST['cart_item_key'] ?? '');

    if (empty($key) || !WC()->cart->remove_cart_item($key)) {
        wp_send_json_error(['message' => __('Could not remove item.', 'flavor')]);
    }

    WC()->cart->calculate_totals();
    $cart_empty = WC()->cart->is_empty();

    ob_start();
    woocommerce_cart_totals();
    $totals_html = ob_get_clean();

    $empty_html = '';
    if ($cart_empty) {
        ob_start();
        wc_get_template('cart/cart-empty.php');
        $empty_html = ob_get_clean();
    }

    wp_send_json_success([
        'totals_html' => $totals_html,
        'cart_empty'  => $cart_empty,
        'empty_html'  => $empty_html,
    ]);
}
add_action('wp_ajax_flavor_remove_cart_item', 'flavor_remove_cart_item');
add_action('wp_ajax_nopriv_flavor_remove_cart_item', 'flavor_remove_cart_item');

/**
 * Apply coupon code.
 */
function flavor_apply_coupon() {
    check_ajax_referer('flavor_nonce');

    $code = sanitize_text_field($_POST['coupon_code'] ?? '');

    if (empty($code)) {
        wp_send_json_error(['message' => __('Please enter a coupon code.', 'flavor')]);
    }

    $result = WC()->cart->apply_coupon($code);

    if ($result) {
        WC()->cart->calculate_totals();

        ob_start();
        woocommerce_cart_totals();
        $totals_html = ob_get_clean();

        wp_send_json_success([
            'message'     => __('Coupon applied successfully.', 'flavor'),
            'totals_html' => $totals_html,
        ]);
    } else {
        $errors = wc_get_notices('error');
        wc_clear_notices();
        $msg = !empty($errors) ? wp_strip_all_tags($errors[0]['notice'] ?? '') : __('Invalid coupon code.', 'flavor');

        wp_send_json_error(['message' => $msg]);
    }
}
add_action('wp_ajax_flavor_apply_coupon', 'flavor_apply_coupon');
add_action('wp_ajax_nopriv_flavor_apply_coupon', 'flavor_apply_coupon');

/**
 * Toggle warranty on a cart item.
 */
function flavor_toggle_warranty() {
    check_ajax_referer('flavor_nonce');

    $key      = sanitize_text_field($_POST['cart_item_key'] ?? '');
    $warranty = sanitize_text_field($_POST['warranty'] ?? 'no');

    if (empty($key)) {
        wp_send_json_error(['message' => __('Invalid cart item.', 'flavor')]);
    }

    $cart = WC()->cart;
    $cart_items = $cart->get_cart();

    if (!isset($cart_items[$key])) {
        wp_send_json_error(['message' => __('Cart item not found.', 'flavor')]);
    }

    $cart->cart_contents[$key]['flavor_warranty'] = ($warranty === 'yes') ? 'yes' : 'no';
    $cart->calculate_totals();

    $item = $cart->get_cart_item($key);

    ob_start();
    woocommerce_cart_totals();
    $totals_html = ob_get_clean();

    wp_send_json_success([
        'line_total'  => wc_price($item['line_total'] ?? 0),
        'totals_html' => $totals_html,
    ]);
}
add_action('wp_ajax_flavor_toggle_warranty', 'flavor_toggle_warranty');
add_action('wp_ajax_nopriv_flavor_toggle_warranty', 'flavor_toggle_warranty');

/**
 * Return mini-cart HTML fragment.
 */
function flavor_mini_cart_fragments() {
    check_ajax_referer('flavor_nonce');

    ob_start();
    woocommerce_mini_cart();
    $html = ob_get_clean();

    wp_send_json_success([
        'html'  => $html,
        'count' => WC()->cart->get_cart_contents_count(),
    ]);
}
add_action('wp_ajax_flavor_mini_cart_fragments', 'flavor_mini_cart_fragments');
add_action('wp_ajax_nopriv_flavor_mini_cart_fragments', 'flavor_mini_cart_fragments');

/* ================================================================
 * Search AJAX Handlers
 * ================================================================ */

/**
 * Live product search.
 */
function flavor_live_search() {
    check_ajax_referer('flavor_nonce');

    $query = sanitize_text_field($_POST['query'] ?? '');

    if (strlen($query) < 2) {
        wp_send_json_success([]);
    }

    $search = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        's'              => $query,
    ]);

    $results = [];

    if ($search->have_posts()) {
        while ($search->have_posts()) {
            $search->the_post();
            $product = wc_get_product(get_the_ID());
            if (!$product) continue;

            $results[] = [
                'id'    => $product->get_id(),
                'name'  => $product->get_name(),
                'url'   => get_permalink(),
                'image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') ?: wc_placeholder_img_src('thumbnail'),
                'price' => $product->get_price_html(),
            ];
        }
    }
    wp_reset_postdata();

    wp_send_json_success($results);
}
add_action('wp_ajax_flavor_live_search', 'flavor_live_search');
add_action('wp_ajax_nopriv_flavor_live_search', 'flavor_live_search');

/* ================================================================
 * Filter AJAX Handlers
 * ================================================================ */

/**
 * Filter products with full criteria.
 */
function flavor_filter_products() {
    check_ajax_referer('flavor_nonce');

    $price_min  = floatval($_POST['price_min'] ?? 0);
    $price_max  = floatval($_POST['price_max'] ?? 0);
    $brands     = array_map('sanitize_text_field', (array) ($_POST['brands'] ?? []));
    $attributes = array_map('sanitize_text_field', (array) ($_POST['attributes'] ?? []));
    $rating     = absint($_POST['rating'] ?? 0);
    $stock      = sanitize_text_field($_POST['stock'] ?? '');
    $orderby    = sanitize_text_field($_POST['orderby'] ?? 'menu_order');
    $page       = max(1, absint($_POST['page'] ?? 1));
    $per_page   = absint(get_theme_mod('flavor_products_per_page', 12));

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'tax_query'      => ['relation' => 'AND'],
        'meta_query'     => ['relation' => 'AND'],
    ];

    // Orderby
    switch ($orderby) {
        case 'price':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order']    = 'ASC';
            break;
        case 'price-desc':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order']    = 'DESC';
            break;
        case 'popularity':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'total_sales';
            $args['order']    = 'DESC';
            break;
        case 'rating':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_wc_average_rating';
            $args['order']    = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
        default:
            $args['orderby'] = 'menu_order title';
            $args['order']   = 'ASC';
            break;
    }

    // Price range
    if ($price_min > 0 || $price_max > 0) {
        $price_meta = ['relation' => 'AND'];
        if ($price_min > 0) {
            $price_meta[] = [
                'key'     => '_price',
                'value'   => $price_min,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        }
        if ($price_max > 0) {
            $price_meta[] = [
                'key'     => '_price',
                'value'   => $price_max,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ];
        }
        $args['meta_query'][] = $price_meta;
    }

    // Brands
    if (!empty($brands)) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_brand',
            'field'    => 'slug',
            'terms'    => $brands,
        ];
    }

    // Attributes
    if (!empty($attributes)) {
        foreach ($attributes as $attr) {
            $parts = explode(':', $attr, 2);
            if (count($parts) === 2) {
                $args['tax_query'][] = [
                    'taxonomy' => 'pa_' . sanitize_title($parts[0]),
                    'field'    => 'slug',
                    'terms'    => [$parts[1]],
                ];
            }
        }
    }

    // Rating
    if ($rating > 0) {
        $args['meta_query'][] = [
            'key'     => '_wc_average_rating',
            'value'   => $rating,
            'compare' => '>=',
            'type'    => 'DECIMAL',
        ];
    }

    // Stock
    if ($stock === 'instock') {
        $args['meta_query'][] = [
            'key'   => '_stock_status',
            'value' => 'instock',
        ];
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
    } else {
        echo '<div class="no-products-found"><p>' . esc_html__('No products found matching your criteria.', 'flavor') . '</p></div>';
    }
    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html'     => $html,
        'total'    => $query->found_posts,
        'has_more' => $page < $query->max_num_pages,
    ]);
}
add_action('wp_ajax_flavor_filter_products', 'flavor_filter_products');
add_action('wp_ajax_nopriv_flavor_filter_products', 'flavor_filter_products');

/**
 * Quick filter (bestseller / most_viewed / top_rated).
 */
function flavor_quick_filter() {
    check_ajax_referer('flavor_nonce');

    $tab      = sanitize_text_field($_POST['tab'] ?? 'bestseller');
    $per_page = absint(get_theme_mod('flavor_products_per_page', 12));

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
    ];

    switch ($tab) {
        case 'most_viewed':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'post_views';
            $args['order']    = 'DESC';
            break;
        case 'top_rated':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_wc_average_rating';
            $args['order']    = 'DESC';
            break;
        case 'bestseller':
        default:
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'total_sales';
            $args['order']    = 'DESC';
            break;
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
    } else {
        echo '<div class="no-products-found"><p>' . esc_html__('No products found.', 'flavor') . '</p></div>';
    }
    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_flavor_quick_filter', 'flavor_quick_filter');
add_action('wp_ajax_nopriv_flavor_quick_filter', 'flavor_quick_filter');
