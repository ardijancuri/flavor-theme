<?php
/**
 * Filter drawer - slides in from the left
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attribute_taxonomies = wc_get_attribute_taxonomies();
$brands               = get_terms( array(
	'taxonomy'   => 'pa_brand',
	'hide_empty' => true,
) );
?>

<!-- Backdrop -->
<div
	x-show="filterDrawerOpen"
	x-transition:enter="transition-opacity ease-out duration-300"
	x-transition:enter-start="opacity-0"
	x-transition:enter-end="opacity-100"
	x-transition:leave="transition-opacity ease-in duration-200"
	x-transition:leave-start="opacity-100"
	x-transition:leave-end="opacity-0"
	@click="filterDrawerOpen = false"
	class="fixed inset-0 bg-black/50 z-40"
	style="display: none;"
></div>

<!-- Drawer panel -->
<div
	x-show="filterDrawerOpen"
	x-transition:enter="transition-transform ease-out duration-300"
	x-transition:enter-start="-translate-x-full"
	x-transition:enter-end="translate-x-0"
	x-transition:leave="transition-transform ease-in duration-200"
	x-transition:leave-start="translate-x-0"
	x-transition:leave-end="-translate-x-full"
	class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white shadow-xl flex flex-col"
	style="display: none;"
	@keydown.escape.window="filterDrawerOpen = false"
>
	<!-- Header -->
	<div class="flex items-center justify-between px-4 py-3 border-b border-gray-300">
		<h3 class="text-base font-bold"><?php esc_html_e( 'Filters', 'flavor' ); ?></h3>
		<div class="flex items-center gap-3">
			<button @click="clearAllFilters()" class="text-xs text-primary underline"><?php esc_html_e( 'Clear All', 'flavor' ); ?></button>
			<button @click="filterDrawerOpen = false" class="p-1 text-gray-600 hover:text-gray-700">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
			</button>
		</div>
	</div>

	<!-- Scrollable filter groups -->
	<div class="flex-1 overflow-y-auto px-4 py-3 space-y-1">

		<!-- Price Range -->
		<div x-data="{ open: true }">
			<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm">
				<span><?php esc_html_e( 'Price Range', 'flavor' ); ?></span>
				<svg :class="open && 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
			</button>
			<div x-show="open" x-collapse class="pb-3">
				<div class="flex items-center gap-2">
					<input type="number" x-model.number="filters.price_min" placeholder="<?php esc_attr_e( 'Min', 'flavor' ); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-primary focus:outline-none" min="0">
					<span class="text-gray-500">–</span>
					<input type="number" x-model.number="filters.price_max" placeholder="<?php esc_attr_e( 'Max', 'flavor' ); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-primary focus:outline-none" min="0">
				</div>
			</div>
		</div>

		<!-- Brand -->
		<?php if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) : ?>
		<div x-data="{ open: true, brandSearch: '' }">
			<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm">
				<span><?php esc_html_e( 'Brand', 'flavor' ); ?></span>
				<svg :class="open && 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
			</button>
			<div x-show="open" x-collapse class="pb-3 space-y-2">
				<input type="text" x-model="brandSearch" placeholder="<?php esc_attr_e( 'Search brands...', 'flavor' ); ?>" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-primary focus:outline-none">
				<div class="max-h-40 overflow-y-auto space-y-1">
					<?php foreach ( $brands as $brand ) : ?>
					<label
						x-show="!brandSearch || '<?php echo esc_js( strtolower( $brand->name ) ); ?>'.includes(brandSearch.toLowerCase())"
						class="flex items-center gap-2 text-sm cursor-pointer"
					>
						<input
							type="checkbox"
							:checked="filters.brands.includes('<?php echo esc_js( $brand->slug ); ?>')"
							@change="toggleArrayFilter('brands', '<?php echo esc_js( $brand->slug ); ?>')"
							class="rounded border-gray-300 text-primary focus:ring-primary"
						>
						<span><?php echo esc_html( $brand->name ); ?></span>
						<span class="text-gray-500 text-xs">(<?php echo absint( $brand->count ); ?>)</span>
					</label>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<!-- Dynamic Attributes -->
		<?php if ( $attribute_taxonomies ) : ?>
			<?php foreach ( $attribute_taxonomies as $attribute ) :
				$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
				if ( 'pa_brand' === $taxonomy ) continue;
				$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
				if ( is_wp_error( $terms ) || empty( $terms ) ) continue;
			?>
			<div x-data="{ open: false }">
				<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm">
					<span><?php echo esc_html( $attribute->attribute_label ); ?></span>
					<svg :class="open && 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
				</button>
				<div x-show="open" x-collapse class="pb-3 space-y-1 max-h-40 overflow-y-auto">
					<?php foreach ( $terms as $term ) : ?>
					<label class="flex items-center gap-2 text-sm cursor-pointer">
						<input
							type="checkbox"
							:checked="(filters.attributes['<?php echo esc_js( $taxonomy ); ?>'] || []).includes('<?php echo esc_js( $term->slug ); ?>')"
							@change="toggleAttributeFilter('<?php echo esc_js( $taxonomy ); ?>', '<?php echo esc_js( $term->slug ); ?>')"
							class="rounded border-gray-300 text-primary focus:ring-primary"
						>
						<span><?php echo esc_html( $term->name ); ?></span>
					</label>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		<?php endif; ?>

		<!-- Rating -->
		<div x-data="{ open: true }">
			<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm">
				<span><?php esc_html_e( 'Rating', 'flavor' ); ?></span>
				<svg :class="open && 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
			</button>
			<div x-show="open" x-collapse class="pb-3 space-y-1">
				<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
				<label class="flex items-center gap-2 text-sm cursor-pointer">
					<input type="radio" name="rating" :checked="filters.rating === <?php echo $i; ?>" @change="filters.rating = <?php echo $i; ?>" class="text-primary focus:ring-primary">
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

		<!-- Stock Status -->
		<div x-data="{ open: true }">
			<button @click="open = !open" class="flex items-center justify-between w-full py-3 font-semibold text-sm">
				<span><?php esc_html_e( 'Availability', 'flavor' ); ?></span>
				<svg :class="open && 'rotate-180'" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
			</button>
			<div x-show="open" x-collapse class="pb-3 space-y-1">
				<label class="flex items-center gap-2 text-sm cursor-pointer">
					<input type="checkbox" :checked="filters.in_stock" @change="filters.in_stock = !filters.in_stock" class="rounded border-gray-300 text-primary focus:ring-primary">
					<span><?php esc_html_e( 'In stock only', 'flavor' ); ?></span>
				</label>
			</div>
		</div>

	</div>

	<!-- Sticky Apply Button -->
	<div class="px-4 py-3 border-t border-gray-300">
		<button @click="applyFilters()" class="w-full bg-primary text-white font-semibold py-2.5 rounded-lg hover:bg-primary/90 transition-colors">
			<?php esc_html_e( 'Apply Filters', 'flavor' ); ?>
		</button>
	</div>
</div>
