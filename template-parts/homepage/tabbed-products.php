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

<section class="my-6" x-data="flavorTabbedProducts({initialTab: 'for-you', initialCat: 0})" aria-label="<?php echo esc_attr( $section_title ); ?>">
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
	<div class="flex border-b border-gray-200 mb-4 overflow-x-auto scrollbar-hide">
		<?php foreach ( $tabs as $i => $tab ) : ?>
			<button @click="switchTab('<?php echo esc_attr( $tab['slug'] ); ?>', <?php echo esc_attr( $tab['cat'] ); ?>)"
					class="px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors relative"
					:class="activeTab === '<?php echo esc_attr( $tab['slug'] ); ?>' ? 'text-primary' : 'text-gray-500 hover:text-gray-700'">
				<?php echo esc_html( $tab['name'] ); ?>
				<span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary transition-opacity"
					  :class="activeTab === '<?php echo esc_attr( $tab['slug'] ); ?>' ? 'opacity-100' : 'opacity-0'"></span>
			</button>
		<?php endforeach; ?>
	</div>

	<!-- Product Grid (AJAX loaded) -->
	<div x-html="productsHtml"
		 class="grid grid-cols-2 tablet-sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 transition-opacity duration-300"
		 :class="loading ? 'opacity-0' : 'opacity-100'"></div>
</section>
