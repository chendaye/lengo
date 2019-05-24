/**
 * asyncRoutes
 * 基于用户角色自动追加的动态路由
 */
export const asyncRoutes = [{
  path: 'external-link',
  component: '',
  children: [{
    path: 'https://panjiachen.github.io/vue-element-admin-site/#/',
    meta: {
      title: 'External Link',
      icon: 'link'
    }
  }]
},

// 404 page must be placed at the end !!!
{
  path: '*',
  redirect: '/404',
  hidden: true
}
]
