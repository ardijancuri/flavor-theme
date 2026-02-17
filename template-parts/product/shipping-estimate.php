<?php
/**
 * Shipping estimate — delivery dates by zone
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zones_raw = get_theme_mod( 'flavor_shipping_zones', '' );
$zones     = array();

if ( $zones_raw ) {
	$zones = json_decode( $zones_raw, true );
}

if ( empty( $zones ) || ! is_array( $zones ) ) {
	$zones = array(
		array( 'name' => __( 'Metro', 'flavor' ),    'days' => 2 ),
		array( 'name' => __( 'Regional', 'flavor' ), 'days' => 4 ),
		array( 'name' => __( 'Remote', 'flavor' ),   'days' => 7 ),
	);
}
?>

<div class="border border-gray-200 rounded-lg p-4 mt-4" x-data="{ zone: 0 }">
	<div class="flex items-center gap-2 mb-3">
		<svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
		<span class="text-sm font-medium text-gray-900"><?php esc_html_e( 'Delivery Estimate', 'flavor' ); ?></span>
	</div>

	<select
		x-model="zone"
		class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 mb-3 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
	>
		<?php foreach ( $zones as $i => $z ) : ?>
			<option value="<?php echo absint( $i ); ?>"><?php echo esc_html( $z['name'] ); ?></option>
		<?php endforeach; ?>
	</select>

	<?php foreach ( $zones as $i => $z ) :
		$from = gmdate( 'M j', strtotime( '+' . max( 1, (int) $z['days'] - 1 ) . ' days' ) );
		$to   = gmdate( 'M j', strtotime( '+' . (int) $z['days'] . ' days' ) );
	?>
		<div x-show="zone == <?php echo absint( $i ); ?>" class="flex items-center gap-2 text-sm">
			<svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
			<span class="text-gray-700">
				<?php printf( esc_html__( 'Estimated delivery: %1$s – %2$s', 'flavor' ), esc_html( $from ), esc_html( $to ) ); ?>
			</span>
		</div>
	<?php endforeach; ?>
</div>
