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
