import { Dialog, Notify, Quasar } from 'quasar'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import "@fontsource/roboto"
import 'quasar/dist/quasar.css'
import 'material-icons/iconfont/material-icons.css'
import '../css/app.css'
import store from './store'

createInertiaApp({
  resolve: name => require(`./pages/${name}`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(store)
      .use(Quasar, {
        plugins: [
          Notify,
          Dialog
        ],
        config: {
          notify: {}
        }
      })
      .mount(el)
  },
})
