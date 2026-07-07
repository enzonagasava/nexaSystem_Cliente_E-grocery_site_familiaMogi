import '../css/app.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { vMaska } from 'maska/vue';
import App from './App.vue';
import { useCartStore } from './stores/cartStore';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.directive('maska', vMaska);

const cartStore = useCartStore(pinia);
cartStore.hydrate();

app.mount('#app');
