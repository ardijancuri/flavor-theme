/**
 * Alpine.js component registrations — loaded BEFORE Alpine.js
 *
 * @package Flavor
 */

document.addEventListener('alpine:init', () => {
  'use strict';

  const ajaxUrl = (window.flavorData || {}).ajaxUrl || (window.flavorAjax || {}).url || '/wp-admin/admin-ajax.php';
  const nonce   = (window.flavorData || {}).nonce   || (window.flavorAjax || {}).nonce || '';

  function post(action, body = {}) {
    const fd = new FormData();
    fd.append('action', action);
    fd.append('nonce', nonce);
    Object.entries(body).forEach(([k, v]) => fd.append(k, v));
    return fetch(ajaxUrl, { method: 'POST', body: fd }).then(r => r.json());
  }

  /* ── Tabbed Products (Homepage "Recommended for you") ──── */

  Alpine.data('flavorTabbedProducts', (opts = {}) => ({
    activeTab: opts.initialTab || 'for-you',
    activeCat: opts.initialCat || 0,
    loading: true,
    productsHtml: '',

    init() {
      this.loadProducts();
    },

    switchTab(slug, cat) {
      if (this.activeTab === slug) return;
      this.activeTab = slug;
      this.activeCat = cat;
      this.loadProducts();
    },

    loadProducts() {
      this.loading = true;
      post('flavor_load_products', {
        category: this.activeCat,
        per_page: 10,
        context: 'tabbed',
      }).then(res => {
        if (res.success) {
          this.productsHtml = res.data.html || '';
        }
        this.loading = false;
      }).catch(() => {
        this.loading = false;
      });
    },
  }));

  /* ── Toast Notifications ─────────────────────────────────── */

  Alpine.data('flavorToasts', () => ({
    toasts: [],

    addToast(detail) {
      const id = Date.now();
      this.toasts.push({
        id,
        message: detail.message,
        type: detail.type || 'success',
      });
      setTimeout(() => this.removeToast(id), 4000);
    },

    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },
  }));

  /* ── Cart Page Component ─────────────────────────────────── */

  Alpine.data('flavorCart', () => ({
    couponCode: '',
    couponMessage: '',
    couponSuccess: false,
    applying: false,
    cartEmpty: false,

    removeItem(key) {
      const row = document.querySelector(`[data-cart-key="${key}"]`);
      if (row) {
        row.style.opacity = '0.4';
        row.style.transition = 'opacity .3s';
      }
      post('flavor_remove_cart_item', { cart_item_key: key }).then(res => {
        if (res.success) {
          if (row) row.remove();
          if (res.data && res.data.cart_empty) {
            this.cartEmpty = true;
            location.reload();
          } else {
            location.reload();
          }
        } else {
          if (row) row.style.opacity = '1';
        }
      });
    },

    updateQty(key, qty) {
      post('flavor_update_cart_quantity', { cart_item_key: key, quantity: qty }).then(() => {
        location.reload();
      });
    },

    toggleWarranty(key, val) {
      post('flavor_toggle_warranty', { cart_item_key: key, warranty: val ? 'yes' : 'no' }).then(() => {
        location.reload();
      });
    },

    applyCoupon() {
      if (!this.couponCode || this.applying) return;
      this.applying = true;
      this.couponMessage = '';

      post('flavor_apply_coupon', { coupon_code: this.couponCode }).then(res => {
        this.applying = false;
        if (res.success) {
          this.couponSuccess = true;
          this.couponMessage = res.data.message || 'Coupon applied!';
          setTimeout(() => location.reload(), 800);
        } else {
          this.couponSuccess = false;
          this.couponMessage = (res.data && res.data.message) || 'Invalid coupon.';
        }
      }).catch(() => {
        this.applying = false;
        this.couponSuccess = false;
        this.couponMessage = 'Something went wrong.';
      });
    },
  }));

  /* ── Product Page Component ──────────────────────────────── */

  Alpine.data('productPage', (config = {}) => ({
    productId: config.productId || 0,
    quantity: 1,
    warranty: false,
    price: config.price || 0,
    regularPrice: config.regularPrice || 0,
    inStock: config.inStock !== false,
    warrantyPrice: config.warrantyPrice || 0,
    addingToCart: false,

    get displayPrice() {
      const base = this.price || this.regularPrice;
      return this.warranty ? base + this.warrantyPrice : base;
    },

    setQty(val) {
      this.quantity = Math.max(1, parseInt(val) || 1);
    },

    addToCart(redirect) {
      if (this.addingToCart) return;
      this.addingToCart = true;

      post('flavor_add_to_cart', {
        product_id: this.productId,
        quantity: this.quantity,
        warranty: this.warranty ? '1' : '0',
      }).then(res => {
        this.addingToCart = false;
        if (res.success) {
          if (redirect) {
            window.location.href = (window.flavorData || {}).checkoutUrl || '/checkout/';
          } else {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Added to cart!', type: 'success' } }));
            window.dispatchEvent(new CustomEvent('open-mini-cart'));
          }
        } else {
          window.dispatchEvent(new CustomEvent('toast', { detail: { message: (res.data && res.data.message) || 'Error', type: 'error' } }));
        }
      }).catch(() => {
        this.addingToCart = false;
      });
    },
  }));

  /* ── Shop / Archive Page Component ───────────────────────── */

  Alpine.data('shopPage', () => ({
    view: 'grid',
    quickFilter: '',
    sortBy: 'default',
    filterDrawerOpen: false,
    loading: false,
    hasMore: false,
    currentPage: 1,
    totalProducts: 0,
    brandSearch: '',
    showAll: false,
    filters: {
      price_min: '',
      price_max: '',
      categories: [],
      brands: [],
      in_stock: false,
      out_of_stock: false,
      on_sale: false,
      attributes: {},
    },
    get activeFilterCount() {
      let count = 0;
      if (this.filters.price_min || this.filters.price_max) count++;
      count += this.filters.categories.length;
      count += this.filters.brands.length;
      if (this.filters.in_stock) count++;
      if (this.filters.out_of_stock) count++;
      if (this.filters.on_sale) count++;
      return count;
    },
    get activeFilters() {
      const chips = [];
      if (this.filters.price_min || this.filters.price_max) {
        chips.push({ key: 'price', label: (this.filters.price_min || '0') + ' - ' + (this.filters.price_max || '∞'), remove: () => { this.filters.price_min = ''; this.filters.price_max = ''; } });
      }
      this.filters.categories.forEach(c => {
        chips.push({ key: 'cat-' + c, label: c, remove: () => { this.filters.categories = this.filters.categories.filter(x => x !== c); } });
      });
      this.filters.brands.forEach(b => {
        chips.push({ key: 'brand-' + b, label: b, remove: () => { this.filters.brands = this.filters.brands.filter(x => x !== b); } });
      });
      if (this.filters.in_stock) chips.push({ key: 'in_stock', label: 'In Stock', remove: () => { this.filters.in_stock = false; } });
      if (this.filters.out_of_stock) chips.push({ key: 'out_of_stock', label: 'Out of Stock', remove: () => { this.filters.out_of_stock = false; } });
      if (this.filters.on_sale) chips.push({ key: 'on_sale', label: 'On Sale', remove: () => { this.filters.on_sale = false; } });
      return chips;
    },
    clearAllFilters() {
      this.filters.price_min = '';
      this.filters.price_max = '';
      this.filters.categories = [];
      this.filters.brands = [];
      this.filters.in_stock = false;
      this.filters.out_of_stock = false;
      this.filters.on_sale = false;
      this.filters.attributes = {};
      this.quickFilter = '';
    },
    toggleArrayFilter(key, value) {
      const arr = this.filters[key];
      const idx = arr.indexOf(value);
      if (idx === -1) { arr.push(value); } else { arr.splice(idx, 1); }
    },
    toggleAttributeFilter(taxonomy, value) {
      if (!this.filters.attributes[taxonomy]) this.filters.attributes[taxonomy] = [];
      const arr = this.filters.attributes[taxonomy];
      const idx = arr.indexOf(value);
      if (idx === -1) { arr.push(value); } else { arr.splice(idx, 1); }
    },
    applyFilters() {
      // Placeholder — filters are reactive via Alpine bindings
      // Future: AJAX product reload based on filter state
    },
  }));

  /* ── Mobile Sticky Bar (Single Product) ──────────────────── */

  Alpine.data('mobileStickyBar', () => ({
    visible: false,
    init() {
      const mainCTA = document.querySelector('.js-product-add-to-cart');
      if (!mainCTA) return;
      const observer = new IntersectionObserver(
        ([entry]) => { this.visible = !entry.isIntersecting; },
        { threshold: 0 }
      );
      observer.observe(mainCTA);
    },
  }));

  /* ── Global Wishlist (localStorage) ──────────────────────── */

  Alpine.store('wishlist', {
    items: JSON.parse(localStorage.getItem('flavor_wishlist') || '[]'),
    has(id) {
      return this.items.includes(id);
    },
    toggle(id) {
      const idx = this.items.indexOf(id);
      if (idx === -1) {
        this.items.push(id);
      } else {
        this.items.splice(idx, 1);
      }
      localStorage.setItem('flavor_wishlist', JSON.stringify(this.items));
    },
  });
});
