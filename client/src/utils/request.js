import axios from 'axios'
import { MessageBox, Message } from 'element-ui'
import store from '@/store'
import {
  getToken,
  setToken
} from '@/utils/auth'

// 封装axios

// 创建一个 axios 实例
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
  withCredentials: true, // send cookies when cross-domain requests
  timeout: 5000 // request timeout
})

// 请求拦截器
service.interceptors.request.use(
  config => {
    // do something before request is sent
    if (store.getters.client_token && config.url.startsWith('/client')) {
      // 让每一个请求都有token
      // ['X-Token'] is a custom headers key
      // please modify it according to the actual situation
      config.headers['X-Token'] = getToken('client')
    } else {
      config.headers['X-Token'] = getToken('admin')
    }
    return config
  },
  error => {
    // do something with request error
    console.log(error) // for debug
    return Promise.reject(error)
  }
)

// 响应拦截器
service.interceptors.response.use(
  /**
   * If you want to get http information such as headers or status
   * Please return  response => response
  */

  /**
   * Determine the request status by custom code
   * Here is just an example
   * You can also judge the status by HTTP Status Code
   */
  response => {
    // 判断一下响应中是否有 token，如果有就直接使用此 token 替换掉本地的 token。你可以根据你的业务需求自己编写更新 token 的逻辑
    const token = response.headers.authorization
    const isClient = response.config.url.startsWith('/client') // 是否是客户端请求
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

    const res = response.data

    // if the custom code is not 20000, it is judged as an error.
    if (res.code !== 20000) {
      Message({
        message: res.message || 'error',
        type: 'error',
        duration: 5 * 1000
      })

      // 50008: Illegal token; 50012: Other clients logged in; 50014: Token expired;
      if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
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
  error => {
    // error.response   error.config 请求配置和请求响应
    // 判断是客户端响应还是 后台响应
    const isClient = error.response.url.startsWith('/client')
    switch (error.response.status) {
      // 如果响应中的 http code 为 401，那么则此用户可能 token 失效了之类的，我会触发 logout 方法，清除本地的数据并将用户重定向至登录页面
      case 401:
        if (isClient) {
          return this.$store.dispatch('/client/logout')
        } else {
          return this.$store.dispatch('/admin/logout')
        }
        break
        // 如果响应中的 http code 为 400，那么就弹出一条错误提示给用户
      case 400:
        console.log('err' + error) // for debug
        Message({
          message: error.response.data.error,
          type: 'error',
          duration: 5 * 1000
        })
        break
    }

    return Promise.reject(error)
  }
)

export default service
