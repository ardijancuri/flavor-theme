<?php
/**
 * The Template for displaying product archives (shop / category pages).
 *
 * Override of woocommerce/templates/archive-product.php
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_ajax = wp_doing_ajax();
?>

<div class="max-w-site-xxl mx-auto px-3 md:px-4" x-data="shopPage()">

	<?php get_template_part( 'template-parts/global/breadcrumbs' ); ?>

	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="text-xl md:text-2xl font-bold text-gray-700 mb-3">
			<?php woocommerce_page_title(); ?>
		</h1>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/shop/quick-filter-tabs' ); ?>

	<!-- Toolbar -->
	<div class="flex items-center justify-between gap-3 py-3 border-b border-gray-300">
		<button
			@click="filterDrawerOpen = true"
			class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:border-primary transition-colors"
		>
			<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
			<?php esc_html_e( 'Filters', 'flavor' ); ?>
			<span x-show="activeFilterCount > 0" x-text="activeFilterCount" class="bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"></span>
		</button>

		<?php get_template_part( 'template-parts/shop/sort-dropdown' ); ?>
	</div>

	<!-- Active filter chips -->
	<div x-show="activeFilters.length > 0" class="flex flex-wrap gap-2 py-2">
		<template x-for="chip in activeFilters" :key="chip.key">
			<span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-light text-primary text-xs rounded-full">
				<span x-text="chip.label"></span>
				<button @click="removeFilter(chip.key)" class="hover:text-red">
					<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
				</button>
			</span>
		</template>
		<button @click="clearAllFilters()" class="text-xs text-gray-600 underline hover:text-primary">
			<?php esc_html_e( 'Clear all', 'flavor' ); ?>
		</button>
	</div>

	<?php get_template_part( 'template-parts/shop/filter-drawer' ); ?>

	<?php get_template_part( 'template-parts/shop/product-grid' ); ?>

	<?php if ( ! $is_ajax ) : ?>
		<?php get_template_part( 'template-parts/shop/seo-content' ); ?>
	<?php endif; ?>

</div>

<?php
get_footer();
