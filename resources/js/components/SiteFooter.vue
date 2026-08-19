<template>
    <footer v-if="!checkoutOpen" id="contato" class="border-t border-[#d9dfcf] bg-[#f1f5ea]">
      <div class="mx-auto grid max-w-7xl gap-10 px-6 py-12 lg:grid-cols-[1.1fr_0.9fr_0.9fr] lg:px-8">
        <div>
          <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-[#d8c48a] bg-white shadow-sm">
              <img :src="brand.logo" :alt="brand.name" class="h-full w-full object-contain p-1" />
            </div>
            <div>
              <p class="text-2xl font-black text-[#2f9c44]">{{ brand.name }}</p>
              <p class="text-sm text-[#66715b]">{{ brand.slogan }}</p>
            </div>
          </div>
        </div>

        <div>
          <p class="text-sm font-black uppercase tracking-[0.2em] text-[#9f6a1d]">Mapa do site</p>
          <div class="mt-4 space-y-3 text-sm font-semibold text-[#55614a]">
            <a href="#inicio" class="block transition hover:text-[#2f9c44]">Início</a>
            <a href="#categorias" class="block transition hover:text-[#2f9c44]">Categorias</a>
            <a href="#produtos" class="block transition hover:text-[#2f9c44]">Loja</a>
            <a href="#depoimentos" class="block transition hover:text-[#2f9c44]">Depoimentos</a>
          </div>
        </div>

        <div>
          <p class="text-sm font-black uppercase tracking-[0.2em] text-[#9f6a1d]">Contato</p>
          <div class="mt-4 space-y-3 text-sm text-[#55614a]">
            <p>{{ user.telefone }}</p>
            <p>{{ user.email }}</p>
            <p>{{ enderecoFormatado }}</p>
            <p>Seg a sáb • 7h às 18h</p>
          </div>
        </div>
      </div>
    </footer>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
  brand: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
  cartCount: {
    type: Number,
    default: 0,
  },
});

defineEmits(['open-cart']);

const enderecoFormatado = computed(() => {
  const endereco = props.user.endereco;

  if (!endereco) {
    return 'Endereço não informado';
  }

  return `${endereco.logradouro}, ${endereco.numero} - ${endereco.bairro}, ${endereco.cidade} - ${endereco.estado}`;
});
</script>
