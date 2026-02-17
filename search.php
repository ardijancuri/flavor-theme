<?php
/**
 * Search Results Page
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$search_query = get_search_query();
$total_results = $wp_query->found_posts;
?>

<div class="max-w-site-xxl mx-auto px-3 md:px-4 py-6" x-data="shopPage()">

	<?php get_template_part( 'template-parts/global/breadcrumbs' ); ?>

	<h1 class="text-xl md:text-2xl font-bold text-gray-700 mb-1">
		<?php
		/* translators: %s: search query */
		printf( esc_html__( 'Search results for "%s"', 'flavor' ), esc_html( $search_query ) );
		?>
	</h1>
	<p class="text-sm text-gray-500 mb-4">
		<?php
		/* translators: %d: number of results */
		printf( esc_html( _n( '%d product found', '%d products found', $total_results, 'flavor' ) ), esc_html( $total_results ) );
		?>
	</p>

	<?php if ( have_posts() ) : ?>

		<!-- Toolbar -->
		<div class="flex items-center justify-between gap-3 py-3 border-b border-gray-300 mb-4">
			<button @click="filterDrawerOpen = true" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:border-primary transition-colors">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
				<?php esc_html_e( 'Filters', 'flavor' ); ?>
			</button>

			<div class="flex items-center gap-2">
				<label for="orderby" class="text-xs text-gray-500"><?php esc_html_e( 'Sort by:', 'flavor' ); ?></label>
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>

		<!-- Product Grid -->
		<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endwhile; ?>
		</div>

		<!-- Pagination -->
		<div class="flex justify-center mt-8">
			<?php
			echo paginate_links( array( // phpcs:ignore
				'prev_text' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>',
				'next_text' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>',
			) );
			?>
		</div>

	<?php else : ?>

		<div class="py-12">
			<?php
			get_template_part( 'template-parts/global/empty-states', null, array(
				'icon'     => '<svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>',
				'title'    => esc_html__( 'No products found', 'flavor' ),
				'message'  => esc_html__( 'We couldn\'t find any products matching your search. Try different keywords or browse our categories below.', 'flavor' ),
				'cta_url'  => esc_url( wc_get_page_permalink( 'shop' ) ),
				'cta_text' => esc_html__( 'Browse All Products', 'flavor' ),
			) );
			?>

			<!-- Search Suggestions -->
			<div class="max-w-md mx-auto mt-6">
				<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="flex gap-2">
					<input type="search" name="s" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary" placeholder="<?php esc_attr_e( 'Try a different search...', 'flavor' ); ?>" value="<?php echo esc_attr( $search_query ); ?>">
					<input type="hidden" name="post_type" value="product">
					<button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg transition-colors">
						<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
					</button>
				</form>
			</div>

			<!-- Popular Categories -->
			<?php
			$categories = get_terms( array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => 8,
				'parent'     => 0,
			) );

			if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
			?>
				<div class="mt-10">
					<h3 class="text-base font-bold text-gray-700 text-center mb-4"><?php esc_html_e( 'Popular Categories', 'flavor' ); ?></h3>
					<div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-2xl mx-auto">
						<?php foreach ( $categories as $cat ) : ?>
							<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="text-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:bg-primary/5 transition-colors">
								<span class="text-sm font-medium text-gray-700"><?php echo esc_html( $cat->name ); ?></span>
								<span class="block text-xs text-gray-400 mt-0.5">
									<?php
									/* translators: %d: product count */
									printf( esc_html( _n( '%d product', '%d products', $cat->count, 'flavor' ) ), esc_html( $cat->count ) );
									?>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
