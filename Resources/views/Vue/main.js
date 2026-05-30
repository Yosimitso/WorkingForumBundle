// import * as Vue from 'vue'
import App from "./App.vue";
import router from './router'
//
// new Vue({
//     router,
//     render: (h) => h(App),
// }).$mount("#app");

import { createApp } from "vue";

const app = createApp({
    render: (h) => h(App),
}).use(router);

app.mount("#app");
