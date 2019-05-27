/* Layout */
import Layout from '@/layout'

// 无权限路由
export default [
  {
    path: '/admin/login',
    name: 'adminLogin',
    component: () => import('@/views/admin/login/index'),
    hidden: true,
    meta: {
      title: 'adminLogin',
      icon: ''
    }
  },
  {
    path: 'admin/404',
    component: () => import('@/views/admin/404'),
    hidden: true
  },
  {
    path: '/admin',
    component: Layout,
    redirect: '/admin/dashboard',
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/views/admin/dashboard/index'),
        meta: {
          title: 'Dashboard',
          icon: 'dashboard'
        }
      },
      {
        path: '/login',
        component: () => import('@/views/admin/login/index')
      }

    ]
  }
]
