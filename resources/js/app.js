import './bootstrap';
import { createApp } from 'vue';
import PaymentCheck from './components/PaymentCheck.vue';
import '../css/app.css'
const app = createApp({});

app.component('PaymentCheck', PaymentCheck);

app.mount("#app");