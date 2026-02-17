<?php
/**
 * Special Offers — Featured deal + deal list with countdown
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enabled = get_theme_mod( 'flavor_special_offers_enabled', true );
if ( ! $enabled ) {
	return;
}

$title         = get_theme_mod( 'flavor_special_offers_title', __( 'Special Offers', 'flavor' ) );
$countdown_end = get_theme_mod( 'flavor_special_offers_countdown', '' );
?>

<section class="my-6"
		 x-data="flavorSpecialOffers()"
		 aria-label="<?php echo esc_attr( $title ); ?>">

	<!-- Header -->
	<div class="flex items-center justify-between mb-4">
		<h2 class="text-lg font-bold text-gray-700"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $countdown_end ) : ?>
			<div class="flex items-center gap-1 text-sm" x-show="timeLeft.total > 0">
				<svg class="w-4 h-4 text-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				<span class="bg-red text-white text-xs font-bold px-1.5 py-0.5 rounded" x-text="timeLeft.days + 'd'"></span>
				<span class="bg-red text-white text-xs font-bold px-1.5 py-0.5 rounded" x-text="timeLeft.hours + 'h'"></span>
				<span class="bg-red text-white text-xs font-bold px-1.5 py-0.5 rounded" x-text="timeLeft.minutes + 'm'"></span>
				<span class="bg-red text-white text-xs font-bold px-1.5 py-0.5 rounded" x-text="timeLeft.seconds + 's'"></span>
			</div>
		<?php endif; ?>
	</div>

	<!-- Content -->
	<div class="flex flex-col lg:flex-row gap-4">
		<!-- Featured Deal (left) -->
		<div class="w-full lg:w-2/5">
			<template x-if="loading">
				<div class="bg-white rounded-lg border border-gray-300 p-4 animate-pulse">
					<div class="bg-gray-200 rounded h-[220px] mb-4"></div>
					<div class="bg-gray-200 rounded h-4 w-3/4 mb-2"></div>
					<div class="bg-gray-200 rounded h-5 w-1/2"></div>
				</div>
			</template>
			<template x-if="!loading && featured">
				<div class="bg-white rounded-lg border border-gray-300 p-4 text-center">
					<div class="relative inline-block mb-3">
						<template x-if="featured.discount">
							<span class="absolute top-0 left-0 bg-red text-white text-xs font-bold px-2 py-0.5 rounded-full" x-text="'-' + featured.discount + '%'"></span>
						</template>
						<a :href="featured.url">
							<img :src="featured.image" :alt="featured.name" class="w-[220px] h-[220px] object-contain mx-auto" loading="lazy">
						</a>
					</div>
					<h3 class="text-sm font-medium text-gray-700 line-clamp-2 mb-2">
						<a :href="featured.url" x-text="featured.name" class="hover:text-primary transition-colors"></a>
					</h3>
					<div class="flex items-center justify-center gap-2 mb-3">
						<template x-if="featured.regular_price !== featured.sale_price">
							<span class="text-sm text-gray-500 line-through" x-text="featured.regular_price + '€'"></span>
						</template>
						<span class="text-lg font-bold text-primary" x-text="featured.sale_price + '€'"></span>
					</div>
					<a :href="featured.url" class="inline-block bg-primary text-white text-sm font-medium px-6 py-2 rounded-lg hover:opacity-90 transition-opacity">
						<?php esc_html_e( 'Buy Now', 'flavor' ); ?>
					</a>
				</div>
			</template>
		</div>

		<!-- Deal List (right) -->
		<div class="w-full lg:w-3/5">
			<div class="grid grid-cols-1 tablet-sm:grid-cols-2 gap-3">
				<template x-if="loading">
					<template x-for="n in 6" :key="n">
						<div class="bg-white rounded-lg border border-gray-300 p-3 flex items-center gap-3 animate-pulse">
							<div class="bg-gray-200 rounded w-16 h-16 flex-shrink-0"></div>
							<div class="flex-1">
								<div class="bg-gray-200 rounded h-3 w-3/4 mb-2"></div>
								<div class="bg-gray-200 rounded h-4 w-1/2"></div>
							</div>
						</div>
					</template>
				</template>
				<template x-if="!loading">
					<template x-for="deal in deals" :key="deal.id">
						<a :href="deal.url" class="bg-white rounded-lg border border-gray-300 p-3 flex items-center gap-3 hover:shadow-md transition-shadow">
							<div class="relative flex-shrink-0">
								<img :src="deal.image" :alt="deal.name" class="w-16 h-16 object-contain rounded" loading="lazy">
								<template x-if="deal.discount">
									<span class="absolute -top-1 -left-1 bg-red text-white text-[10px] font-bold px-1 py-0.5 rounded-full" x-text="'-' + deal.discount + '%'"></span>
								</template>
							</div>
							<div class="flex-1 min-w-0">
								<p class="text-sm text-gray-700 line-clamp-2 mb-1" x-text="deal.name"></p>
								<div class="flex items-center gap-2">
									<template x-if="deal.regular_price !== deal.sale_price">
										<span class="text-xs text-gray-500 line-through" x-text="deal.regular_price + '€'"></span>
									</template>
									<span class="text-sm font-bold text-primary" x-text="deal.sale_price + '€'"></span>
								</div>
							</div>
						</a>
					</template>
				</template>
			</div>
		</div>
	</div>
</section>

<script>
function flavorSpecialOffers() {
	const endDate = '<?php echo esc_js( $countdown_end ); ?>';
	return {
		loading: true,
		featured: null,
		deals: [],
		timeLeft: { total: 0, days: 0, hours: 0, minutes: 0, seconds: 0 },
		interval: null,
		init() {
			this.loadDeals();
			if (endDate) {
				this.updateCountdown();
				this.interval = setInterval(() => this.updateCountdown(), 1000);
			}
		},
		destroy() {
			if (this.interval) clearInterval(this.interval);
		},
		updateCountdown() {
			const diff = new Date(endDate).getTime() - Date.now();
			if (diff <= 0) {
				this.timeLeft = { total: 0, days: 0, hours: 0, minutes: 0, seconds: 0 };
				if (this.interval) clearInterval(this.interval);
				return;
			}
			this.timeLeft = {
				total: diff,
				days: Math.floor(diff / 86400000),
				hours: Math.floor((diff % 86400000) / 3600000),
				minutes: Math.floor((diff % 3600000) / 60000),
				seconds: Math.floor((diff % 60000) / 1000),
			};
		},
		loadDeals() {
			this.loading = true;
			const data = new FormData();
			data.append('action', 'flavor_load_products');
			data.append('nonce', flavorData.nonce);
			data.append('context', 'special_offers');
			data.append('per_page', 7);

			fetch(flavorData.ajaxUrl, { method: 'POST', body: data })
				.then(r => r.json())
				.then(res => {
					if (res.success && res.data.products) {
						const products = res.data.products;
						this.featured = products.length > 0 ? products[0] : null;
						this.deals = products.slice(1, 7);
					}
					this.loading = false;
				})
				.catch(() => { this.loading = false; });
		}
	};
}
</script>
