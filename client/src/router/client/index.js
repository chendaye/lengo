import Layout from '@/layoutClient'
export default [
  // 前端登录
  {
    path: '/login',
    name: 'clientLogin',
    component: () => import('@/views/client/login/login'),
    hidden: true,
    meta: {
      title: 'clientLogin',
      icon: ''
    }
  },
  // 前端注册
  {
    path: '/register',
    name: 'clientRegister',
    component: () => import('@/views/client/login/register'),
    hidden: true,
    meta: {
      title: 'clientLogin',
      icon: ''
    }
  },
  // 路由
  {
    path: '/',
    component: Layout,
    redirect: 'Home',
    children: [
      {
        path: 'home',
        name: 'Home',
        component: () => import('@/views/client/dashboard/index'),
        hidden: true,
        meta: {
          title: 'Home',
        }
      },
      {
        path: 'article',
        name: 'Article',
        component: () => import('@/views/client/article/index'),
        hidden: true,
        meta: {
          title: 'Article'
        }
      },
      {
        path: 'categories',
        name: 'Categories',
        component: () => import('@/views/client/categories/index'),
        hidden: true,
        meta: {
          title: 'Categories'
        }
      },
      {
        path: 'list',
        name: 'List',
        component: () => import('@/views/client/categories/list'),
        hidden: true,
        meta: {
          title: 'Categories'
        }
      },
      {
        path: 'friends',
        name: 'Friends',
        component: () => import('@/views/client/friends/index'),
        hidden: true,
        meta: {
          title: 'Friends'
        }
      },
      {
        path: 'about',
        name: 'About',
        component: () => import('@/views/client/about/index'),
        hidden: true,
        meta: {
          title: 'About'
        }
      },
      {
        path: 'search',
        name: 'Search',
        component: () => import('@/views/client/search/index'),
        hidden: true,
        meta: {
          title: 'Search'
        }
      },
      {
        path: 'write',
        name: 'Write',
        component: () => import('@/views/admin/wtu/article/write'),
        hidden: true,
        meta: {
          title: 'Write'
        }
      },
      {
        path: 'archives',
        name: 'Archives',
        component: () => import('@/views/client/archives/index'),
        hidden: true,
        meta: {
          title: 'Archives'
        }
      }
    ]
  },
  // 404
  {
    path: '/404',
    component: () => import('@/views/client/404'),
    hidden: true
  }
]
