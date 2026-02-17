<?php
/**
 * Popular Categories — Large CTA cards
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enabled = get_theme_mod( 'flavor_popular_cats_enabled', true );
if ( ! $enabled ) {
	return;
}

$title   = get_theme_mod( 'flavor_popular_cats_title', __( 'Popular Categories', 'flavor' ) );
$cat_ids = get_theme_mod( 'flavor_popular_cats_ids', '' );

if ( $cat_ids ) {
	$cat_ids    = array_filter( array_map( 'absint', explode( ',', $cat_ids ) ) );
	$categories = array();
	foreach ( $cat_ids as $id ) {
		$term = get_term( $id, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$categories[] = $term;
		}
	}
} else {
	$categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'number'     => 5,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'exclude'    => array( get_option( 'default_product_cat' ) ),
	) );
	if ( is_wp_error( $categories ) ) {
		$categories = array();
	}
}

if ( empty( $categories ) ) {
	return;
}
?>

<section class="my-6" aria-label="<?php echo esc_attr( $title ); ?>">
	<h2 class="text-lg font-bold text-gray-700 mb-4"><?php echo esc_html( $title ); ?></h2>

	<div class="grid grid-cols-1 tablet-sm:grid-cols-2 lg:grid-cols-5 gap-3">
		<?php foreach ( $categories as $i => $cat ) :
			$thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'flavor-category-card' ) : '';
			$is_first  = $i === 0;
		?>
			<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
			   class="bg-gray-50 rounded-lg p-4 flex items-center justify-between hover:shadow-md transition-shadow group <?php echo $is_first ? 'lg:col-span-3' : 'lg:col-span-2'; ?> <?php echo $i >= 1 && $i <= 2 ? '' : ''; ?>">
				<div class="flex-1 min-w-0">
					<h3 class="text-base font-bold text-gray-700 mb-1 group-hover:text-primary transition-colors">
						<?php echo esc_html( $cat->name ); ?>
					</h3>
					<span class="inline-flex items-center text-sm text-primary font-medium">
						<?php esc_html_e( 'Buy now', 'flavor' ); ?>
						<svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
					</span>
				</div>
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>"
						 alt="<?php echo esc_attr( $cat->name ); ?>"
						 class="w-[140px] h-[140px] lg:w-[180px] lg:h-[180px] object-contain flex-shrink-0"
						 loading="lazy">
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
</section>
