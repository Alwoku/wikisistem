import './bootstrap';
import './jquery.js';

import { createApp } from 'vue';
import Toaster from "@meforma/vue-toaster";

import VueMobileDetection from "vue-mobile-detection";

import vSelect from 'vue-select'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import breadcrumbs from 'vue-3-breadcrumbs'
import { createPinia } from 'pinia';

import router from './router';
import BasePage from './base.vue';

import naive from 'naive-ui';
import VueDiff from 'vue-diff';

import 'vue-diff/dist/index.css';
import 'vue-select/dist/vue-select.css';


const pinia = createPinia();

// Инициализация Vue 

const app = createApp(BasePage);


app.use(pinia);
app.use(router);
app.use(naive);
app.use(Toaster);
app.use(VueMobileDetection);
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});

app.use(breadcrumbs)
app.use(VueDiff);
  
import Editor from './components/modules/md-editor-component.vue';
import elementGroup from './components/modules/elements-groups.vue';


app.component("md-editor-component", Editor)
.component('v-select', vSelect)
.component('elements-groups', elementGroup)


app.mount("#app")

