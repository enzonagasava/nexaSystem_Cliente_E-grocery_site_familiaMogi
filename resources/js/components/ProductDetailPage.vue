<template>
  <main class="min-h-screen bg-[#f7f6ef] text-[#2b2b2b]">
    <SiteHeader :brand="brand" :cart-count="cartCount" @open-cart="onOpenCart" />

    <section class="relative overflow-hidden border-b border-[#d9dfcf]">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(47,156,68,0.16),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(225,179,0,0.18),_transparent_30%),linear-gradient(180deg,#f7f6ef_0%,#eef5e8_100%)]" />

      <div v-if="loadingProduct" class="relative mx-auto max-w-7xl px-5 py-16 text-center lg:px-8">
        <p class="text-2xl font-black text-[#2f4b1f]">Carregando produto...</p>
      </div>

      <div v-else-if="product" class="relative mx-auto max-w-7xl px-5 py-10 lg:px-8 lg:py-14">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-2 text-sm font-semibold text-[#6f775f]">
            <a href="/" class="hover:text-[#2f9c44]">Início</a>
            <span>/</span>
            <span class="text-[#2f4b1f]">{{ product.name }}</span>
          </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
          <section class="rounded-[36px] border border-[#d9dfcf] bg-white p-4 shadow-xl shadow-[#dfe8d7] sm:p-5">
            <div class="relative overflow-hidden rounded-[28px] bg-[#f2f6ee]">
              <img :src="selectedImage" :alt="product.name" class="h-[330px] w-full object-cover sm:h-[520px]" />

              <button type="button" class="absolute left-4 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 text-2xl font-black text-[#2f9c44] shadow-lg transition hover:bg-[#eef7e7]" @click="previousImage">‹</button>
              <button type="button" class="absolute right-4 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 text-2xl font-black text-[#2f9c44] shadow-lg transition hover:bg-[#eef7e7]" @click="nextImage">›</button>

              <div class="absolute left-4 top-4 rounded-full bg-[#f6ecd2] px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-[#8f5b11] shadow-sm">
                {{ product.badge }}
              </div>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-3">
              <button v-for="(image, index) in product.images" :key="image" type="button" class="overflow-hidden rounded-2xl border bg-white p-1 transition" :class="index === selectedImageIndex ? 'border-[#2f9c44] ring-2 ring-[#d4efd9]' : 'border-[#d9dfcf] hover:border-[#2f9c44]'" @click="selectedImageIndex = index">
                <img :src="image" :alt="`${product.name} ${index + 1}`" class="h-20 w-full rounded-xl object-cover" />
              </button>
            </div>
          </section>

          <aside class="rounded-[36px] border border-[#d9dfcf] bg-white p-6 shadow-xl shadow-[#dfe8d7] lg:sticky lg:top-28 lg:p-8">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2f9c44]">{{ product.category }}</p>
            <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight text-[#2f4b1f] lg:text-5xl">{{ product.name }}</h1>
            <p class="mt-4 text-base leading-8 text-[#5f6b54]">{{ product.shortDescription }}</p>

            <div class="mt-6 flex flex-wrap items-end gap-3 border-y border-[#edf1e8] py-6">
              <div>
                <p class="text-sm font-bold text-[#6f775f]">Preço</p>
                <p class="text-4xl font-black text-[#3d2d13]">{{ formatPrice(product.price) }}</p>
              </div>
              <span class="pb-2 text-base font-semibold text-[#717a66]">/ {{ product.unit }}</span>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
              <label class="block">
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">Quantidade</span>
                <select v-model.number="quantity" class="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#2f9c44]">
                  <option v-for="item in 10" :key="item" :value="item">{{ item }}</option>
                </select>
              </label>

              <div>
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">Estoque</span>
                <div class="rounded-2xl bg-[#eef8f0] px-4 py-3 text-sm font-bold text-[#246b34]">{{ product.stock }} disponíveis</div>
              </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
              <button type="button" class="rounded-full bg-[#2f9c44] px-6 py-4 text-sm font-black text-white shadow-lg shadow-green-100 transition hover:bg-[#267c37]" @click="buyNow">Comprar Agora</button>
              <button type="button" class="rounded-full border border-[#9f6a1d] bg-[#fff7df] px-6 py-4 text-sm font-black text-[#7c5316] transition hover:bg-[#f6ecd2]" @click="addToCart">Adicionar ao Carrinho</button>
            </div>

            <div class="mt-7 rounded-[28px] border border-[#d9dfcf] bg-[#f9fbf5] p-5">
              <div class="mt-4 flex gap-2">
                <input v-model="zipCode" type="text" placeholder="Insira seu CEP" class="min-w-0 flex-1 rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#2f9c44]" />
                <button type="button" class="rounded-2xl bg-[#2f4b1f] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#223816]" @click="calculateShipping">Calcular</button>
              </div>

              <p v-if="shippingMessage" class="mt-3 rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-[#5f6b54]">{{ shippingMessage }}</p>
            </div>
          </aside>
        </div>
      </div>

      <div v-else class="relative mx-auto max-w-7xl px-5 py-16 text-center lg:px-8">
        <p class="text-2xl font-black text-[#2f4b1f]">Produto não encontrado.</p>
        <a href="/" class="mt-6 inline-block rounded-full bg-[#2f9c44] px-6 py-3 text-sm font-bold text-white">Voltar para vitrine</a>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14 lg:px-8" v-if="product">
      <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
        <div class="rounded-[34px] border border-[#d9dfcf] bg-white p-6 shadow-sm lg:p-8">
          <p class="text-sm font-black uppercase tracking-[0.22em] text-[#9f6a1d]">Resumo</p>
          <h2 class="mt-2 text-3xl font-black text-[#2f4b1f]">Informações do produto</h2>
          <div class="mt-7 space-y-4">
            <div v-for="item in productDetails" :key="item.label" class="flex items-start justify-between gap-6 border-b border-[#edf1e8] pb-4 last:border-b-0 last:pb-0">
              <span class="text-sm font-bold text-[#6f775f]">{{ item.label }}</span>
              <span class="max-w-[220px] text-right text-sm font-black text-[#2f4b1f]">{{ item.value }}</span>
            </div>
          </div>
        </div>

        <div class="rounded-[34px] border border-[#d9dfcf] bg-white p-6 shadow-sm lg:p-8">
          <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2f9c44]">Descrição</p>
          <h2 class="mt-2 text-3xl font-black text-[#2f4b1f]">{{ product.name }} selecionado pela Família Mogi</h2>
          <div class="mt-5 space-y-4 text-base leading-8 text-[#5f6b54]">
            <p>{{ product.description }}</p>
            <p>Esta página prioriza foto grande, preço claro, compra rápida e dados de apoio para conversão.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="border-y border-[#d9dfcf] bg-white/70 py-14" v-if="product">
      <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2f9c44]">Produtos relacionados</p>
            <h2 class="mt-2 text-3xl font-black text-[#2f4b1f]">Combine com seu pedido</h2>
          </div>
          <a href="/" class="rounded-full border border-[#9f6a1d] bg-white px-5 py-3 text-center text-sm font-bold text-[#7c5316] transition hover:bg-[#fff7df]">Ver todos</a>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-3">
          <article v-for="item in relatedProducts" :key="item.id" class="overflow-hidden rounded-[30px] border border-[#d9dfcf] bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
            <a :href="`/produtos/${item.id}`">
              <img :src="item.images[0]" :alt="item.name" class="h-52 w-full object-cover" />
            </a>
            <div class="p-5">
              <p class="text-xs font-black uppercase tracking-[0.18em] text-[#2f9c44]">{{ item.category }}</p>
              <a :href="`/produtos/${item.id}`" class="mt-2 block text-xl font-black text-[#2f4b1f] hover:text-[#2f9c44]">{{ item.name }}</a>
              <div class="mt-4 flex items-end gap-2">
                <span class="text-2xl font-black text-[#3d2d13]">{{ formatPrice(item.price) }}</span>
                <span class="pb-1 text-sm text-[#717a66]">/ {{ item.unit }}</span>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <CartDrawer
      :open="cartOpen"
      :cart-count="cartCount"
      :cart="cart"
      :subtotal="subtotal"
      :shipping="shipping"
      :total="total"
      :format-price="formatPrice"
      @close="cartOpen = false"
      @remove-item="removeItem"
      @update-quantity="updateQuantity"
      @open-checkout="goToCheckout"
    />
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { storeToRefs } from 'pinia';
import { useCartStore } from '../stores/cartStore';
import SiteHeader from './SiteHeader.vue';
import CartDrawer from './CartDrawer.vue';

