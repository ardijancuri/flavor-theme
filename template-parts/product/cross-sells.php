<?php
/**
 * Cross-sells — Swiper carousel of related products
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$cross_sell_ids = $product->get_cross_sell_ids();

if ( empty( $cross_sell_ids ) ) {
	$cross_sell_ids = wc_get_related_products( $product->get_id(), 12 );
}

if ( empty( $cross_sell_ids ) ) {
	return;
}

$cross_sells = wc_get_products( array(
	'include' => $cross_sell_ids,
	'limit'   => 12,
	'status'  => 'publish',
) );

if ( empty( $cross_sells ) ) {
	return;
}
?>

<div class="mt-8 md:mt-12">
	<h2 class="text-lg md:text-xl font-bold text-gray-900 mb-4">
		<?php esc_html_e( 'Customers who bought this also bought', 'flavor' ); ?>
	</h2>

	<div class="swiper flavor-cross-sells-swiper">
		<div class="swiper-wrapper">
			<?php foreach ( $cross_sells as $cs_product ) :
				$GLOBALS['post'] = get_post( $cs_product->get_id() ); // phpcs:ignore
				setup_postdata( $GLOBALS['post'] );
			?>
				<div class="swiper-slide">
					<?php get_template_part( 'template-parts/product/product-card' ); ?>
				</div>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
	</div>
</div>
