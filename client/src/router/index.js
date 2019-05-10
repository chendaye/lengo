import Vue from 'vue'
import Router from 'vue-router'

import adminRoutes from './admin'

Vue.use(Router)

// 前后台路由
const routes = [...adminRoutes]

// 静态
const staticRouter = () => new Router({
  mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: routes
})

// 生成路由
const router = staticRouter()

//  重置路由
export function resetRouter() {
  const newRouter = staticRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router

/**
 * asyncRoutes
 * the routes that need to be dynamically loaded based on user roles
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

