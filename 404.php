<?php
/**
 * 404 Page
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="max-w-site-xxl mx-auto px-3 md:px-4 py-12">

	<div class="text-center max-w-lg mx-auto mb-10">
		<p class="text-8xl font-black text-primary/20 mb-4">404</p>
		<h1 class="text-2xl font-bold text-gray-700 mb-2"><?php esc_html_e( 'Page Not Found', 'flavor' ); ?></h1>
		<p class="text-gray-500 mb-6"><?php esc_html_e( 'Sorry, the page you\'re looking for doesn\'t exist or has been moved.', 'flavor' ); ?></p>

		<!-- Search -->
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="flex gap-2 max-w-md mx-auto mb-6">
			<input type="search" name="s" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary" placeholder="<?php esc_attr_e( 'Search for products...', 'flavor' ); ?>">
			<input type="hidden" name="post_type" value="product">
			<button type="submit" class="bg-primary hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg transition-colors">
				<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
			</button>
		</form>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 bg-primary hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
			<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
			<?php esc_html_e( 'Go Home', 'flavor' ); ?>
		</a>
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
		<div>
			<h3 class="text-base font-bold text-gray-700 text-center mb-4"><?php esc_html_e( 'Popular Categories', 'flavor' ); ?></h3>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl mx-auto">
				<?php foreach ( $categories as $cat ) :
					$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
					$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'flavor-category-card' ) : wc_placeholder_img_src( 'flavor-category-card' );
				?>
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="group text-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" class="w-16 h-16 mx-auto object-contain mb-2 group-hover:scale-110 transition-transform">
						<span class="text-sm font-medium text-gray-700"><?php echo esc_html( $cat->name ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
