import axios from 'axios'
import {
  MessageBox,
  Message
} from 'element-ui'
import store from '@/store'
import router from '../router'
import {
  getToken,
  setToken,
  removeToken
} from '@/utils/auth'

// 封装axios

// 创建一个 axios 实例
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
  withCredentials: true, // send cookies when cross-domain requests
  timeout: 5000 // request timeout
})

/**
 * 请求拦截器
 */
service.interceptors.request.use(
  config => {
    // console.log('请求拦截' + config.url, config)
    // const url = config.url.replace(config.baseURL, '')
    // todo: api 地址
    const isAdmin = config.url.indexOf('admin') // 是否是客户端请求
    if (isAdmin > -1) {
      // 让每一个请求都有 jwt token
      config.headers['Authorization'] = 'Bearer' + getToken('admin')
    } else {
      config.headers['Authorization'] = 'Bearer' + getToken('client')
    }
    return config
  },
  error => {
    // do something with request error
    console.log(error) // for debug
    return Promise.reject(error)
  }
)

/**
 * 响应拦截
 */
service.interceptors.response.use(
  response => {
    // console.log('响应拦截' + response.config.url, response)
    // 判断一下响应中是否有 token，如果有就直接使用此 token 替换掉本地的 token。你可以根据你的业务需求自己编写更新 token 的逻辑
    let token = response.headers.authorization
    // todo: api 地址
    const isAdmin = response.config.url.indexOf('admin') // 是否是客户端请求
    if (token) {
      token = token.slice(token.indexOf('ey'))
      if (isAdmin > -1) {
        // 如果 header 中存在 token，那么触发 refreshToken 方法，替换本地的 token
        store.dispatch('admin/refreshToken', token)
      } else {
        // 如果 header 中存在 token，那么触发 refreshToken 方法，替换本地的 token
        store.dispatch('client/refreshToken', token)
      }
    }

    // return response
    const res = response.data.meta !== undefined ? response.data.meta : {
      code: 200000
    }

    // if the custom code is not 200000, it is judged as an error.
    if (res.code !== 200000) {
      Message({
        message: response.data.message || res.message || 'error',
        type: 'error',
        duration: 5 * 1000
      })

      // 500008: Illegal token; 500012: Other clients logged in; 500014: Token expired;
      if (res.code === 500008 || res.code === 500012 || res.code === 500014) {
        // to re-login
        MessageBox.confirm('You have been logged out, you can cancel to stay on this page, or log in again', 'Confirm logout', {
          confirmButtonText: 'Re-Login',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }).then(() => {
          if (!isAdmin) {
            store.dispatch('client/resetToken').then(() => {
              location.reload()
            })
          } else {
            store.dispatch('admin/resetToken').then(() => {
              location.reload()
            })
          }
        })
      }
      return Promise.reject(res.message || 'error')
    } else {
      return response
    }
  },

  /**
   * 错误拦截
   */
  error => {
    // console.log('错误拦截' + error.response.config.url, error)
    // 错误信息
    const msg = error.response.data;
    const isAdmin = error.response.config.url.indexOf('admin')
    switch (msg.status_code) {
      // 如果响应中的 http code 为 401，那么则此用户可能 token 刷新时间过期 msg.message
      case 401:
        Message({
          message: `token 验证失败：${msg.message}`,
          type: 'error',
          duration: 5 * 1000
        })
        // location.reload()
        if (isAdmin > -1) {
          store.dispatch('admin/resetToken').then(_ => {
            router.push({
              path: '/admin/login'
            })
          })
        } else {
          store.dispatch('client/resetToken').then(_ => {
            router.push({
              path: '/login'
            })
          })
        }
        break;
      case 400:
        console.log('err' + error) // for debug
        Message({
          message: msg.message,
          type: 'error',
          duration: 5 * 1000
        })
        break
      case 500:
        console.log('err' + error) // for debug
        Message({
          message: msg.message,
          type: 'error',
          duration: 5 * 1000
        })
        break
    }

    return Promise.reject(error)
  }
)

export default service
