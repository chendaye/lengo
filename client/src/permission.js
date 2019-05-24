import router from './router'
import store from './store'
import {
  Message
} from 'element-ui'
import NProgress from 'nprogress' // 进度条
import 'nprogress/nprogress.css' // 进度条样式
import {
  getToken
} from '@/utils/auth' // 从cookie中获取token

import getPageTitle from '@/utils/get-page-title'

// 进度条配置
NProgress.configure({
  showSpinner: false
})

// 白名单
const whiteList = ['/admin/login', '/admin/register', '/client/login', '/client/register']
// 路由前置守卫
router.beforeEach(async(to, from, next) => {
  console.log(to.path, from.path, next.path)
  // 进度条
  NProgress.start()

  if (to.path.startsWith('/admin')) {
    // 设置页面标题
    document.title = getPageTitle(to.meta.title)

    // 检查token
    const hasToken = getToken('admin')

    // token 存在
    if (hasToken) {
      if (to.path === '/admin/login') {
        // 已经登录直接跳转到主页
        next({
          path: '/admin'
        })
        NProgress.done()
      } else {
        // 检查store是否有可访问动态路由
        const hasRoles = store.getters.roles && store.getters.roles.length > 0
        if (hasRoles) {
          next()
        } else {
          // 没有
          try {
            // 获取用户信息
            // note: roles 必须是数组! 如: ['admin'] or ,['developer','editor']
            const {
              roles
            } = await store.dispatch('admin/getInfo')

            // 基于角色生成动态路由
            const accessRoutes = await store.dispatch('routers/generateRoutes', roles)

            // 添加动态路由
            router.addRoutes(accessRoutes)

            // 确保路由生成完成
            // 设置 replace: true, 侧边栏将不会留下历史记录
            next({
              ...to,
              replace: true
            })
          } catch (error) {
            // 删掉token 回到登录页面
            await store.dispatch('user/resetToken')
            Message.error(error || 'Has Error')
            next(`/admin/login?redirect=${to.path}`)
            NProgress.done()
          }
        }
      }
    } else {
      // 没有token
      if (whiteList.indexOf(to.path) !== -1) {
        // 访问的路由在白名单之内 直接访问
        next()
      } else {
        // 不在白名单被，定向到登录页面.
        next(`/admin/login?redirect=${to.path}`)
        NProgress.done()
      }
    }
  } else if (to.path.startsWith('/client')) {
    // 前端路由
  } else {
    // 既不是前端路由 也不是后端路由
    next({
      path: '/404'
    })
  }
})

router.afterEach(() => {
  NProgress.done()
})