const brand = {
  name: 'Família Mogi',
  slogan: 'Produtos frescos direto do produtor para sua casa',
  whatsapp: '(11) 99999-9999',
  email: 'contato@familiamogi.com.br',
  logo: '/images/logo-familia-mogi.svg',
};

const pathParts = window.location.pathname.split('/').filter(Boolean);
const productId = Number(pathParts[pathParts.length - 1]);
const products = ref([]);
const loadingProduct = ref(false);
const product = computed(() => products.value.find((item) => item.id === productId));

const selectedImageIndex = ref(0);
const quantity = ref(1);
const zipCode = ref('');
const shippingMessage = ref('');
const cartOpen = ref(false);
const cartStore = useCartStore();
const { cart, cartCount, subtotal, shipping, total } = storeToRefs(cartStore);

const selectedImage = computed(() => product.value?.images[selectedImageIndex.value] || '');
const relatedProducts = computed(() => products.value.filter((item) => item.id !== productId).slice(0, 3));
const productDetails = computed(() => {
  if (!product.value) return [];
  return [
    { label: 'Categoria', value: product.value.category },
    { label: 'Unidade', value: product.value.unit },
    { label: 'Disponibilidade', value: `${product.value.stock} unidades` },
    { label: 'Origem', value: 'Mogi das Cruzes - SP' },
    { label: 'Indicação', value: 'Risotos, massas e refogados' }
  ];
});

