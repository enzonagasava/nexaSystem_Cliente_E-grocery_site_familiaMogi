import { defineStore } from 'pinia';

const STORAGE_KEY = 'familiaMogiCart';

export const useCartStore = defineStore('cart', {
  state: () => ({
    cart: [],
    hydrated: false,
  }),
  getters: {
    cartCount: (state) => state.cart.reduce((sum, item) => sum + item.quantity, 0),
    subtotal: (state) => state.cart.reduce((sum, item) => sum + (item.product.price * item.quantity), 0),
    shipping() {
      if (this.subtotal === 0) return 0;
      return this.subtotal >= 80 ? 0 : 8.9;
    },
    total() {
      return this.subtotal + this.shipping;
    },
  },
  actions: {
    hydrate() {
      if (this.hydrated) return;

      try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (raw) {
          const parsed = JSON.parse(raw);
          if (Array.isArray(parsed)) {
            this.cart = parsed;
          }
        }
      } catch (error) {
        console.error(error);
      } finally {
        this.hydrated = true;
      }
    },
    persist() {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify(this.cart));
    },
    addItem(product, quantity = 1) {
      const qty = Math.max(1, Number(quantity) || 1);
      const existing = this.cart.find((item) => item.product.id === product.id);

      if (existing) {
        existing.quantity += qty;
      } else {
        this.cart.push({ product, quantity: qty });
      }

      this.persist();
    },
    updateQuantity(productId, delta) {
      this.cart = this.cart
        .map((item) => item.product.id === productId ? { ...item, quantity: item.quantity + delta } : item)
        .filter((item) => item.quantity > 0);

      this.persist();
    },
    removeItem(productId) {
      this.cart = this.cart.filter((item) => item.product.id !== productId);
      this.persist();
    },
    clear() {
      this.cart = [];
      this.persist();
    },
  },
});
