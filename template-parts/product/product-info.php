<?php
/**
 * Product Info — right column of single product page
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$active_price  = (float) $product->get_price();
$is_on_sale    = $product->is_on_sale() && $regular_price > 0;
$discount_pct  = $is_on_sale ? round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 ) : 0;
$savings       = $is_on_sale ? $regular_price - $sale_price : 0;
$price_no_vat  = $active_price / 1.19; // 19% VAT assumed
$stock_qty     = $product->get_stock_quantity();
$sku           = $product->get_sku();
$brand_terms   = get_the_terms( $product->get_id(), 'pa_brand' );
$warranty_yrs  = get_post_meta( $product->get_id(), '_flavor_warranty_years', true );
?>

<div class="space-y-4">
	<!-- Title + badges -->
	<div>
		<h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-tight">
			<?php the_title(); ?>
		</h1>

		<div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
			<?php if ( $warranty_yrs ) : ?>
				<span class="inline-flex items-center gap-1 text-green-700 bg-green-50 px-2 py-0.5 rounded">
					<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
					<?php printf( esc_html__( '%d year warranty', 'flavor' ), (int) $warranty_yrs ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ) : ?>
				<a href="<?php echo esc_url( get_term_link( $brand_terms[0] ) ); ?>" class="hover:text-primary transition-colors">
					<?php echo esc_html( $brand_terms[0]->name ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $sku ) : ?>
				<span><?php esc_html_e( 'SKU:', 'flavor' ); ?> <?php echo esc_html( $sku ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Price block -->
	<div class="space-y-1">
		<div class="flex items-center gap-3 flex-wrap">
			<?php if ( $is_on_sale ) : ?>
				<span class="text-sm text-gray-400 line-through"><?php echo wc_price( $regular_price ); ?></span>
			<?php endif; ?>

			<span class="text-2xl font-bold text-gray-900"><?php echo wc_price( $active_price ); ?></span>

			<?php if ( $is_on_sale && $discount_pct > 0 ) : ?>
				<span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-0.5">
					-<?php echo absint( $discount_pct ); ?>%
				</span>
			<?php endif; ?>
		</div>

		<?php if ( $is_on_sale && $savings > 0 ) : ?>
			<p class="text-green-600 text-sm font-medium">
				<?php printf( esc_html__( 'You save %s', 'flavor' ), wc_price( $savings ) ); ?>
			</p>
		<?php endif; ?>

		<p class="text-xs text-gray-400">
			<?php esc_html_e( 'VAT included', 'flavor' ); ?> &middot;
			<?php printf( esc_html__( '%s without VAT', 'flavor' ), wc_price( $price_no_vat ) ); ?>
		</p>
	</div>

	<!-- Stock -->
	<div class="text-sm font-medium">
		<?php if ( ! $product->is_in_stock() ) : ?>
			<span class="text-red-600"><?php esc_html_e( 'Out of stock', 'flavor' ); ?></span>
		<?php elseif ( $stock_qty === null || $stock_qty > 10 ) : ?>
			<span class="text-green-600"><?php esc_html_e( 'More than 10 in stock', 'flavor' ); ?></span>
		<?php elseif ( $stock_qty > 0 ) : ?>
			<span class="text-green-600"><?php esc_html_e( 'In stock', 'flavor' ); ?></span>
		<?php endif; ?>
	</div>

	<!-- Add to Cart -->
	<?php if ( $product->is_in_stock() ) : ?>
		<div class="space-y-3" id="flavor-main-cta">
			<div class="flex items-center gap-3">
				<!-- Qty -->
				<div class="flex items-center border border-gray-300 rounded-lg overflow-hidden" x-data>
					<button @click="$dispatch('qty-change', -1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition-colors" type="button" aria-label="<?php esc_attr_e( 'Decrease quantity', 'flavor' ); ?>">
						<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M5 12h14"/></svg>
					</button>
					<input type="number" x-model.number="qty" min="1" class="w-12 text-center border-x border-gray-300 py-2 text-sm focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" aria-label="<?php esc_attr_e( 'Quantity', 'flavor' ); ?>">
					<button @click="$dispatch('qty-change', 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition-colors" type="button" aria-label="<?php esc_attr_e( 'Increase quantity', 'flavor' ); ?>">
						<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14m-7-7h14"/></svg>
					</button>
				</div>

				<!-- Add to Cart button -->
				<button
					@click="addToCart()"
					:disabled="adding"
					class="flex-1 bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
				>
					<svg x-show="adding" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
					<svg x-show="!adding" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
					<span x-text="adding ? '<?php esc_attr_e( 'Adding…', 'flavor' ); ?>' : '<?php esc_attr_e( 'Add to Cart', 'flavor' ); ?>'"></span>
				</button>
			</div>

			<!-- Buy Now -->
			<button
				@click="buyNow()"
				class="w-full border border-primary text-primary font-semibold py-3 px-6 rounded-lg hover:bg-primary/5 transition-colors flex items-center justify-center gap-2"
			>
				<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
				<?php esc_html_e( 'Buy Now', 'flavor' ); ?>
			</button>
		</div>
	<?php endif; ?>

	<!-- Wishlist -->
	<button
		@click="wishlisted = !wishlisted"
		class="flex items-center gap-2 text-sm text-gray-500 hover:text-red-500 transition-colors"
		:class="wishlisted && 'text-red-500'"
	>
		<svg class="w-5 h-5" :fill="wishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
		<span x-text="wishlisted ? '<?php esc_attr_e( 'In wishlist', 'flavor' ); ?>' : '<?php esc_attr_e( 'Add to wishlist', 'flavor' ); ?>'"></span>
	</button>
</div>
