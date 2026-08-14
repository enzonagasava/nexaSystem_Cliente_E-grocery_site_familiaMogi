<template>
  <div class="min-h-screen bg-[#f7f6ef] text-[#2b2b2b]">
    <SiteHeader :brand="brand" :cart-count="cartCount" @open-cart="cartOpen = true" />

    <main v-if="!checkoutOpen">
      <section id="inicio" class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(47,156,68,0.16),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(225,179,0,0.18),_transparent_30%),linear-gradient(180deg,#f7f6ef_0%,#eef5e8_100%)]" />
        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
          <div>
            <span class="inline-flex rounded-full border border-[#d8c48a] bg-white px-4 py-1.5 text-sm font-bold text-[#9f6a1d] shadow-sm">
              Compra online • Entrega local • Marca familiar
            </span>

            <h1 class="mt-6 max-w-3xl text-4xl font-black leading-tight tracking-tight text-[#2f4b1f] sm:text-5xl lg:text-6xl">
              Família Mogi
            </h1>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
              <a href="#produtos" class="rounded-full bg-[#2f9c44] px-7 py-3.5 text-center text-base font-bold text-white shadow-lg shadow-green-200 transition hover:bg-[#267c37]">
                Ver Catálogo
              </a>
<!--               <button @click="cartOpen = true" class="rounded-full border border-[#9f6a1d] bg-white px-7 py-3.5 text-base font-bold text-[#7c5316] transition hover:bg-[#f8f0da]">
                Abrir carrinho
              </button> -->
            </div>

<!--             <p class="mt-6 text-sm text-[#6f775f]">
              API: <strong>{{ apiBaseUrl || 'não configurada' }}</strong> | Webhook: <strong>{{ webhookUrl || 'não configurado' }}</strong>
            </p> -->
          </div>

          <div class="rounded-[36px] border border-[#d8c48a] bg-white p-6 shadow-2xl shadow-[#dfe8d7]">
            <div class="rounded-[28px] bg-[linear-gradient(180deg,#f9fbf5_0%,#f1f7eb_100%)] p-8 text-center">
               <Carousel v-bind="carouselConfig">
                <Slide
                        v-for="(carousel, index) in carrossel"
                        :key="index"
                    >
                        <img
                            :src="carousel.image"
                            :alt="carousel.alt"
                        />
                    </Slide>
                  <template #addons>
                    <Pagination />
                  </template>
              </Carousel>
            </div>
          </div>
        </div>
      </section>

      <section id="categorias" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <SectionTitle
          eyebrow="Categorias"
          title="Seções preparadas para vender mais"
          description="A estrutura da loja separa os principais grupos da operação e facilita tanto compras avulsas quanto pedidos maiores."
        />

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
          <div v-for="item in categories" :key="item.name" class="group rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
            <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-[#eef7e7] text-3xl ring-1 ring-[#d9dfcf]">{{ item.icon }}</div>
            <h3 class="text-2xl font-black text-[#2f4b1f]">{{ item.name }}</h3>
            <p class="mt-3 text-sm leading-7 text-[#5f6b54]">{{ item.description }}</p>
          </div>
        </div>
      </section>

      <section id="produtos" class="border-y border-[#dde5d4] bg-white/70 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <SectionTitle
              eyebrow="Loja"
              title="Catálogo com filtros, preço e ação direta"
              description="Cards maiores, leitura clara de preço e botão de compra para acelerar a conversão."
            />

            <div class="flex w-full flex-col gap-3 sm:max-w-xl sm:flex-row">
              <input
                v-model="search"
                type="text"
                placeholder="Buscar verduras, legumes, cogumelos..."
                class="w-full rounded-full border border-[#d9dfcf] bg-white px-5 py-3 text-sm text-[#384131] outline-none transition placeholder:text-[#95a08b] focus:border-[#2f9c44]"
              />
              <select
                v-model="selectedCategory"
                class="rounded-full border border-[#d9dfcf] bg-white px-5 py-3 text-sm text-[#384131] outline-none transition focus:border-[#2f9c44]"
              >
                <option value="">Todas</option>
                <option v-for="category in categories" :key="category.name" :value="category.name">{{ category.name }}</option>
              </select>
            </div>
          </div>

          <p v-if="loadingCatalog" class="mt-10 rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm font-semibold text-[#5f6b54]">
            Carregando produtos do catálogo...
          </p>

          <p v-else-if="catalogError" class="mt-10 rounded-2xl border border-[#eed8b0] bg-[#fff7e7] px-4 py-3 text-sm font-semibold text-[#7f5518]">
            {{ catalogError }}
          </p>

          <p v-else-if="filteredProducts.length === 0" class="mt-10 rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm font-semibold text-[#5f6b54]">
            Nenhum produto encontrado para este filtro.
          </p>

          <div v-else class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <article v-for="product in filteredProducts" :key="product.id" class="overflow-hidden rounded-[30px] border border-[#d9dfcf] bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
              <a :href="`/produtos/${product.id}`" class="relative block">
                <img v-bind:src="`${product.image}`" :alt="product.name" class="h-64 w-full object-cover" />
                <span class="absolute left-4 top-4 rounded-full bg-[#f6ecd2] px-3 py-1 text-xs font-black text-[#8f5b11] shadow-sm">
                  {{ product.badge }}
                </span>
              </a>
              <div class="p-5">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#2f9c44]">{{ product.category }}</p>
                <a :href="`/produtos/${product.id}`" class="mt-2 block text-2xl font-black text-[#2f4b1f] hover:text-[#2f9c44]">{{ product.name }}</a>
                <p class="mt-3 text-sm leading-6 text-[#64705a]">{{ product.description }}</p>
                <div class="mt-4 flex items-end gap-2">
                  <span class="text-2xl font-black text-[#3d2d13]">{{ product.price ? formatPrice(product.price) : 'Consultar preço'}}</span>
                  <span class="pb-1 text-sm text-[#717a66]">/ {{ product.price ? product.unit : '' }}</span>
                </div>
                <a :href="`/produtos/${product.id}`" class="mt-4 block w-full rounded-full border border-[#9f6a1d] bg-[#fff7df] px-4 py-3 text-center text-sm font-bold text-[#7c5316] transition hover:bg-[#f6ecd2]">
                  Ver detalhes
                </a>
                <button @click="addToCart(product)" class="mt-5 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]">
                  Adicionar ao carrinho
                </button>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section id="depoimentos" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <SectionTitle
          eyebrow="Depoimentos"
          title="Bloco de prova social para aumentar confiança"
          description="Esses cards ajudam a validar a qualidade percebida da marca e apoiam a conversão da landing page."
        />

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
          <div v-for="item in testimonials" :key="item.name" class="rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm">
            <p class="text-base leading-8 text-[#54604a]">“{{ item.text }}”</p>
            <div class="mt-6 border-t border-[#edf1e8] pt-4">
              <p class="font-black text-[#2f4b1f]">{{ item.name }}</p>
              <p class="text-sm text-[#6f775f]">{{ item.role }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <SiteFooter :brand="brand" :cart-count="cartCount" @open-cart="onOpenCart"/>

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
      @open-checkout="openCheckout"
    />

    <section v-if="checkoutOpen" class="mx-auto max-w-7xl px-4 py-8 lg:px-8 lg:py-10">
      <div class="mb-6 flex items-center justify-between gap-4">
        <div>
          <h3 class="text-3xl font-black text-[#2f4b1f]">Checkout</h3>
          <p class="text-sm text-[#6f775f]">Revise seus dados e finalize o pedido</p>
        </div>
        <button @click="checkoutOpen = false" class="rounded-full border border-[#d9dfcf] bg-white px-5 py-2.5 text-sm font-bold text-[#55614a]">
          Voltar para loja
        </button>
      </div>

      <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm lg:p-8">
          <div class="grid gap-4 sm:grid-cols-2">
            <label class="block" v-for="field in checkoutFields" :key="field.key" :class="field.full ? 'sm:col-span-2' : ''">
              <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">{{ field.label }}</span>
              <input
                v-if="field.key === 'phone'"
                v-model="checkoutForm[field.key]"
                v-maska="'(##) #####-####'"
                type="text"
                class="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
              />
              <div v-else-if="field.key === 'cep'" class="flex gap-2">
                <input
                  v-model="checkoutForm[field.key]"
                  v-maska="'#####-###'"
                  type="text"
                  class="min-w-0 flex-1 rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
                  @blur="lookupCep"
                />
                <button
                  type="button"
                  class="rounded-2xl bg-[#2f4b1f] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#223816] disabled:cursor-not-allowed disabled:opacity-70"
                  :disabled="findingCep"
                  @click="lookupCep"
                >
                  {{ findingCep ? '...' : 'Buscar' }}
                </button>
              </div>
              <input
                v-else-if="field.type !== 'textarea' && field.type !== 'select'"
                v-model="checkoutForm[field.key]"
                :type="field.type"
                class="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
              />
              <textarea
                v-else-if="field.type === 'textarea'"
                v-model="checkoutForm[field.key]"
                rows="4"
                class="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
              />
              <select
                v-else
                v-model="checkoutForm[field.key]"
                class="w-full rounded-2xl border border-[#d9dfcf] bg-white px-4 py-3 text-sm outline-none focus:border-[#2f9c44]"
              >
                <option>Pix</option>
                <option>Cartão na entrega</option>
                <option>Dinheiro</option>
              </select>
            </label>
          </div>

          <div v-if="cepMessage" class="mt-4 rounded-2xl border border-[#d9dfcf] bg-[#f9fbf5] px-4 py-3 text-sm text-[#5f6b54]">
            {{ cepMessage }}
          </div>

          <div
            v-if="checkoutMessage"
            :class="[
              'mt-5 rounded-2xl border px-4 py-3 text-sm',
              orderPlaced ? 'border-[#cfe8d5] bg-[#eef8f0] text-[#246b34]' : 'border-[#eed8b0] bg-[#fff7e7] text-[#7f5518]'
            ]"
          >
            {{ checkoutMessage }}
          </div>
        </div>

        <div>
          <div class="rounded-[30px] border border-[#d9dfcf] bg-white p-6 shadow-sm lg:sticky lg:top-28">
            <h4 class="text-xl font-black text-[#2f4b1f]">Resumo do pedido</h4>
            <div class="mt-5 space-y-4">
              <div v-if="cart.length === 0" class="text-sm text-[#6f775f]">Nenhum item no carrinho.</div>
              <div v-else v-for="item in cart" :key="item.product.id" class="flex items-center justify-between gap-4 border-b border-[#edf1e8] pb-4">
                <div>
                  <p class="font-bold text-[#2f4b1f]">{{ item.product.name }}</p>
                  <p class="text-sm text-[#6f775f]">{{ item.quantity }}x • {{ formatPrice(item.product.price) }}</p>
                </div>
                <p class="font-black text-[#3d2d13]">{{ formatPrice(item.product.price * item.quantity) }}</p>
              </div>
            </div>

            <div class="mt-6 space-y-3 text-sm">
              <div class="flex items-center justify-between text-[#55614a]">
                <span>Subtotal</span>
                <span class="font-bold">{{ formatPrice(subtotal) }}</span>
              </div>
              <div class="flex items-center justify-between text-[#55614a]">
                <span>Frete</span>
                <span class="font-bold">{{ shipping === 0 ? 'Grátis' : formatPrice(shipping) }}</span>
              </div>
              <div class="flex items-center justify-between border-t border-[#edf1e8] pt-3 text-base font-black text-[#2f4b1f]">
                <span>Total</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
            </div>

            <button
              @click="submitCheckout"
              class="mt-6 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]"
            >
              {{ submitting ? 'Enviando...' : 'Finalizar pedido' }}
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import { storeToRefs } from 'pinia';
import { useCartStore } from '../stores/cartStore';
import SiteHeader from './SiteHeader.vue';
import SiteFooter from './SiteFooter.vue';
import CartDrawer from './CartDrawer.vue';

import 'vue3-carousel/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

const apiBaseUrl = import.meta.env.VITE_EGROCERY_API_URL || '';
const checkoutEndpoint = import.meta.env.VITE_EGROCERY_CHECKOUT_ENDPOINT || '/api/v1/integrations/e-grocery/orders';
const webhookUrl = import.meta.env.VITE_EGROCERY_WEBHOOK_URL || '';
const webhookToken = import.meta.env.VITE_EGROCERY_WEBHOOK_TOKEN || '';

const brand = {
  name: 'Família Mogi',
  slogan: 'Produtos frescos direto do produtor para sua casa',
  whatsapp: '(11) 94156-0613',
  email: 'enzonagasava@gmail.com',
  address: 'Mogi das Cruzes - SP',
  logo: '/images/logo-sem-fundo.png',
};

const carrossel = [
    {
        image: "/images/carrossel/image2.png",
        alt: "carrossel 2"
    },
    {
        image: "/images/carrossel/image3.jpg",
        alt: "carrossel 3"
    },
    {
        image: "/images/carrossel/image4.jpg",
        alt: "carrossel 4"
    },
    {
        image: "/images/carrossel/image5.jpg",
        alt: "carrossel 5"
    }
]

const carouselConfig = {
  height: 300,
  itemsToShow: 1,
  wrapAround: true,
  autoplay: 2000
}

const categoryMeta = {
  Verduras: { description: 'Folhas frescas e selecionadas para consumo diário.', icon: '🥬' },
  Legumes: { description: 'Legumes premium com padrão visual e sabor natural.', icon: '🥕' },
  Cogumelos: { description: 'Linha especial para varejo e gastronomia.', icon: '🍄' },
  Cestas: { description: 'Combos para família e pedidos recorrentes.', icon: '🧺' },
  'Sem categoria': { description: 'Produtos gerais do catálogo.', icon: '🛒' },
};

const testimonials = [
  { name: 'Mariana Costa', role: 'Cliente recorrente', text: 'Os produtos chegam muito frescos e o processo de compra ficou simples e profissional.' },
  { name: 'Carlos Henrique', role: 'Restaurante local', text: 'A apresentação dos cogumelos e legumes transmite qualidade logo no primeiro contato.' },
  { name: 'Fernanda Alves', role: 'Assinatura semanal', text: 'A cesta semanal ajudou a organizar minhas compras e reduzir idas ao mercado.' },
];

const initialForm = {
  name: '',
  phone: '',
  cep: '',
  email: '',
  address: '',
  addressNumber: '',
  neighborhood: '',
  city: 'Mogi das Cruzes',
  notes: '',
  paymentMethod: 'Pix',
};

const checkoutFields = [
  { key: 'name', label: 'Nome completo', type: 'text' },
  { key: 'phone', label: 'Telefone', type: 'text' },
  { key: 'cep', label: 'CEP', type: 'text' },
  { key: 'email', label: 'E-mail', type: 'email', full: true },
  { key: 'address', label: 'Endereço', type: 'text', full: true },
  { key: 'addressNumber', label: 'Número', type: 'text' },
  { key: 'neighborhood', label: 'Bairro', type: 'text' },
  { key: 'city', label: 'Cidade', type: 'text' },
  { key: 'paymentMethod', label: 'Forma de pagamento', type: 'select', full: true },
  { key: 'notes', label: 'Observações', type: 'textarea', full: true },
];

const search = ref('');
const selectedCategory = ref('');
const cartOpen = ref(false);
const checkoutOpen = ref(false);
const checkoutForm = reactive({ ...initialForm });
const checkoutMessage = ref('');
const cepMessage = ref('');
const findingCep = ref(false);
const orderPlaced = ref(false);
const submitting = ref(false);
const loadingCatalog = ref(false);
const catalogError = ref('');
const products = ref([]);
const cartStore = useCartStore();
const { cart, cartCount, subtotal, shipping, total } = storeToRefs(cartStore);

const categories = computed(() => {
  const names = [...new Set(products.value.map((item) => item.category || 'Sem categoria'))];
  return names.map((name) => ({
    name,
    description: categoryMeta[name]?.description || 'Produtos sincronizados do painel E-grocery.',
    icon: categoryMeta[name]?.icon || '🛒',
  }));
});

const filteredProducts = computed(() => filterProducts(products.value, search.value, selectedCategory.value));

function formatPrice(value) {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function filterProducts(items, searchText, selected) {
  const term = searchText.trim().toLowerCase();
  return items.filter((product) => {
    const matchesCategory = !selected || product.category === selected;
    const matchesSearch = !term || [product.name, product.category, product.description].some((f) => f.toLowerCase().includes(term));
    return matchesCategory && matchesSearch;
  });
}

function getCartCount(items) {
  return items.reduce((sum, item) => sum + item.quantity, 0);
}

function getSubtotal(items) {
  return items.reduce((sum, item) => sum + item.product.price * item.quantity, 0);
}

function getShipping(value) {
  if (value === 0) return 0;
  return value >= 80 ? 0 : 8.9;
}

function validateCheckout(form, items) {
  if (items.length === 0) return 'Seu carrinho está vazio.';
  if (!form.name.trim()) return 'Informe seu nome.';
  if (!form.phone.trim()) return 'Informe seu telefone.';
  if (!form.cep.trim()) return 'Informe seu CEP.';
  if (!form.address.trim()) return 'Informe seu endereço.';
  if (!form.addressNumber.trim()) return 'Informe o número do endereço.';
  if (!form.neighborhood.trim()) return 'Informe seu bairro.';
  if (!form.city.trim()) return 'Informe sua cidade.';
  return '';
}

async function lookupCep() {
  const cepDigits = checkoutForm.cep.replace(/\D/g, '');

  if (cepDigits.length !== 8) {
    cepMessage.value = 'Informe um CEP válido com 8 números.';
    return;
  }

  findingCep.value = true;
  cepMessage.value = 'Buscando endereço...';

  try {
    const { data } = await axios.get(`/api/cep/${cepDigits}`);

    if (!data.street && !data.neighborhood && !data.city) {
      cepMessage.value = 'CEP encontrado, mas sem dados completos de endereço.';
      return;
    }

    if (data.street) checkoutForm.address = data.street;
    if (data.neighborhood) checkoutForm.neighborhood = data.neighborhood;
    if (data.city) checkoutForm.city = data.state ? `${data.city} - ${data.state}` : data.city;

    cepMessage.value = 'Endereço preenchido automaticamente pelo CEP.';
  } catch (error) {
    console.error(error);
    cepMessage.value = 'Não foi possível consultar o CEP agora. Preencha o endereço manualmente.';
  } finally {
    findingCep.value = false;
  }
}

async function loadCatalogProducts() {
  loadingCatalog.value = true;
  catalogError.value = '';

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
      image: item?.images?.[0] || '/images/logo-familia-mogi.svg',
      description: item.shortDescription || item.description || 'Produto do catálogo integrado.',
      stock: Number(item.stock || 0),
    }));
  } catch (error) {
    catalogError.value = 'Não foi possível carregar os produtos do catálogo.';
    products.value = [];
    console.error(error);
  } finally {
    loadingCatalog.value = false;
  }
}

