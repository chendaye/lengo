import {
  login,
  logout,
  getInfo
} from '@/api/user'
import {
  getToken,
  setToken,
  removeToken
} from '@/utils/auth'
import {
  resetRouter
} from '@/router'

// 用户信息
const state = {
  token: getToken(),
  name: '',
  avatar: ''
}

// 设置 state 内容（同步）
const mutations = {
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  }
}

// 异步 action
const actions = {
  // 用户登录
  login({
    commit
  }, userInfo) {
    // 账户密码
    const {
      username,
      password
    } = userInfo
    // 登录
    return new Promise((resolve, reject) => {
      // 登录 api/user/login
      login({
        username: username.trim(),
        password: password
      }).then(response => {
        // 登录成功返回数据
        const {
          data
        } = response
        // 保存 token
        commit('SET_TOKEN', data.token)
        setToken(data.token)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // 获取用户信息
  getInfo({
    commit,
    state
  }) {
    return new Promise((resolve, reject) => {
      // api/user/getInfo
      getInfo(state.token).then(response => {
        // 返回的用户信息
        const {
          data
        } = response
        if (!data) {
          // 信息为空 重新登录
          reject('验证失败，请重新登录！')
        }
        // 名称 头像
        const {
          name,
          avatar
        } = data
        // 调用mutations 设置 state
        commit('SET_NAME', name)
        commit('SET_AVATAR', avatar)
        resolve(data)
      }).catch(error => {
        reject(error)
      })
    })
  },

  //  用户登出
  logout({
    commit,
    state
  }) {
    return new Promise((resolve, reject) => {
      // api/user/logout
      logout(state.token).then(() => {
        // token 置为空
        commit('SET_TOKEN', '')
        removeToken()
        // 重置路由
        resetRouter()
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // 移除 token
  resetToken({
    commit
  }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '')
      removeToken()
      resolve()
    })
  }
}

// 组装  store/user
export default {
  namespaced: true, // 启用命名空间
  state,
  mutations,
  actions
}
