<template>
  <ContactPage v-if="isContactPage" />
  <ProductDetailPage v-else-if="isProductDetail" />
  <Storefront v-else />
</template>

<script setup>
import Storefront from './components/Storefront.vue';
import ProductDetailPage from './components/ProductDetailPage.vue';
import ContactPage from './components/ContactPage.vue';

const isContactPage = window.location.pathname === '/contato';
const isProductDetail = /^\/produtos\/\d+$/.test(window.location.pathname);
</script>
