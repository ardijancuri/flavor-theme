<?php
/**
 * Filter sidebar — persistent left sidebar for shop/category pages (desktop).
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => 0,
) );

$brands = get_terms( array(
	'taxonomy'   => 'pa_brand',
	'hide_empty' => true,
) );

$chevron_svg = '<svg class="w-4 h-4 transition-transform" :class="open && \'rotate-180\'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
?>

<div class="sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto pb-6">

	<!-- Clear all + Active chips -->
	<div x-show="activeFilters.length > 0" class="mb-4">
		<div class="flex items-center justify-between mb-2">
			<span class="text-xs font-semibold text-gray-500 uppercase tracking-wide"><?php esc_html_e( 'Active Filters', 'flavor' ); ?></span>
			<button @click="clearAllFilters()" class="text-xs text-primary underline hover:text-primary/80"><?php esc_html_e( 'Clear all', 'flavor' ); ?></button>
		</div>
		<div class="flex flex-wrap gap-1.5">
			<template x-for="chip in activeFilters" :key="chip.key">
				<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
					<span x-text="chip.label"></span>
					<button @click="removeFilter(chip.key)" class="hover:text-red-500">
						<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
					</button>
				</span>
			</template>
		</div>
	</div>

	<!-- Price Range -->
	<div x-data="{ open: true }">
		<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm border-b border-gray-200">
			<span><?php esc_html_e( 'Price', 'flavor' ); ?></span>
			<?php echo $chevron_svg; ?>
		</button>
		<div x-show="open" x-collapse class="py-3">
			<div class="flex items-center gap-2">
				<input type="number" x-model.number="filters.price_min" placeholder="<?php esc_attr_e( 'Min €', 'flavor' ); ?>" class="js-filter-price-min w-1/2 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-primary focus:outline-none accent-primary" min="0">
				<span class="text-gray-400">–</span>
				<input type="number" x-model.number="filters.price_max" placeholder="<?php esc_attr_e( 'Max €', 'flavor' ); ?>" class="js-filter-price-max w-1/2 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-primary focus:outline-none accent-primary" min="0">
			</div>
			<button @click="applyFilters()" class="mt-2 w-full text-center text-xs font-semibold text-primary border border-primary rounded py-1.5 hover:bg-primary hover:text-white transition-colors">
				<?php esc_html_e( 'Apply', 'flavor' ); ?>
			</button>
		</div>
	</div>

	<!-- Categories -->
	<?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
	<div x-data="{ open: true, showAll: false }">
		<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm border-b border-gray-200">
			<span><?php esc_html_e( 'Categories', 'flavor' ); ?></span>
			<?php echo $chevron_svg; ?>
		</button>
		<div x-show="open" x-collapse class="py-3 space-y-1">
			<?php foreach ( $categories as $index => $cat ) : ?>
			<label class="flex items-center gap-2 text-sm cursor-pointer" <?php if ( $index >= 8 ) : ?>x-show="showAll"<?php endif; ?>>
				<input
					type="checkbox"
					:checked="filters.categories && filters.categories.includes('<?php echo esc_js( $cat->slug ); ?>')"
					@change="toggleArrayFilter('categories', '<?php echo esc_js( $cat->slug ); ?>')"
					class="rounded border-gray-300 accent-primary focus:ring-primary"
				>
				<span><?php echo esc_html( $cat->name ); ?></span>
				<span class="text-gray-400 text-xs">(<?php echo absint( $cat->count ); ?>)</span>
			</label>
			<?php endforeach; ?>
			<?php if ( count( $categories ) > 8 ) : ?>
			<button @click="showAll = !showAll" class="text-xs text-primary underline mt-1" x-text="showAll ? '<?php echo esc_js( __( 'Show less', 'flavor' ) ); ?>' : '<?php echo esc_js( __( 'Show more', 'flavor' ) ); ?>'"></button>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- Brands -->
	<?php if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) : ?>
	<div x-data="{ open: true, brandSearch: '' }">
		<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm border-b border-gray-200">
			<span><?php esc_html_e( 'Brand', 'flavor' ); ?></span>
			<?php echo $chevron_svg; ?>
		</button>
		<div x-show="open" x-collapse class="py-3 space-y-2">
			<input type="text" x-model="brandSearch" placeholder="<?php esc_attr_e( 'Search brands...', 'flavor' ); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-primary focus:outline-none">
			<div class="max-h-40 overflow-y-auto space-y-1">
				<?php foreach ( $brands as $brand ) : ?>
				<label
					x-show="!brandSearch || '<?php echo esc_js( mb_strtolower( $brand->name ) ); ?>'.includes(brandSearch.toLowerCase())"
					class="flex items-center gap-2 text-sm cursor-pointer js-brand-item"
				>
					<input
						type="checkbox"
						value="<?php echo esc_attr( $brand->slug ); ?>"
						:checked="filters.brands.includes('<?php echo esc_js( $brand->slug ); ?>')"
						@change="toggleArrayFilter('brands', '<?php echo esc_js( $brand->slug ); ?>')"
						class="js-filter-brand rounded border-gray-300 accent-primary focus:ring-primary"
					>
					<span><?php echo esc_html( $brand->name ); ?></span>
					<span class="text-gray-400 text-xs">(<?php echo absint( $brand->count ); ?>)</span>
				</label>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Rating -->
	<div x-data="{ open: true }">
		<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm border-b border-gray-200">
			<span><?php esc_html_e( 'Rating', 'flavor' ); ?></span>
			<?php echo $chevron_svg; ?>
		</button>
		<div x-show="open" x-collapse class="py-3 space-y-1">
			<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
			<label class="flex items-center gap-2 text-sm cursor-pointer">
				<input type="radio" name="sidebar_rating" :checked="filters.rating === <?php echo $i; ?>" @change="filters.rating = <?php echo $i; ?>; applyFilters()" class="js-filter-rating accent-primary focus:ring-primary" value="<?php echo $i; ?>">
				<span class="flex items-center gap-0.5">
					<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
						<svg class="w-4 h-4 <?php echo $s <= $i ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
					<?php endfor; ?>
					<span class="text-gray-500 ml-1"><?php esc_html_e( '& up', 'flavor' ); ?></span>
				</span>
			</label>
			<?php endfor; ?>
		</div>
	</div>

	<!-- Availability -->
	<div x-data="{ open: true }">
		<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm border-b border-gray-200">
			<span><?php esc_html_e( 'Availability', 'flavor' ); ?></span>
			<?php echo $chevron_svg; ?>
		</button>
		<div x-show="open" x-collapse class="py-3 space-y-1">
			<label class="flex items-center gap-2 text-sm cursor-pointer">
				<input type="checkbox" :checked="filters.in_stock" @change="filters.in_stock = !filters.in_stock; applyFilters()" class="js-filter-stock rounded border-gray-300 accent-primary focus:ring-primary">
				<span><?php esc_html_e( 'In stock', 'flavor' ); ?></span>
			</label>
			<label class="flex items-center gap-2 text-sm cursor-pointer">
				<input type="checkbox" :checked="filters.out_of_stock" @change="filters.out_of_stock = !filters.out_of_stock; applyFilters()" class="rounded border-gray-300 accent-primary focus:ring-primary">
				<span><?php esc_html_e( 'Out of stock', 'flavor' ); ?></span>
			</label>
		</div>
	</div>

	<!-- On Sale -->
	<div x-data="{ open: true }">
		<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm border-b border-gray-200">
			<span><?php esc_html_e( 'On Sale', 'flavor' ); ?></span>
			<?php echo $chevron_svg; ?>
		</button>
		<div x-show="open" x-collapse class="py-3">
			<label class="flex items-center gap-2 text-sm cursor-pointer">
				<input type="checkbox" :checked="filters.on_sale" @change="filters.on_sale = !filters.on_sale; applyFilters()" class="rounded border-gray-300 accent-primary focus:ring-primary">
				<span><?php esc_html_e( 'Show only discounted', 'flavor' ); ?></span>
			</label>
		</div>
	</div>

</div>
