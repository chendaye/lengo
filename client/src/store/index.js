import Vue from 'vue'
import Vuex from 'vuex'

// 跟级别的 getters actions mutations
import getters from './getters'
import actions from './actions'
import mutations from './mutations'

// 后台的 store 模块
import app from './admin/app'
import settings from './admin/settings'
import admin from './admin/user'
import routers from './admin/routers'

// 前台的
import client from './client/user'
import blog from './client/blog'

Vue.use(Vuex)

// 组装模块并导出 store
const store = new Vuex.Store({
  modules: {
    // 后台 store
    app, // app:app 命名空间就是 app
    settings,
    admin,
    routers,
    // 客户端 store
    client,
    blog
  },
  getters,
  actions,
  mutations
})
export default store
