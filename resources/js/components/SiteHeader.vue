<template>
  <header class="sticky top-0 z-40 border-b border-[#d9dfcf] bg-[#f7f6ef]/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
      <a href="/#inicio" class="flex items-center gap-3">
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
        <button class="hidden rounded-full border border-[#9f6a1d] px-4 py-2 text-sm font-bold text-[#7c5316] transition hover:bg-[#9f6a1d] hover:text-white md:inline-flex">
          Catálogo
        </button>
        <button class="rounded-full bg-[#2f9c44] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#267c37]" @click="$emit('open-cart')">
          Carrinho ({{ cartCount }})
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
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

defineEmits(['open-cart']);
</script>
