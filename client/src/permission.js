import router from './router'
import store from './store'
import { Message } from 'element-ui'
import NProgress from 'nprogress' // 进度条
import 'nprogress/nprogress.css' // 进度条样式
import { getToken } from '@/utils/auth' // 从cookie中获取 token
import getPageTitle from '@/utils/get-page-title'

NProgress.configure({ showSpinner: false }) // 进度条配置

const whiteList = ['/admin/login', '/login'] // 无需重定向（权限检查）的路由

router.beforeEach(async(to, from, next) => {
  // 开始进度条
  NProgress.start()

  // 设置页面标题
  document.title = getPageTitle(to.meta.title)

  // 检查token 用户是否登录
  const hasToken = getToken()

  // 已经登录
  if (hasToken) {
    if (to.path === '/admin/login') {
      // 如果是访问登录页面 重定向到 /
      next({ path: '/admin' })
      NProgress.done() // 进度条结束
    } else {
      // 如果是访问其他路由 从store中取用户信息
      const hasGetUserInfo = store.getters.name
      if (hasGetUserInfo) {
        next()
      } else {
        try {
          // 获取用户信息
          await store.dispatch('user/getInfo')

          next()
        } catch (error) {
          // 删除token 并重新登录
          await store.dispatch('user/resetToken')
          Message.error(error || 'Has Error')
          next(`/login?redirect=${to.path}`)
          NProgress.done()
        }
      }
    }
  } else {
    // 没有登录，没有token
    if (whiteList.indexOf(to.path) !== -1) {
      // 在白名单中 直接访问
      next()
    } else {
      // 如果不在白名单中， 重定向到登录页面
      next(`/admin/login?redirect=${to.path}`)
      NProgress.done()  // 开启进度条
    }
  }
})

router.afterEach(() => {
  // 进度条结束
  NProgress.done()
})
