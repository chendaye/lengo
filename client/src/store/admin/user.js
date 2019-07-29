import {
  login,
  logout,
  me
} from '@/api/admin'
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
  id: null,
  token: getToken(),
  name: '',
  email: '',
  remark: '',
  avatar: '',
  auth: false
}

// 设置 state 内容（同步）
const mutations = {
  // 在 state 中保存 token
  SET_ID: (state, id) => {
    state.id = id
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_EMAIL: (state, email) => {
    state.email = email
  },
  SET_REMARK: (state, remark) => {
    state.remark = remark
  },
  SET_AUTH: (state, auth) => {
    state.auth = auth
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
      name,
      password
    } = userInfo
    // 登录
    return new Promise((resolve, reject) => {
      // 登录 api/client/login
      login({
        name: name.trim(),
        password: password
      }).then(response => {
        // 登录成功返回数据
        const {
          data
        } = response
        // 保存 token
        commit('SET_TOKEN', data.access_token)
        commit('SET_AUTH', true)
        setToken(data.access_token, 'admin')
        resolve(response)
      }).catch(error => {
        reject(error)
      })
    })
  },

  // 获取用户信息
  me({
    commit,
    state
  }) {
    return new Promise((resolve, reject) => {
      // /client/me
      me(state.token).then(response => {
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
          id,
          name,
          email,
          remark,
          avatar
        } = data.user
        // 调用mutations 设置 state
        commit('SET_ID', id)
        commit('SET_NAME', name)
        commit('SET_AVATAR', avatar)
        commit('SET_EMAIL', email)
        commit('SET_REMARK', remark)
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
      // /client/logout
      logout(state.token).then(() => {
        // state 中 token 置为空
        commit('SET_TOKEN', null)
        commit('SET_AUTH', false)
        // cookie 中 token 删除
        removeToken('admin')
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
      commit('SET_TOKEN', null)
      removeToken('admin')
      resolve()
    })
  },

  // 刷新token
  refreshToken({
    commit
  }, token) {
    return new Promise(resolve => {
      commit('SET_TOKEN', token)
      setToken(token, 'admin')
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
