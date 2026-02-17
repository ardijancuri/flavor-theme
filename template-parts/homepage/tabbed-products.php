<?php
/**
 * Tabbed Products — Recommended products with category tabs
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enabled = get_theme_mod( 'flavor_tabbed_products_enabled', true );
if ( ! $enabled ) {
	return;
}

$section_title = get_theme_mod( 'flavor_tabbed_products_title', __( 'Recommended for you', 'flavor' ) );

// Build tabs: first is "For you" (all), rest from customizer categories
$tabs = array(
	array(
		'slug' => 'for-you',
		'name' => esc_html__( 'For you', 'flavor' ),
		'cat'  => 0,
	),
);

$tab_cats = get_theme_mod( 'flavor_tabbed_products_categories', '' );
if ( $tab_cats ) {
	$cat_ids = array_filter( array_map( 'absint', explode( ',', $tab_cats ) ) );
	foreach ( $cat_ids as $cat_id ) {
		$term = get_term( $cat_id, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$tabs[] = array(
				'slug' => $term->slug,
				'name' => esc_html( $term->name ),
				'cat'  => $cat_id,
			);
		}
	}
}

$see_all_link = get_theme_mod( 'flavor_tabbed_products_see_all_link', '' );
?>

<section class="my-6" x-data="flavorTabbedProducts()" aria-label="<?php echo esc_attr( $section_title ); ?>">
	<!-- Header -->
	<div class="flex items-center justify-between mb-4">
		<h2 class="text-lg font-bold text-gray-700"><?php echo esc_html( $section_title ); ?></h2>
		<?php if ( $see_all_link ) : ?>
			<a href="<?php echo esc_url( $see_all_link ); ?>" class="text-sm text-primary hover:underline">
				<?php esc_html_e( 'See all', 'flavor' ); ?>
				<svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
			</a>
		<?php endif; ?>
	</div>

	<!-- Tabs -->
	<div class="flex gap-1 overflow-x-auto pb-2 mb-4 border-b border-gray-300 scrollbar-hide">
		<?php foreach ( $tabs as $i => $tab ) : ?>
			<button @click="switchTab('<?php echo esc_attr( $tab['slug'] ); ?>', <?php echo esc_attr( $tab['cat'] ); ?>)"
					:class="activeTab === '<?php echo esc_attr( $tab['slug'] ); ?>' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700'"
					class="px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 -mb-px transition-colors">
				<?php echo esc_html( $tab['name'] ); ?>
			</button>
		<?php endforeach; ?>
	</div>

	<!-- Product Grid -->
	<div class="grid grid-cols-2 tablet-sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-3">
		<!-- Skeleton placeholders -->
		<template x-if="loading">
			<template x-for="n in 10" :key="n">
				<?php get_template_part( 'template-parts/product/product-card-skeleton' ); ?>
			</template>
		</template>

		<!-- Products -->
		<div x-show="!loading" x-html="productsHtml"></div>
	</div>
</section>

<script>
function flavorTabbedProducts() {
	return {
		activeTab: '<?php echo esc_js( $tabs[0]['slug'] ); ?>',
		loading: true,
		productsHtml: '',
		init() {
			this.loadProducts(<?php echo esc_js( $tabs[0]['cat'] ); ?>);
		},
		switchTab(slug, catId) {
			if (this.activeTab === slug) return;
			this.activeTab = slug;
			this.loadProducts(catId);
		},
		loadProducts(catId) {
			this.loading = true;
			this.productsHtml = '';

			const data = new FormData();
			data.append('action', 'flavor_load_products');
			data.append('nonce', flavorAjax.nonce);
			data.append('category', catId);
			data.append('per_page', 10);

			fetch(flavorAjax.url, { method: 'POST', body: data })
				.then(r => r.json())
				.then(res => {
					if (res.success) {
						this.productsHtml = res.data.html;
					}
					this.loading = false;
				})
				.catch(() => { this.loading = false; });
		}
	};
}
</script>
