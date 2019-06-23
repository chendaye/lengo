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
    path: '/admin',
    component: Layout,
    redirect: 'dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/admin/dashboard/index'),
      meta: {
        title: 'Dashboard',
        icon: 'dashboard'
      }
    }

    ]
  },
  {
    path: '/admin/rbac',
    component: Layout,
    redirect: 'dashboard',
    name: 'RBAC',
    meta: {
      title: 'RBAC',
      icon: 'tree'
    },
    children: [{
      path: 'user',
      name: 'User',
      component: () => import('@/views/admin/rbac/user'),
      meta: {
        title: 'User',
        icon: 'user'
      }
    },
    {
      path: 'tree',
      name: 'Tree',
      component: () => import('@/views/admin/dashboard/index'),
      meta: {
        title: 'Tree',
        icon: 'tree'
      }
    }
    ]
  },
  {
    path: 'admin/404',
    component: () => import('@/views/admin/404'),
    hidden: true
  }
]
