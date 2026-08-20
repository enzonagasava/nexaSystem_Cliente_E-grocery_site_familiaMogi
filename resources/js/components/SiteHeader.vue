<template>
  <header class="sticky top-0 z-40 border-b border-[#d9dfcf] bg-[#f7f6ef]/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
      <a href="/#inicio" class="flex items-center gap-3" @cick="closeMenu">
        <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-[#d8c48a] bg-white shadow-sm">
          <img :src="brand.logo" :alt="brand.name" class="h-full w-full object-contain p-1" />
        </div>
        <div>
          <p class="text-xl font-black tracking-tight text-[#2f9c44]">{{ brand.name }}</p>
          <p class="text-xs text-[#6f775f]">Verduras, legumes e cogumelos frescos</p>
        </div>
      </a>

      <nav class="hidden items-center gap-8 text-sm font-semibold text-[#4f5a43] md:flex">
        <a href="/#inicio" class="transition hover:text-[#2f9c44]">Início</a>
        <a href="/#categorias" class="transition hover:text-[#2f9c44]">Categorias</a>
        <a href="/#produtos" class="transition hover:text-[#2f9c44]">Loja</a>
        <a href="/#como-funciona" class="transition hover:text-[#2f9c44]">Como funciona</a>
        <a href="/#depoimentos" class="transition hover:text-[#2f9c44]">Depoimentos</a>
        <a href="/contato" class="transition hover:text-[#2f9c44]">Contato</a>
      </nav>

      <div class="flex items-center gap-3">
        <button class="cursor-pointer rounded-full bg-[#2f9c44] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#267c37]" @click="$emit('open-cart')">
          Carrinho ({{ cartCount }})
        </button>

      <button
        type="button"
        class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg border border-[#d9dfcf] text-[#4f5a43] transition hover:bg-[#eef0e6] md:hidden"
        :aria-expanded="menuOpen"
        aria-label="Abrir menu"
        @click="menuOpen = !menuOpen"
      >
        <!-- Hamburguer -->
        <svg
          v-if="!menuOpen"
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>

        <!-- Fechar -->
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M6 6l12 12M6 18L18 6"
          />
        </svg>
      </button>

      </div>
    </div>

    <div
      v-if="menuOpen"
      class="border-t border-[#d9dfcf] bg-[#f7f6ef] md:hidden"
    >
      <nav class="mx-auto flex max-w-7xl flex-col px-6 py-4 lg:px-8">
        <a
          href="/#inicio"
          class="border-b border-[#e1e5da] py-3 text-sm font-semibold text-[#4f5a43] transition hover:text-[#2f9c44]"
          @click="closeMenu">
            Inicio
        </a>
        <a
          href="/#categorias"
          class="border-b border-[#e1e5da] py-3 text-sm font-semibold text-[#4f5a43] transition hover:text-[#2f9c44]"
          @click="closeMenu">
            Categorias
        </a>
        <a
          href="/#loja"
          class="border-b border-[#e1e5da] py-3 text-sm font-semibold text-[#4f5a43] transition hover:text-[#2f9c44]"
          @click="closeMenu">
            Loja
        </a>
        <a
          href="/#como-funciona"
          class="border-b border-[#e1e5da] py-3 text-sm font-semibold text-[#4f5a43] transition hover:text-[#2f9c44]"
          @click="closeMenu">
            Como funciona
        </a>
        <a
          href="/#depoimentos"
          class="border-b border-[#e1e5da] py-3 text-sm font-semibold text-[#4f5a43] transition hover:text-[#2f9c44]"
          @click="closeMenu">
            Depoimentos
        </a>
        <a
          href="/contato"
          class="border-b border-[#e1e5da] py-3 text-sm font-semibold text-[#4f5a43] transition hover:text-[#2f9c44]"
          @click="closeMenu">
            Contato
        </a>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
defineProps({
  brand: {
    type: Object,
    required: true,
  },
  cartCount: {
    type: Number,
    default: 0,
  },
});

const menuOpen = ref(false);

const closeMenu = () => {
  menuOpen.value = false;
}

defineEmits(['open-cart']);
</script>