function addToCart(product) {
  cartStore.addItem(product, 1);
  cartOpen.value = true;
}

function updateQuantity(productId, delta) {
  cartStore.updateQuantity(productId, delta);
}

function removeItem(productId) {
  cartStore.removeItem(productId);
}

function openCheckout() {
  window.location.href = '/?checkout=1';
}

async function submitCheckout() {
  const error = validateCheckout(checkoutForm, cart.value);
  if (error) {
    checkoutMessage.value = error;
    orderPlaced.value = false;
    return;
  }

  const payload = {
    customer: {
      name: checkoutForm.name,
      phone: checkoutForm.phone,
      email: checkoutForm.email,
    },
    delivery: {
      cep: checkoutForm.cep,
      address: checkoutForm.address,
      address_number: checkoutForm.addressNumber,
      neighborhood: checkoutForm.neighborhood,
      city: checkoutForm.city,
      notes: checkoutForm.notes,
    },
    payment_method: checkoutForm.paymentMethod,
    items: cart.value.map((item) => ({
      product_id: item.product.id,
      name: item.product.name,
      unit_price: item.product.price,
      quantity: item.quantity,
    })),
    subtotal: subtotal.value,
    shipping: shipping.value,
    total: total.value,
    source: 'familiaMogi-front',
  };

  submitting.value = true;

  try {
    let orderResponse = null;

    if (apiBaseUrl) {
      orderResponse = await axios.post(`${apiBaseUrl.replace(/\/$/, '')}${checkoutEndpoint}`, payload, {
        headers: { 'Content-Type': 'application/json' },
      });
    }

    const effectiveWebhookUrl = webhookUrl || (apiBaseUrl ? `${apiBaseUrl.replace(/\/$/, '')}/api/checkout/webhook` : '');

    if (effectiveWebhookUrl) {
      await axios.post(effectiveWebhookUrl, {
        event: 'order.created',
        order: orderResponse?.data || payload,
      }, {
        headers: {
          'Content-Type': 'application/json',
          ...(webhookToken ? { Authorization: `Bearer ${webhookToken}` } : {}),
        },
      });
    }

    checkoutMessage.value = apiBaseUrl
      ? 'Pedido enviado com sucesso para API e webhook configurados.'
      : 'Pedido validado no front. Configure VITE_EGROCERY_API_URL para envio real.';
    orderPlaced.value = true;
    cartStore.clear();
    Object.assign(checkoutForm, initialForm);
  } catch (submitError) {
    checkoutMessage.value = 'Falha ao enviar pedido para integração. Verifique API/Webhook e tente novamente.';
    orderPlaced.value = false;
    console.error(submitError);
  } finally {
    submitting.value = false;
  }
}

