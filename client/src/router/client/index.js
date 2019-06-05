/* Layout */
import Layout from '@/layout'

export default [{
  path: '/client/login',
  name: 'clientLogin',
  component: () => import('@/views/client/login/login'),
  hidden: true,
  meta: {
    title: 'clientLogin',
    icon: ''
  }
},
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
{
  path: 'client/404',
  component: () => import('@/views/client/404'),
  hidden: true
},
{
  path: '/client',
  component: Layout,
  redirect: '/client/dashboard',
  children: [{
    path: 'dashboard',
    name: 'clientDashboard',
    component: () => import('@/views/client/dashboard/index'),
    meta: {
      title: 'Dashboard',
      icon: 'dashboard'
    }
  }

  ]
}
]
