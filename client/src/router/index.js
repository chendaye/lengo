import Vue from 'vue'
import Router from 'vue-router'

// 管理后台路由
import adminRoutes from './admin/index'
// 客户端路由
import clientRoutes from './client/index'

Vue.use(Router)

// 客户端与后台路由路由
export const CARouters = [...adminRoutes, ...clientRoutes]

const createRouter = () => new Router({
  mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: CARouters
})

const router = createRouter()

// 重置路由
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher
}

export default router
