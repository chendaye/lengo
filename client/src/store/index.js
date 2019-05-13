import Vue from 'vue'
import Vuex from 'vuex'

// 跟级别的 getters actions mutations
import getters from './getters'
import actions from './actions'
import mutations from './mutations'

// 后台的 store 模块
import app from './admin/base/app'
import settings from './admin/base/settings'
import user from './admin/base/user'
import routers from './admin/base/routers'

// 前台的

Vue.use(Vuex)

// 组装模块并导出 store
const store = new Vuex.Store({
  modules: {
    app,
    settings,
    user,
    routers
  },
  getters,
  actions,
  mutations
})

export default store
