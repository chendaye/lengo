import Vue from 'vue'
import Router from 'vue-router'

import adminRoutes from './admin'

Vue.use(Router)

// 前后台路由
export const constantRoutes = [...adminRoutes]

/**
 * asyncRoutes
 * 基于用户角色自动追加的动态路由
 */
export const asyncRoutes = [
  {
    path: 'external-link',
    component: '',
    children: [{
      path: 'https://panjiachen.github.io/vue-element-admin-site/#/',
      meta: {
        title: 'External Link',
        icon: 'link'
      }
    }]
  },

  // 404 page must be placed at the end !!!
  {
    path: '*',
    redirect: '/404',
    hidden: true
  }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: constantRoutes
})

const router = createRouter()

// 重置路由 see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher
}

export default router
