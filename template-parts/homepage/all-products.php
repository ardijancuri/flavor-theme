<?php
/**
 * All Products — Infinite scroll grid
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enabled   = get_theme_mod( 'flavor_all_products_enabled', true );
if ( ! $enabled ) {
	return;
}

$title     = get_theme_mod( 'flavor_all_products_title', __( 'All Products', 'flavor' ) );
$per_page  = absint( get_theme_mod( 'flavor_all_products_per_page', 10 ) );

// Initial products
$args = array(
	'status'   => 'publish',
	'limit'    => $per_page,
	'orderby'  => 'date',
	'order'    => 'DESC',
	'paginate' => true,
);
$results  = wc_get_products( $args );
$products = $results->products;
$max_pages = $results->max_num_pages;
?>

<section class="my-6" x-data="flavorAllProducts()" aria-label="<?php echo esc_attr( $title ); ?>">
	<h2 class="text-lg font-bold text-gray-700 mb-4"><?php echo esc_html( $title ); ?></h2>

	<div class="grid grid-cols-2 tablet-sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-3" id="allProductsGrid">
		<?php foreach ( $products as $product ) :
			$GLOBALS['product'] = $product;
			get_template_part( 'template-parts/product/product-card' );
		endforeach;
		wp_reset_postdata();
		?>

		<!-- Skeleton placeholders for loading -->
		<template x-if="loading">
			<template x-for="n in <?php echo esc_attr( $per_page ); ?>" :key="n">
				<?php get_template_part( 'template-parts/product/product-card-skeleton' ); ?>
			</template>
		</template>
	</div>

	<!-- Appended products -->
	<div x-ref="moreProducts" class="grid grid-cols-2 tablet-sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-3"></div>

	<!-- Show More / End message -->
	<div class="text-center mt-6">
		<template x-if="!ended && !loading">
			<button @click="loadMore()"
					class="bg-white border border-gray-300 text-sm font-medium text-gray-700 px-8 py-2.5 rounded-lg hover:shadow-md transition-shadow">
				<?php esc_html_e( 'Show More', 'flavor' ); ?>
			</button>
		</template>
		<template x-if="loading">
			<span class="text-sm text-gray-500"><?php esc_html_e( 'Loading...', 'flavor' ); ?></span>
		</template>
		<template x-if="ended">
			<p class="text-sm text-gray-500"><?php esc_html_e( 'End of results', 'flavor' ); ?></p>
		</template>
	</div>
</section>

<script>
function flavorAllProducts() {
	return {
		page: 1,
		maxPages: <?php echo (int) $max_pages; ?>,
		loading: false,
		ended: <?php echo $max_pages <= 1 ? 'true' : 'false'; ?>,
		loadMore() {
			if (this.loading || this.ended) return;
			this.page++;
			this.loading = true;

			const data = new FormData();
			data.append('action', 'flavor_load_more_products');
			data.append('nonce', flavorData.nonce);
			data.append('page', this.page);
			data.append('per_page', <?php echo (int) $per_page; ?>);

			fetch(flavorData.ajaxUrl, { method: 'POST', body: data })
				.then(r => r.json())
				.then(res => {
					if (res.success && res.data.html) {
						this.$refs.moreProducts.insertAdjacentHTML('beforeend', res.data.html);
					}
					if (this.page >= this.maxPages || !res.data.html) {
						this.ended = true;
					}
					this.loading = false;
				})
				.catch(() => { this.loading = false; });
		}
	};
}
</script>
