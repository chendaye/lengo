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
      icon: 'home',
      noCache: true,
      affix: true
    }
  }]
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
  children: [
    // tag
    {
      path: 'tag',
      component: () => import('@/views/admin/wtu/tag/index'),
      name: 'Tag',
      meta: {
        title: '标签',
        icon: 'tag'
      },
      redirect: '/admin/wtu/tag',
      children: [{
        path: 'tagCreate',
        component: () => import('@/views/admin/wtu/tag/tagCreate'),
        name: 'Menu1-1',
        meta: {
          title: '标签',
          icon: 'tag'
        }
      }]
    },
    // 分类
    {
      path: 'category',
      component: () => import('@/views/admin/wtu/category/index'),
      name: 'Category',
      meta: {
        title: '分类',
        icon: 'plane'
      },
      redirect: '/admin/wtu/category',
      children: [{
        path: 'categoryCreate',
        component: () => import('@/views/admin/wtu/category/categoryCreate'),
        name: 'CategoryCreate',
        meta: {
          title: '分类',
          icon: 'plane'
        }
      }]
    },
    // 链接
    {
      path: 'link',
      component: () => import('@/views/admin/wtu/link/index'),
      name: 'Link',
      meta: {
        title: '链接',
        icon: 'linkm'
      },
      redirect: '/admin/wtu/link',
      children: [{
        path: 'linkManage',
        component: () => import('@/views/admin/wtu/link/linkManage'),
        name: 'LinkManage',
        meta: {
          title: '链接',
          icon: 'link'
        }
      }]
    },
    // 文章
    {
      path: 'note',
      name: 'Note',
      component: () => import('@/views/admin/wtu/article'),
      meta: {
        title: '笔记',
        icon: 'wtu'
      },
      redirect: '/admin/wtu/article',
      children: [{
        path: 'noteCreate',
        component: () => import('@/views/admin/wtu/article/write'),
        name: 'NoteCreate',
        meta: {
          title: '写笔记',
          icon: 'notebook'
        }
      },
      {
        path: 'noteManage',
        component: () => import('@/views/admin/wtu/article/manage'),
        name: 'noteManage',
        meta: {
          title: '笔记管理',
          icon: 'txt'
        }
      }
      ]
    }
  ]
},
{
  path: 'admin/404',
  component: () => import('@/views/admin/404'),
  hidden: true
}
]