function runStoreTests() {
  const sampleProducts = [
    { id: 1, name: 'Shimeji', category: 'Cogumelos', description: 'Fresco', price: 19.9 },
    { id: 2, name: 'Alface', category: 'Verduras', description: 'Orgânica', price: 5.9 },
    { id: 3, name: 'Cenoura', category: 'Legumes', description: 'Selecionada', price: 2.9 },
  ];
  const filtered = filterProducts(sampleProducts, 'cogumelo', 'Cogumelos');
  const testCart = [{ product: sampleProducts[0], quantity: 2 }, { product: sampleProducts[2], quantity: 1 }];
  const testSubtotal = getSubtotal(testCart);

  console.assert(filtered.length >= 1, 'Deve filtrar produtos por busca e categoria');
  console.assert(getCartCount(testCart) === 3, 'Deve contar itens do carrinho');
  console.assert(Math.abs(testSubtotal - 42.7) < 0.001, 'Deve calcular subtotal');
  console.assert(getShipping(testSubtotal) === 8.9, 'Deve calcular frete corretamente');
  console.assert(validateCheckout({ ...initialForm, name: 'Enzo', phone: '11999999999', cep: '08710-000', address: 'Rua A', addressNumber: '100', neighborhood: 'Centro', city: 'Mogi' }, testCart) === '', 'Validação completa deve passar');
}

const SectionTitle = {
  props: {
    eyebrow: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, default: '' },
  },
  template: `
    <div>
      <p class="text-sm font-bold uppercase tracking-[0.22em] text-[#2f9c44]">{{ eyebrow }}</p>
      <h2 class="mt-2 text-3xl font-black tracking-tight text-[#2f4b1f] sm:text-4xl">{{ title }}</h2>
      <p v-if="description" class="mt-4 max-w-2xl text-base leading-7 text-[#5b684f]">{{ description }}</p>
    </div>
  `,
};

onMounted(async () => {
  await loadCatalogProducts();

  const params = new URLSearchParams(window.location.search);
  if (params.get('checkout') === '1' && cart.value.length > 0) {
    checkoutMessage.value = '';
    orderPlaced.value = false;
    checkoutOpen.value = true;
    cartOpen.value = false;
  }

  runStoreTests();
});

</script>
<style>
.carousel__pagination-button {
  height: 5px;
  width: 5px;
  border-radius: 5px;
  background-color: white;
}
.carousel__pagination-button--active {
  background-color: #267c37;
}
</style>
