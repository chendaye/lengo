import Vue from 'vue'
import Vuex from 'vuex'

// 跟级别的 getters actions mutations
import getters from './getters'
import actions from './actions'
import mutations from './mutations'

// 不同的 store 模块
import app from './admin/base/app'
import settings from './admin/base/settings'
import user from './admin/base/user'

Vue.use(Vuex)

// 组装模块并导出 store
const store = new Vuex.Store({
  modules: {
    app,
    settings,
    user
  },
  getters,
  actions,
  mutations
})

export default store
