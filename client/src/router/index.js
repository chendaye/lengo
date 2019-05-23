import Vue from 'vue'
import Router from 'vue-router'

// 管理后台路由
import adminRoutes from './admin'
// 
import clientRoutes from './client'

Vue.use(Router)

// 前后台路由
export const constantRoutes = [...adminRoutes, ...clientRoutes]

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

// 重置路由
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher
}

export default router
