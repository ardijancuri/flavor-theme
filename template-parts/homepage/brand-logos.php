<?php
/**
 * Brand Logos — Horizontal scrollable row
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enabled = get_theme_mod( 'flavor_brands_enabled', true );
if ( ! $enabled ) {
	return;
}

$logos = array();
for ( $i = 1; $i <= 12; $i++ ) {
	$logo_url  = get_theme_mod( "flavor_brand_logo_{$i}_image", '' );
	$logo_name = get_theme_mod( "flavor_brand_logo_{$i}_name", '' );
	$logo_link = get_theme_mod( "flavor_brand_logo_{$i}_link", '' );
	if ( $logo_url ) {
		$logos[] = array(
			'url'  => $logo_url,
			'name' => $logo_name,
			'link' => $logo_link,
		);
	}
}

if ( empty( $logos ) ) {
	return;
}
?>

<section class="my-6 mb-8" aria-label="<?php esc_attr_e( 'Our Brands', 'flavor' ); ?>">
	<div class="bg-white border border-gray-300 rounded-lg h-16 flex items-center overflow-x-auto scrollbar-hide px-4 gap-6">
		<?php foreach ( $logos as $logo ) : ?>
			<?php if ( $logo['link'] ) : ?>
				<a href="<?php echo esc_url( $logo['link'] ); ?>" class="flex-shrink-0 hover:shadow-md rounded p-1 transition-shadow">
			<?php else : ?>
				<span class="flex-shrink-0 hover:shadow-md rounded p-1 transition-shadow">
			<?php endif; ?>
				<img src="<?php echo esc_url( $logo['url'] ); ?>"
					 alt="<?php echo esc_attr( $logo['name'] ); ?>"
					 class="w-[100px] h-[40px] object-contain"
					 loading="lazy">
			<?php echo $logo['link'] ? '</a>' : '</span>'; ?>
		<?php endforeach; ?>
	</div>
</section>
