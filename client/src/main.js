import Vue from 'vue'

import 'normalize.css/normalize.css' // A modern alternative to CSS resets
// import 'flex.css' // 适配移动端

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import locale from 'element-ui/lib/locale/lang/zh-CN' // lang i18n

import '@/styles/index.scss' // global css

import App from './App'
import store from './store'
import router from './router'

import {
  sync
} from 'vuex-router-sync'
import Highlight from '@/utils/mHighlight'
import photoPreview from '@/components/photoPreview'

import '@/icons' // icon

// 权限控制
import '@/permission'

// 路由前置守卫
// router.beforeEach((to, from, next) => {
//   console.log(to.path, from.path, next.path)
//   next()
// })

// set ElementUI lang to EN
Vue.use(ElementUI, {
  locale
})

Vue.use(Highlight)
Vue.use(photoPreview)

sync(store, router)

Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
