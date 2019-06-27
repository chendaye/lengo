import axios from 'axios'
import {
  MessageBox,
  Message
} from 'element-ui'
import store from '@/store'
import {
  getToken
  // setToken
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
    console.log('请求拦截', config)
    const url = config.url.replace(config.baseURL, '')
    const isClient = url.startsWith('/client') // 是否是客户端请求
    if (isClient) {
      // 让每一个请求都有 jwt token
      config.headers['Authorization'] = 'Bearer' + getToken('client')
    } else {
      config.headers['Authorization'] = 'Bearer' + getToken('admin')
    }
    // console.log(config.headers['Authorization'])
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
    console.log('响应拦截', response)
    // 判断一下响应中是否有 token，如果有就直接使用此 token 替换掉本地的 token。你可以根据你的业务需求自己编写更新 token 的逻辑
    const token = response.headers.authorization
    const url = response.config.url.replace(response.config.baseURL, '')
    const isClient = url.startsWith('/client') // 是否是客户端请求
    if (token) {
      if (isClient) {
        // 如果 header 中存在 token，那么触发 refreshToken 方法，替换本地的 token
        this.$store.dispatch('client/refreshToken', token)
      } else {
        // 如果 header 中存在 token，那么触发 refreshToken 方法，替换本地的 token
        this.$store.dispatch('admin/refreshToken', token)
      }
    }

    // return response
    const res = response.data.meta !== undefined ? response.data.meta : {
      code: 200000
    }

    // if the custom code is not 200000, it is judged as an error.
    if (res.code !== 200000) {
      Message({
        message: res.message || 'error',
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
          if (isClient) {
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
    console.log('错误拦截', error)

    // 错误信息
    const msg = error.response.data;

    const url = error.response.config.url.replace(error.response.config.baseURL, '')
    // error.response   error.config 请求配置和请求响应
    // 判断是客户端响应还是 后台响应
    const isClient = url.startsWith('/client')
    switch (msg.status_code) {
      // 如果响应中的 http code 为 401，那么则此用户可能 token 失效了之类的，我会触发 logout 方法，清除本地的数据并将用户重定向至登录页面
      case 401:
        Message({
          message: '令牌失效，请重新登录！',
          type: 'error',
          duration: 5 * 1000
        })
        if (isClient) {
          return this.$store.dispatch('/client/logout')
        } else {
          return this.$store.dispatch('/admin/logout')
        }
        // 如果响应中的 http code 为 400，那么就弹出一条错误提示给用户
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
