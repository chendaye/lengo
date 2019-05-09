
/* Layout */
import Layout from '@/layout'

// 无权限路由
export default [

   {
     path: "/admin",
     component: Layout,
     children: [
       {
         path: '/login',
         component: () => import('@/views/login/index'),
         hidden: true // 无侧边栏
       }
     ]
   },

  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true // 无侧边栏
  },

  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },

  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/dashboard/index'),
      meta: { title: 'Dashboard', icon: 'dashboard' }
    }]
  },

  // 404 页面 放在最后 匹配任意路由 !!!
  { path: '*', redirect: '/404', hidden: true }
]

