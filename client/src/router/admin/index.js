/* Layout */
import Layout from '@/layout'

// 无权限路由
export default [{
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
  path: '/example',
  component: Layout,
  redirect: '/example/table',
  name: 'Example',
  meta: {
    title: 'Example',
    icon: 'example'
  },
  children: [{
    path: 'table',
    name: 'Table',
    component: () => import('@/views/admin/dashboard/index'),
    meta: {
      title: 'Table',
      icon: 'table'
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
}
]
