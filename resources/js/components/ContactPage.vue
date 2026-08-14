<template>
  <main class="min-h-screen bg-[#f7f6ef] text-[#2b2b2b]">
    <SiteHeader :brand="brand" :cart-count="cartCount" @open-cart="cartOpen = true" />

    <section class="relative overflow-hidden border-b border-[#d9dfcf]">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(47,156,68,0.16),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(225,179,0,0.18),_transparent_30%),linear-gradient(180deg,#f7f6ef_0%,#eef5e8_100%)]" />

      <div class="relative mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20">
        <div class="max-w-3xl">
          <p class="text-sm font-black uppercase tracking-[0.24em] text-[#2f9c44]">Contato</p>
          <h1 class="mt-3 text-4xl font-black leading-tight tracking-tight text-[#2f4b1f] sm:text-5xl">Fale com a Família Mogi</h1>
          <p class="mt-5 text-lg leading-8 text-[#5f6b54]">Tire dúvidas sobre produtos, entregas, pedidos recorrentes, cestas semanais ou compras para restaurantes e mercados.</p>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
      <div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
    <form @submit.prevent="submitContact"
        class="rounded-[36px] border border-[#d9dfcf] bg-white p-6 shadow-xl shadow-[#dfe8d7] lg:p-8"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-[#9f6a1d]"> Mensagem </p>
                <h2 class="mt-2 text-3xl font-black text-[#2f4b1f]">Envie sua solicitação</h2>
                <p class="mt-3 text-sm leading-7 text-[#6f775f]">Preencha os dados abaixo e retornaremos pelo melhor canal informado.</p>
            </div>
            <span class="hidden rounded-full bg-[#eef8f0] px-4 py-2 text-xs font-black text-[#246b34] sm:inline-flex">
                Resposta rápida
            </span>
        </div>

        <div class="mt-8 grid gap-5 sm:grid-cols-2">
            <label class="block">
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">
                    Nome completo
                </span>
                <input
                    v-model="form.name"
                    name="nome"
                    type="text"
                    required
                    placeholder="Seu nome"
                    class="w-full rounded-2xl border border-[#d9dfcf] bg-[#fcfdf9] px-4 py-3 text-sm outline-none transition placeholder:text-[#9ba893] focus:border-[#2f9c44] focus:bg-white"
                />
            </label>

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">
                    Telefone / WhatsApp
                </span>
                <input
                    v-model="form.phone"
                    name="Telefone"
                    type="tel"
                    inputmode="tel"
                    required
                    placeholder="(11) 99999-9999"
                    class="w-full rounded-2xl border border-[#d9dfcf] bg-[#fcfdf9] px-4 py-3 text-sm outline-none transition placeholder:text-[#9ba893] focus:border-[#2f9c44] focus:bg-white"
                    @input="onPhoneInput"
                />
            </label>

            <label class="block sm:col-span-2">
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">
                    E-mail
                </span>
                <input
                    v-model="form.email"
                    name="email"
                    type="email"
                    required
                    placeholder="voce@email.com"
                    class="w-full rounded-2xl border border-[#d9dfcf] bg-[#fcfdf9] px-4 py-3 text-sm outline-none transition placeholder:text-[#9ba893] focus:border-[#2f9c44] focus:bg-white"
                />
            </label>

            <label class="block sm:col-span-2">
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">
                    Assunto
                </span>
                <select
                    v-model="form.subject"
                    name="assunto"
                    required
                    class="w-full rounded-2xl border border-[#d9dfcf] bg-[#fcfdf9] px-4 py-3 text-sm outline-none transition focus:border-[#2f9c44] focus:bg-white"
                >
                    <option value="Pedido">Pedido</option>
                    <option value="Entrega">Entrega</option>
                    <option value="Cestas semanais">Cestas semanais</option>
                    <option value="Compra para restaurante">
                        Compra para restaurante
                    </option>
                    <option value="Outro assunto">
                        Outro assunto
                    </option>
                </select>
            </label>

            <label class="block sm:col-span-2">
                <span class="mb-2 block text-sm font-bold text-[#2f4b1f]">
                    Mensagem
                </span>

                <textarea
                    v-model="form.message"
                    name="mensagem"
                    required
                    rows="6"
                    placeholder="Conte como podemos ajudar..."
                    class="w-full resize-none rounded-2xl border border-[#d9dfcf] bg-[#fcfdf9] px-4 py-3 text-sm outline-none transition placeholder:text-[#9ba893] focus:border-[#2f9c44] focus:bg-white"
                ></textarea>
            </label>
        </div>
        <div v-if="feedback" class="mt-5 rounded-2xl border px-4 py-3 text-sm font-semibold" :class="feedbackType === 'success' ? 'border-[#cfe8d5] bg-[#eef8f0] text-[#246b34]' : 'border-[#eed8b0] bg-[#fff7e7] text-[#7f5518]'">{{ feedback }}
        </div>

        <input type="hidden" name="_subject" value="Nova solicitação - Hortifruti">
        <input type="hidden" name="_captcha" value="false">

        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
            <button
                type="submit"
                class="rounded-full bg-[#2f9c44] px-7 py-3.5 text-sm font-black text-white shadow-lg shadow-green-100 transition hover:bg-[#267c37]"
            >
                Enviar mensagem
            </button>

            <a
                :href="whatsappLink"
                target="_blank"
                rel="noopener noreferrer"
                class="rounded-full border border-[#9f6a1d] bg-[#fff7df] px-7 py-3.5 text-center text-sm font-black text-[#7c5316] transition hover:bg-[#f6ecd2]"
            >
                Chamar no WhatsApp
            </a>
        </div>
    </form>

        <aside class="space-y-6">
          <div class="rounded-[36px] border border-[#d9dfcf] bg-white p-6 shadow-xl shadow-[#dfe8d7] lg:p-8">
            <p class="text-sm font-black uppercase tracking-[0.22em] text-[#2f9c44]">Informações</p>
            <h2 class="mt-2 text-3xl font-black text-[#2f4b1f]">Canais de atendimento</h2>

            <div class="mt-7 grid gap-4">
              <div v-for="item in contactCards" :key="item.title" class="rounded-[26px] border border-[#edf1e8] bg-[#fcfdf9] p-5">
                <div class="flex gap-4">
                  <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#eef8f0] text-xl">{{ item.icon }}</div>
                  <div>
                    <p class="font-black text-[#2f4b1f]">{{ item.title }}</p>
                    <p class="mt-1 text-sm font-semibold text-[#5f6b54]">{{ item.value }}</p>
                    <p class="mt-1 text-xs leading-5 text-[#7b8771]">{{ item.description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden rounded-[24px] border border-[#d9dfcf] bg-white shadow-xl shadow-[#dfe8d7]">
            <div class="relative h-[290px] bg-[#eef7e7]">
              <a :href="mapsLink" target="_blank" class="absolute left-2 top-2 z-10 rounded-md bg-white/95 px-2 py-1 text-xs font-semibold text-[#2f4b1f] shadow">
                Abrir no Maps
              </a>
              <iframe
                title="Mapa de Mogi das Cruzes"
                class="h-full w-full border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q=Rua%20Exemplo%2C%20123%20-%20Bairro%20Central%2C%20Mogi%20das%20Cruzes%20-%20SP&output=embed"
              />
            </div>
          </div>
        </aside>
      </div>
    </section>

    <SiteFooter :brand="brand" :cart-count="cartCount" @open-cart="cartOpen = true" />

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
import { computed, reactive, ref } from 'vue';
import { storeToRefs } from 'pinia';
import SiteHeader from './SiteHeader.vue';
import CartDrawer from './CartDrawer.vue';
import SiteFooter from './SiteFooter.vue';
import { useCartStore } from '../stores/cartStore';

const brand = {
  name: 'Família Mogi',
  slogan: 'Produtos frescos direto do produtor para sua casa',
  whatsapp: '(11) 94156-0613',
  email: 'enzonagasava@gmail.com',
  address: 'Mogi das Cruzes - SP',
  logo: '/images/logo-sem-fundo.png',
};


const appUrl = import.meta.env.VITE_APP_URL;

const cartStore = useCartStore();
const { cart, cartCount, subtotal, shipping, total } = storeToRefs(cartStore);
const cartOpen = ref(false);

const feedback = ref('');
const feedbackType = ref('success');

const form = reactive({
  name: '',
  phone: '',
  email: '',
  subject: 'Pedido',
  message: '',
});

const whatsappLink = computed(() => {
  const text = encodeURIComponent('Olá, vim pelo site da Família Mogi e gostaria de atendimento.');
  return `https://wa.me/5511999999999?text=${text}`;
});

const mapsLink = 'https://www.google.com/maps/search/?api=1&query=Rua+Exemplo,+123+-+Bairro+Central,+Mogi+das+Cruzes+-+SP';

const contactCards = [
  { icon: '📞', title: 'Telefone / WhatsApp', value: brand.whatsapp, description: 'Canal principal para pedidos, dúvidas e entregas.' },
  { icon: '✉️', title: 'E-mail', value: brand.email, description: 'Ideal para parcerias, compras maiores e solicitações comerciais.' },
  { icon: '📍', title: 'Endereço', value: 'Mogi das Cruzes - SP', description: 'Entrega local e retirada sob combinação.' },
  { icon: '🕐', title: 'Horário', value: 'Seg a sáb • 7h às 18h', description: 'Atendimento em horário comercial e pedidos programados.' },
];

function formatPrice(value) {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function onPhoneInput(event) {
  form.phone = formatPhoneMask(event.target.value);
}

function formatPhoneMask(value) {
  const digits = value.replace(/\D/g, '').slice(0, 11);

  if (!digits) return '';

  if (digits.length <= 2) return `(${digits}`;
  if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
  if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;

  return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
}

async function submitContact() {

    if (!form.name.trim()) {
        feedback.value = 'Informe seu nome para continuar.'
        feedbackType.value = 'error'
        return
    }

    if (!form.phone.trim() && !form.email.trim()) {
        feedback.value = 'Informe pelo menos um canal de contato: telefone ou e-mail.'
        feedbackType.value = 'error'
        return
    }

    if (!form.message.trim()) {
        feedback.value = 'Escreva uma mensagem antes de enviar.'
        feedbackType.value = 'error'
        return
    }

    try {
        const response = await fetch(
            "https://formsubmit.co/ajax/enzonagasava@gmail.com",
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: form.name,
                    phone: form.phone,
                    email: form.email,
                    subject: form.subject,
                    message: form.message,
                }),
            }
        )

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`)
        }

        const data = await response.json()

        if (data.success) {
            feedbackType.value = 'success'
            feedback.value = 'Mensagem enviada com sucesso!'

            form.name = ''
            form.phone = ''
            form.email = ''
            form.subject = 'Pedido'
            form.message = ''
        } else {
            throw new Error(data.message || 'Erro ao enviar mensagem')
        }

    } catch (error) {
        console.error('Erro ao enviar formulário:', error)

        feedbackType.value = 'error'
        feedback.value =
            'Não foi possível enviar sua mensagem. Tente novamente.'
    }
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
</script>
