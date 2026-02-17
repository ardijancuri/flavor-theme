<?php
/**
 * Mini Cart — Slide-out panel
 *
 * @package flavor
 */

defined( 'ABSPATH' ) || exit;
?>

<div x-data="{ open: false }"
	 x-on:open-mini-cart.window="open = true"
	 x-on:keydown.escape.window="open = false"
	 class="relative z-50">

	<!-- Overlay -->
	<div x-show="open" x-cloak
		 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
		 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
		 @click="open = false"
		 class="fixed inset-0 bg-black/40"></div>

	<!-- Panel -->
	<div x-show="open" x-cloak
		 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
		 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
		 class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col">

		<!-- Header -->
		<div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
			<h2 class="text-lg font-bold"><?php esc_html_e( 'Your Cart', 'flavor' ); ?>
				<span class="text-sm font-normal text-gray-500 ml-1">(<span class="mini-cart-count"><?php echo absint( WC()->cart->get_cart_contents_count() ); ?></span>)</span>
			</h2>
			<button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="<?php esc_attr_e( 'Close', 'flavor' ); ?>">
				<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
			</button>
		</div>

		<!-- Items -->
		<div class="flex-1 overflow-y-auto px-6 py-4 mini-cart-items">
			<?php if ( WC()->cart->is_empty() ) : ?>
				<div class="flex flex-col items-center justify-center h-full text-center">
					<svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
					</svg>
					<p class="text-gray-500"><?php esc_html_e( 'Your cart is empty', 'flavor' ); ?></p>
				</div>
			<?php else : ?>
				<ul class="space-y-4">
					<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
						$_product   = $cart_item['data'];
						$product_id = $cart_item['product_id'];
						if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) continue;
					?>
						<li class="flex gap-3 pb-4 border-b border-gray-100" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
							<div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
								<?php echo $_product->get_image( array( 64, 64 ), array( 'class' => 'w-full h-full object-cover' ) ); // phpcs:ignore ?>
							</div>
							<div class="flex-1 min-w-0">
								<h4 class="text-sm font-medium text-gray-900 truncate">
									<a href="<?php echo esc_url( $_product->get_permalink() ); ?>" class="hover:text-[var(--color-primary,#E15726)]">
										<?php echo esc_html( $_product->get_name() ); ?>
									</a>
								</h4>
								<p class="text-sm text-gray-500 mt-0.5"><?php echo WC()->cart->get_product_price( $_product ); // phpcs:ignore ?></p>

								<div class="flex items-center justify-between mt-2">
									<div class="flex items-center border border-gray-200 rounded-md overflow-hidden">
										<button type="button" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-50"
											onclick="flavorMiniCartQty('<?php echo esc_js( $cart_item_key ); ?>', <?php echo max( 0, $cart_item['quantity'] - 1 ); ?>)">
											<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
										</button>
										<span class="w-8 text-center text-xs font-medium"><?php echo absint( $cart_item['quantity'] ); ?></span>
										<button type="button" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-50"
											onclick="flavorMiniCartQty('<?php echo esc_js( $cart_item_key ); ?>', <?php echo $cart_item['quantity'] + 1; ?>)">
											<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
										</button>
									</div>

									<button type="button" class="text-gray-400 hover:text-red-500 transition-colors"
										onclick="flavorMiniCartRemove('<?php echo esc_js( $cart_item_key ); ?>')"
										aria-label="<?php esc_attr_e( 'Remove', 'flavor' ); ?>">
										<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
									</button>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<!-- Footer -->
		<?php if ( ! WC()->cart->is_empty() ) : ?>
		<div class="border-t border-gray-200 px-6 py-4 space-y-3">
			<div class="flex justify-between text-sm">
				<span class="text-gray-600"><?php esc_html_e( 'Subtotal', 'flavor' ); ?></span>
				<span class="font-bold mini-cart-subtotal"><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"
				class="block w-full text-center border-2 border-gray-900 text-gray-900 font-semibold py-3 rounded-xl hover:bg-gray-900 hover:text-white transition-colors">
				<?php esc_html_e( 'View Cart', 'flavor' ); ?>
			</a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
				class="block w-full text-center bg-[var(--color-primary,#E15726)] text-white font-semibold py-3 rounded-xl hover:opacity-90 transition-opacity">
				<?php esc_html_e( 'Checkout', 'flavor' ); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
function flavorMiniCartRemove(cartKey) {
	const fd = new FormData();
	fd.append('action', 'flavor_mini_cart_remove');
	fd.append('cart_key', cartKey);
	fd.append('nonce', (window.flavorData || {}).nonce || '');
	fetch((window.flavorData || {}).ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
		.then(r => r.json())
		.then(res => { if (res.success) location.reload(); });
}
function flavorMiniCartQty(cartKey, qty) {
	if (qty < 1) { flavorMiniCartRemove(cartKey); return; }
	const fd = new FormData();
	fd.append('action', 'flavor_mini_cart_qty');
	fd.append('cart_key', cartKey);
	fd.append('quantity', qty);
	fd.append('nonce', (window.flavorData || {}).nonce || '');
	fetch((window.flavorData || {}).ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
		.then(r => r.json())
		.then(res => { if (res.success) location.reload(); });
}
</script>
