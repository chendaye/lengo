/* Layout */
import Layout from '@/layout'

// 无权限路由
export default [{
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
      icon: 'home'
    }
  }

  ]
},
{
  // RBAC 管理
  path: '/admin/rbac',
  component: Layout,
  redirect: 'dashboard',
  name: 'RBAC',
  meta: {
    title: 'RBAC',
    icon: 'rbac'
  },
  children: [{
    path: 'user',
    name: 'User',
    component: () => import('@/views/admin/rbac/user'),
    meta: {
      title: '管理员',
      icon: 'admin'
    }
  },
  {
    path: 'tree',
    name: 'Tree',
    component: () => import('@/views/admin/dashboard/index'),
    meta: {
      title: 'Tree',
      icon: 'write'
    }
  }
  ]
},
{
  // 知识库
  path: '/admin/wtu',
  component: Layout,
  redirect: 'dashboard',
  name: 'WTU',
  meta: {
    title: 'WTU',
    icon: 'lengo'
  },
  children: [{
    path: 'note',
    name: 'Note',
    component: () => import('@/views/admin/wtu/tag'),
    meta: {
      title: '笔记',
      icon: 'notebook'
    }
  },
  {
    path: 'tag',
    name: 'Tag',
    component: () => import('@/views/admin/wtu/tag'),
    meta: {
      title: '标签',
      icon: 'tag'
    }
  },
  {
    path: 'classify',
    name: 'Classify',
    component: () => import('@/views/admin/wtu/tag'),
    meta: {
      title: '分类',
      icon: 'plane'
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
