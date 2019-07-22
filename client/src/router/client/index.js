import Layout from '@/layoutClient'
export default [
  // 前端登录
  {
    path: '/client/login',
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
    path: '/client/register',
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
    path: '/client',
    component: Layout,
    redirect: 'dashboard',
    children: [{
      path: 'dashboard',
      name: 'Home',
      component: () => import('@/views/client/dashboard/index'),
      hidden: true,
      meta: {
        title: 'Dashboard',
        icon: 'home',
        noCache: true,
        affix: true
      }
    }]
  },
  // 404
  {
    path: '/client/404',
    component: () => import('@/views/client/404'),
    hidden: true
  }
]
