<?php
/**
 * 404 - Page Not Found
 *
 * @package flavor
 */

get_header(); ?>

<main class="max-w-4xl mx-auto px-4 py-16 text-center">

	<!-- Error Icon -->
	<div class="mb-8">
		<span class="text-8xl md:text-9xl font-black text-gray-200">404</span>
	</div>

	<h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3"><?php esc_html_e( 'Page Not Found', 'flavor' ); ?></h1>
	<p class="text-gray-500 mb-8 max-w-md mx-auto"><?php esc_html_e( 'The page you\'re looking for doesn\'t exist or has been moved. Try searching or browse our popular categories.', 'flavor' ); ?></p>

	<!-- Search -->
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="max-w-md mx-auto mb-12">
		<div class="flex gap-2">
			<input type="search" name="s"
				class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent"
				placeholder="<?php esc_attr_e( 'Search…', 'flavor' ); ?>">
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button type="submit" class="px-5 py-2.5 bg-[var(--color-primary,#E15726)] text-white font-medium rounded-lg hover:opacity-90 transition-opacity">
				<?php esc_html_e( 'Search', 'flavor' ); ?>
			</button>
		</div>
	</form>

	<!-- Popular Categories -->
	<?php if ( class_exists( 'WooCommerce' ) ) :
		$categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 6,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'parent'     => 0,
		) );

		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
			<div class="mb-12">
				<h2 class="text-lg font-bold mb-4"><?php esc_html_e( 'Popular Categories', 'flavor' ); ?></h2>
				<div class="flex flex-wrap justify-center gap-3">
					<?php foreach ( $categories as $cat ) :
						$thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
					?>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
							class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:border-[var(--color-primary,#E15726)] hover:text-[var(--color-primary,#E15726)] transition-colors">
							<?php if ( $thumb_id ) : ?>
								<?php echo wp_get_attachment_image( $thumb_id, array( 24, 24 ), false, array( 'class' => 'w-6 h-6 rounded-full object-cover' ) ); ?>
							<?php endif; ?>
							<?php echo esc_html( $cat->name ); ?>
							<span class="text-gray-400 text-xs">(<?php echo absint( $cat->count ); ?>)</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<!-- Go Home -->
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
		class="inline-flex items-center gap-2 bg-gray-900 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-gray-800 transition-colors">
		<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
		<?php esc_html_e( 'Go Home', 'flavor' ); ?>
	</a>

</main>

<?php get_footer(); ?>
