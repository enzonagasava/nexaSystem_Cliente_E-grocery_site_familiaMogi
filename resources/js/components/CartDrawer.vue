<template>
  <div v-if="open" class="fixed inset-0 z-50 flex justify-end bg-black/35" @click="$emit('close')">
    <div class="flex h-full w-full max-w-xl flex-col bg-[#fcfdf9] shadow-2xl" @click.stop>
      <div class="flex items-center justify-between border-b border-[#d9dfcf] px-6 py-5">
        <div>
          <h3 class="text-2xl font-black text-[#2f4b1f]">Seu carrinho</h3>
          <p class="text-sm text-[#6f775f]">{{ cartCount }} item(ns) selecionado(s)</p>
        </div>
        <button @click="$emit('close')" class="cursor-pointer rounded-full border border-[#d9dfcf] px-4 py-2 text-sm font-bold text-[#55614a]">
          Fechar
        </button>
      </div>

      <div class="flex-1 overflow-y-auto px-6 py-5">
        <div v-if="cart.length === 0" class="rounded-[28px] border border-dashed border-[#d0d8c6] bg-white p-8 text-center">
          <p class="text-xl font-black text-[#2f4b1f]">Carrinho vazio</p>
          <p class="mt-3 text-sm leading-7 text-[#64705a]">Adicione produtos para visualizar subtotal, frete e finalizar sua compra.</p>
        </div>
        <div v-else class="space-y-4">
          <div v-for="item in cart" :key="item.product.id" class="rounded-[28px] border border-[#d9dfcf] bg-white p-4 shadow-sm">
            <div class="flex gap-4">
              <img :src="getImage(item)" :alt="item.product.name" class="h-24 w-24 rounded-2xl object-cover" />
              <div class="flex-1">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#2f9c44]">{{ item.product.category }}</p>
                    <h4 class="mt-1 text-lg font-black text-[#2f4b1f]">{{ item.product.name }}</h4>
                    <p class="mt-1 text-sm text-[#6f775f]">{{ item.quantity }} unidade(s)</p>
                  </div>
                  <button @click="$emit('remove-item', item.product.id)" class="text-sm font-bold text-[#9f6a1d]">Remover</button>
                </div>
                <div class="mt-4 flex items-center justify-between gap-4">
                  <div class="inline-flex items-center rounded-full border border-[#d9dfcf] bg-white">
                    <button @click="$emit('update-quantity', item.product.id, -1)" class="px-3 py-1.5 text-sm font-bold text-[#5b684f] hover:text-[#2f9c44]">−</button>
                    <span class="min-w-10 text-center text-sm font-bold text-[#2f4b1f]">{{ item.quantity }}</span>
                    <button @click="$emit('update-quantity', item.product.id, 1)" class="px-3 py-1.5 text-sm font-bold text-[#5b684f] hover:text-[#2f9c44]">+</button>
                  </div>
                  <p class="text-lg font-black text-[#3d2d13]">Sob Consulta</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-[#d9dfcf] bg-white px-6 py-5">
        <div class="space-y-3 text-sm">

          <div class="flex items-center justify-between text-[#55614a]">
            <!-- <span>Subtotal</span> -->
            <!-- <span class="font-bold">{{ formatPrice(subtotal) }}</span> -->
          </div>
          <!--<div class="flex items-center justify-between text-[#55614a]">
            <span>Frete</span>
            <span class="font-bold">{{ shipping === 0 ? 'Grátis' : formatPrice(shipping) }}</span>
          </div>
          -->
          <!-- <div class="flex items-center justify-between border-t border-[#edf1e8] pt-3 text-base font-black text-[#2f4b1f]">
            <span>Total</span>
            <span>{{ formatPrice(total) }}</span>
          </div> -->
        </div>

        <!--  <button
          v-if="showCheckout"
          @click="$emit('open-checkout')"
          class="mt-5 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]"
        >
          Ir para checkout
        </button> -->
        <button
          @click="enviarPedidoWhatsApp"
          class="mt-5 w-full rounded-full bg-[#2f9c44] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#267c37]"
        >
        Fazer pedido
        </button>
        
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';

const props = defineProps({
  open: { type: Boolean, required: true },
  cartCount: { type: Number, required: true },
  cart: { type: Array, required: true },
  subtotal: { type: Number, required: true },
  shipping: { type: Number, required: true },
  total: { type: Number, required: true },
  formatPrice: { type: Function, required: true },
  showCheckout: { type: Boolean, default: true },
});

const telefone = ref(null)

defineEmits(['close', 'remove-item', 'update-quantity', 'open-checkout']);

function getImage(item) {
  return item?.product?.image || item?.product?.images?.[0] || '';
}

function enviarPedidoWhatsApp() {
    const produtos = props.cart.map(item => {
        const nome = item.product.name;
        const quantidade = item.quantity;
        const unidade = item.product.unit;

        return `• ${nome} — ${quantidade} ${unidade}`;
    });

    const mensagem = [
        'Olá! Gostaria de fazer um pedido:',
        '',
        ...produtos,
        '',
        'Os valores podem ser informados por vocês.',
        '',
        'Aguardo a confirmação do pedido.'
    ].join('\n');

    const url = `https://wa.me/55${telefone.value}?text=${encodeURIComponent(mensagem)}`;

    window.open(url, '_blank');
}


async function loadTelefone() {
    try {
        const { data } = await axios.get('/api/config');

        telefone.value = data.telefone;
    } catch (error) {
        console.error('Erro ao carregar contato:', error);
    }
}

onMounted(async()=>{
  await loadTelefone()
});
</script>
