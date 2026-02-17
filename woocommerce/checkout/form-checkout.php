<?php
/**
 * Checkout Form — One-Page Accordion
 *
 * @package flavor
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'flavor' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout max-w-5xl mx-auto px-4 py-8"
	  action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
	  enctype="multipart/form-data"
	  x-data="flavorCheckout()">

	<h1 class="text-2xl md:text-3xl font-bold mb-8"><?php esc_html_e( 'Checkout', 'flavor' ); ?></h1>

	<div class="lg:grid lg:grid-cols-3 lg:gap-8">
		<div class="lg:col-span-2 space-y-4">

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<!-- Step 1: Address -->
			<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
				<button type="button"
					class="w-full flex items-center justify-between px-6 py-4 text-left"
					@click="step = step === 1 ? 0 : 1">
					<div class="flex items-center gap-3">
						<span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
							  :class="step >= 1 ? 'bg-[var(--color-primary,#E15726)] text-white' : 'bg-gray-200 text-gray-600'">1</span>
						<span class="font-semibold text-gray-900"><?php esc_html_e( 'Shipping Address', 'flavor' ); ?></span>
					</div>
					<svg class="w-5 h-5 text-gray-400 transition-transform" :class="step === 1 && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
				</button>

				<div x-show="step === 1" x-collapse x-cloak class="px-6 pb-6">
					<?php if ( is_user_logged_in() ) : ?>
						<!-- Saved Addresses -->
						<?php
						$customer   = WC()->customer;
						$saved_addr = $customer->get_shipping_address_1();
						if ( $saved_addr ) : ?>
							<div class="mb-4 p-4 border-2 border-[var(--color-primary,#E15726)] rounded-xl bg-orange-50 cursor-pointer"
								 @click="useSavedAddress = true">
								<div class="flex items-center gap-2 mb-1">
									<svg class="w-4 h-4 text-[var(--color-primary,#E15726)]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
									<span class="text-sm font-medium text-[var(--color-primary,#E15726)]"><?php esc_html_e( 'Saved Address', 'flavor' ); ?></span>
								</div>
								<p class="text-sm text-gray-700">
									<?php echo esc_html( $customer->get_shipping_first_name() . ' ' . $customer->get_shipping_last_name() ); ?><br>
									<?php echo esc_html( $saved_addr ); ?><br>
									<?php echo esc_html( $customer->get_shipping_postcode() . ' ' . $customer->get_shipping_city() ); ?>
								</p>
							</div>
							<button type="button" class="text-sm text-[var(--color-primary,#E15726)] font-medium mb-4"
								@click="useSavedAddress = false">
								<?php esc_html_e( '+ Use a different address', 'flavor' ); ?>
							</button>
						<?php endif; ?>
					<?php endif; ?>

					<!-- Address Fields -->
					<div x-show="!useSavedAddress || <?php echo is_user_logged_in() ? 'false' : 'true'; ?>">
						<div id="customer_details">
							<div class="woocommerce-billing-fields">
								<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

								<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
									<?php
									$fields = $checkout->get_checkout_fields( 'billing' );
									foreach ( $fields as $key => $field ) {
										$field['class']       = array( 'col-span-1' );
										$field['input_class'] = array( 'w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent' );
										$field['label_class'] = array( 'block text-sm font-medium text-gray-700 mb-1' );

										if ( in_array( $key, array( 'billing_address_1', 'billing_email' ), true ) ) {
											$field['class'] = array( 'sm:col-span-2' );
										}

										woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
									}
									?>
								</div>

								<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
							</div>

							<!-- Business Invoicing Toggle -->
							<div class="mt-4" x-data="{ business: false }">
								<label class="inline-flex items-center gap-2 cursor-pointer">
									<input type="checkbox" x-model="business" class="rounded border-gray-300 text-[var(--color-primary,#E15726)] focus:ring-[var(--color-primary,#E15726)]">
									<span class="text-sm font-medium text-gray-700"><?php esc_html_e( 'I need a business invoice', 'flavor' ); ?></span>
								</label>
								<div x-show="business" x-collapse x-cloak class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Company Name', 'flavor' ); ?></label>
										<input type="text" name="billing_company" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent">
									</div>
									<div>
										<label class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'VAT Number', 'flavor' ); ?></label>
										<input type="text" name="billing_vat" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent">
									</div>
								</div>
							</div>

							<?php if ( WC()->cart->needs_shipping() ) : ?>
								<div class="mt-4">
									<label class="inline-flex items-center gap-2 cursor-pointer">
										<input type="checkbox" name="ship_to_different_address" value="1"
											x-model="shipDifferent"
											class="rounded border-gray-300 text-[var(--color-primary,#E15726)] focus:ring-[var(--color-primary,#E15726)]">
										<span class="text-sm font-medium text-gray-700"><?php esc_html_e( 'Ship to a different address?', 'flavor' ); ?></span>
									</label>

									<div x-show="shipDifferent" x-collapse x-cloak class="mt-3">
										<div class="woocommerce-shipping-fields">
											<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>
											<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
												<?php
												$shipping_fields = $checkout->get_checkout_fields( 'shipping' );
												foreach ( $shipping_fields as $key => $field ) {
													$field['input_class'] = array( 'w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent' );
													$field['label_class'] = array( 'block text-sm font-medium text-gray-700 mb-1' );
													woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
												}
												?>
											</div>
											<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<button type="button"
						class="mt-4 bg-[var(--color-primary,#E15726)] text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm"
						@click="validateStep(1) && (step = 2)">
						<?php esc_html_e( 'Continue to Payment', 'flavor' ); ?>
					</button>
				</div>
			</div>

			<!-- Step 2: Payment -->
			<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
				<button type="button"
					class="w-full flex items-center justify-between px-6 py-4 text-left"
					@click="step = step === 2 ? 0 : 2">
					<div class="flex items-center gap-3">
						<span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
							  :class="step >= 2 ? 'bg-[var(--color-primary,#E15726)] text-white' : 'bg-gray-200 text-gray-600'">2</span>
						<span class="font-semibold text-gray-900"><?php esc_html_e( 'Payment Method', 'flavor' ); ?></span>
					</div>
					<svg class="w-5 h-5 text-gray-400 transition-transform" :class="step === 2 && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
				</button>

				<div x-show="step === 2" x-collapse x-cloak class="px-6 pb-6">
					<div id="payment" class="woocommerce-checkout-payment">
						<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

						<ul class="wc_payment_methods payment_methods methods space-y-3">
							<?php
							$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
							if ( $available_gateways ) :
								$current_gateway = current( array_keys( $available_gateways ) );
								foreach ( $available_gateways as $gateway ) :
							?>
								<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
									<label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-colors"
										:class="paymentMethod === '<?php echo esc_js( $gateway->id ); ?>' ? 'border-[var(--color-primary,#E15726)] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
										<input type="radio" name="payment_method"
											value="<?php echo esc_attr( $gateway->id ); ?>"
											<?php checked( $gateway->chosen, true ); ?>
											x-model="paymentMethod"
											class="text-[var(--color-primary,#E15726)] focus:ring-[var(--color-primary,#E15726)]"
											id="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
										<?php if ( $gateway->icon ) : ?>
											<span class="flex-shrink-0"><?php echo $gateway->icon; // phpcs:ignore ?></span>
										<?php endif; ?>
										<span class="font-medium text-sm"><?php echo esc_html( $gateway->get_title() ); ?></span>
									</label>
									<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
										<div x-show="paymentMethod === '<?php echo esc_js( $gateway->id ); ?>'" x-collapse x-cloak
											class="mt-2 ml-10 p-4 bg-gray-50 rounded-lg text-sm payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>">
											<?php $gateway->payment_fields(); ?>
										</div>
									<?php endif; ?>
								</li>
							<?php endforeach; endif; ?>
						</ul>

						<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
					</div>

					<button type="button"
						class="mt-4 bg-[var(--color-primary,#E15726)] text-white font-semibold px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm"
						@click="step = 3">
						<?php esc_html_e( 'Review Order', 'flavor' ); ?>
					</button>
				</div>
			</div>

			<!-- Step 3: Review & Place Order -->
			<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
				<button type="button"
					class="w-full flex items-center justify-between px-6 py-4 text-left"
					@click="step = step === 3 ? 0 : 3">
					<div class="flex items-center gap-3">
						<span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
							  :class="step >= 3 ? 'bg-[var(--color-primary,#E15726)] text-white' : 'bg-gray-200 text-gray-600'">3</span>
						<span class="font-semibold text-gray-900"><?php esc_html_e( 'Review & Place Order', 'flavor' ); ?></span>
					</div>
					<svg class="w-5 h-5 text-gray-400 transition-transform" :class="step === 3 && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
				</button>

				<div x-show="step === 3" x-collapse x-cloak class="px-6 pb-6">
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

					<!-- Order Items Summary -->
					<div class="divide-y divide-gray-100 mb-4">
						<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
							$_product = $cart_item['data'];
						?>
							<div class="flex items-center gap-3 py-3">
								<div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
									<?php echo $_product->get_image( array( 48, 48 ), array( 'class' => 'w-full h-full object-cover' ) ); // phpcs:ignore ?>
								</div>
								<div class="flex-1 min-w-0">
									<p class="text-sm font-medium truncate"><?php echo esc_html( $_product->get_name() ); ?></p>
									<p class="text-xs text-gray-500"><?php esc_html_e( 'Qty:', 'flavor' ); ?> <?php echo absint( $cart_item['quantity'] ); ?></p>
								</div>
								<span class="text-sm font-medium"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); // phpcs:ignore ?></span>
							</div>
						<?php endforeach; ?>
					</div>

					<!-- Order Notes -->
					<div class="mb-4">
						<label for="order_comments" class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Order notes (optional)', 'flavor' ); ?></label>
						<textarea name="order_comments" id="order_comments" rows="3"
							class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent"
							placeholder="<?php esc_attr_e( 'Notes about your order, e.g. special delivery instructions.', 'flavor' ); ?>"><?php echo esc_textarea( $checkout->get_value( 'order_comments' ) ); ?></textarea>
					</div>

					<!-- Promo Code -->
					<div class="mb-4" x-data="{ showPromo: false }">
						<button type="button" @click="showPromo = !showPromo" class="text-sm text-[var(--color-primary,#E15726)] font-medium">
							<?php esc_html_e( 'Have a promo code?', 'flavor' ); ?>
						</button>
						<div x-show="showPromo" x-collapse x-cloak class="mt-2 flex gap-2">
							<input type="text" name="coupon_code_checkout" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[var(--color-primary,#E15726)] focus:border-transparent"
								placeholder="<?php esc_attr_e( 'Promo code', 'flavor' ); ?>">
							<button type="button" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
								<?php esc_html_e( 'Apply', 'flavor' ); ?>
							</button>
						</div>
					</div>

					<!-- Terms -->
					<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

					<div class="woocommerce-terms-and-conditions-wrapper mb-4">
						<?php wc_get_template( 'checkout/terms.php' ); ?>
					</div>

					<?php echo wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

					<button type="submit" class="w-full bg-[var(--color-primary,#E15726)] text-white font-bold py-4 rounded-xl hover:opacity-90 transition-opacity text-base"
						name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e( 'Place Order', 'flavor' ); ?>"
						:disabled="placing" :class="{ 'opacity-50 cursor-not-allowed': placing }">
						<span x-show="!placing"><?php esc_html_e( 'Place Order', 'flavor' ); ?></span>
						<span x-show="placing" x-cloak class="inline-flex items-center gap-2">
							<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
							<?php esc_html_e( 'Processing…', 'flavor' ); ?>
						</span>
					</button>

					<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		</div>

		<!-- Order Summary Sidebar -->
		<div class="mt-8 lg:mt-0">
			<div class="bg-gray-50 rounded-xl p-6 sticky top-24">
				<h2 class="text-lg font-bold mb-4"><?php esc_html_e( 'Order Summary', 'flavor' ); ?></h2>
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
		</div>
	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
