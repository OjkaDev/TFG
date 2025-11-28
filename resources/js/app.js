import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';  // El componente raíz que puedes crear
import router from './index.js';  // Importa el router desde el archivo index.js
import '../css/app.css';

// Crea la aplicación Vue y usa el router
const app = createApp(App);

// Usa Vue Router en la aplicación
app.use(router);

app.mount('#app');
