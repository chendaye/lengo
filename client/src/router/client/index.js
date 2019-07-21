
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
}
]