function formatPrice(value) {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function getPreviousImageIndex(currentIndex, totalImages) {
  if (totalImages <= 0) return 0;
  return currentIndex === 0 ? totalImages - 1 : currentIndex - 1;
}

function getNextImageIndex(currentIndex, totalImages) {
  if (totalImages <= 0) return 0;
  return currentIndex === totalImages - 1 ? 0 : currentIndex + 1;
}

function getShippingMessage(value) {
  const cep = value.replace(/\D/g, '');
  if (cep.length !== 8) return 'Informe um CEP válido com 8 números.';
  return 'Entrega estimada em até 24h. Frete a partir de R$ 8,90 para sua região.';
}

function previousImage() {
  if (!product.value) return;
  selectedImageIndex.value = getPreviousImageIndex(selectedImageIndex.value, product.value.images.length);
}

function nextImage() {
  if (!product.value) return;
  selectedImageIndex.value = getNextImageIndex(selectedImageIndex.value, product.value.images.length);
}

function calculateShipping() {
  shippingMessage.value = getShippingMessage(zipCode.value);
}

function addToCart() {
  if (!product.value) return;
  cartStore.addItem(product.value, quantity.value);
}

function buyNow() {
  addToCart();
  cartOpen.value = true;
}

function onOpenCart() {
  cartOpen.value = true;
}

function updateQuantity(productId, delta) {
  cartStore.updateQuantity(productId, delta);
}

function removeItem(productId) {
  cartStore.removeItem(productId);
}

function goToCheckout() {
  window.location.href = '/?checkout=1';
}

async function loadProducts() {
  loadingProduct.value = true;

  try {
    const { data } = await axios.get('/api/v1/catalog/products', {
      params: { per_page: 500 },
    });

    const apiProducts = Array.isArray(data?.data) ? data.data : [];
    products.value = apiProducts.map((item) => ({
      id: item.id,
      name: item.name,
      category: item.category || 'Sem categoria',
      price: Number(item.price || 0),
      unit: item.unit || 'unidade',
      badge: item.badge || 'Disponível',
      stock: Number(item.stock || 0),
      shortDescription: item.shortDescription || 'Produto do catálogo integrado.',
      description: item.description || 'Produto sincronizado automaticamente pelo catálogo integrado.',
      images: Array.isArray(item.images) && item.images.length > 0 ? item.images : ['/images/logo-familia-mogi.svg'],
    }));
  } catch (error) {
    products.value = [];
    console.error(error);
  } finally {
    loadingProduct.value = false;
  }
}

onMounted(async () => {
  await loadProducts();
});
</script>
