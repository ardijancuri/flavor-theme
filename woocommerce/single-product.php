<?php
/**
 * Single Product template override
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>
<script>
function productPage(config) {
    return {
        productId: config.productId,
        price: config.price,
        regularPrice: config.regularPrice,
        inStock: config.inStock,
        warrantyPrice: config.warrantyPrice,
        qty: 1,
        adding: false,
        wishlisted: false,
        warranty: false,

        init() {
            this.$watch('qty', v => { if (v < 1) this.qty = 1; });
            this.$el.addEventListener('qty-change', e => {
                this.qty = Math.max(1, this.qty + e.detail);
            });
        },

        addToCart() {
            if (this.adding) return;
            this.adding = true;

            const data = new FormData();
            data.append('action', 'flavor_add_to_cart');
            data.append('nonce', (window.flavorData || {}).nonce || '');
            data.append('product_id', this.productId);
            data.append('quantity', this.qty);
            data.append('warranty', this.warranty ? '1' : '0');

            fetch((window.flavorData || {}).ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: data })
                .then(r => r.json())
                .then(res => {
                    this.adding = false;
                    if (res.success) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: '<?php esc_attr_e( 'Added to cart!', 'flavor' ); ?>', type: 'success' } }));
                        const countEl = document.querySelector('.cart-count');
                        if (countEl && res.data.cart_count) {
                            countEl.textContent = res.data.cart_count;
                            countEl.classList.remove('hidden');
                        }
                        document.dispatchEvent(new Event('added_to_cart'));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: res.data?.message || '<?php esc_attr_e( 'Error adding to cart', 'flavor' ); ?>', type: 'error' } }));
                    }
                })
                .catch(() => {
                    this.adding = false;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: '<?php esc_attr_e( 'Error adding to cart', 'flavor' ); ?>', type: 'error' } }));
                });
        },

        buyNow() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo esc_url( get_permalink() ); ?>';

            const fields = {
                'add-to-cart': this.productId,
                'quantity': this.qty,
                'flavor_redirect_checkout': '1'
            };
            for (const [k, v] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = k;
                input.value = v;
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
        },

        toggleWishlist() {
            this.wishlisted = !this.wishlisted;
            const data = new FormData();
            data.append('action', 'flavor_toggle_wishlist');
            data.append('nonce', (window.flavorData || {}).nonce || '');
            data.append('product_id', this.productId);
            fetch((window.flavorData || {}).ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: data }).catch(() => {});
        }
    };
}

function mobileStickyBar() {
    return {
        visible: false,
        init() {
            const cta = document.getElementById('flavor-main-cta');
            if (!cta) return;
            const observer = new IntersectionObserver(([entry]) => {
                this.visible = !entry.isIntersecting;
            }, { threshold: 0 });
            observer.observe(cta);
        }
    };
}
</script>
<?php

while ( have_posts() ) :
	the_post();

	global $product;
?>

<div class="max-w-site-xxl mx-auto px-3 md:px-4" x-data="productPage(<?php echo esc_attr( wp_json_encode( array(
	'productId'      => $product->get_id(),
	'price'          => (float) $product->get_price(),
	'regularPrice'   => (float) $product->get_regular_price(),
	'inStock'        => $product->is_in_stock(),
	'warrantyPrice'  => (float) get_post_meta( $product->get_id(), '_flavor_warranty_price', true ),
) ) ); ?>)">

	<?php get_template_part( 'template-parts/global/breadcrumbs' ); ?>

	<!-- Main product section -->
	<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 py-4">
		<div>
			<?php get_template_part( 'template-parts/product/product-gallery' ); ?>
		</div>
		<div>
			<?php get_template_part( 'template-parts/product/product-info' ); ?>

			<?php if ( get_theme_mod( 'flavor_installment_enabled', true ) ) : ?>
				<?php get_template_part( 'template-parts/product/installment-display' ); ?>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'flavor_warranty_enabled', true ) ) : ?>
				<?php get_template_part( 'template-parts/product/warranty-upsell' ); ?>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/product/shipping-estimate' ); ?>
		</div>
	</div>

	<?php get_template_part( 'template-parts/product/product-tabs' ); ?>
	<?php get_template_part( 'template-parts/product/cross-sells' ); ?>

</div>

<?php get_template_part( 'template-parts/product/buy-now' ); ?>

<!-- Mobile sticky bar -->
<div
	x-data="mobileStickyBar()"
	x-show="visible"
	x-transition:enter="transition-transform ease-out duration-200"
	x-transition:enter-start="translate-y-full"
	x-transition:enter-end="translate-y-0"
	x-transition:leave="transition-transform ease-in duration-150"
	x-transition:leave-start="translate-y-0"
	x-transition:leave-end="translate-y-full"
	class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 shadow-lg z-40 px-4 py-3 md:hidden"
	style="display: none;"
>
	<div class="flex items-center justify-between gap-3">
		<div>
			<span class="text-lg font-bold"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
		</div>
		<button
			@click="$dispatch('add-to-cart', { id: <?php echo absint( $product->get_id() ); ?>, qty: 1 })"
			class="bg-primary text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors whitespace-nowrap"
		>
			<?php esc_html_e( 'Add to Cart', 'flavor' ); ?>
		</button>
	</div>
</div>

<?php
endwhile;

get_footer();
