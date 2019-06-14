import request from '@/utils/request'

// 用户登录
export function login(data) {
  return request({
    url: '/admin/login',
    method: 'post',
    data
  })
}

// 获取用户信息
export function me(token) {
  return request({
    url: '/admin/me',
    method: 'get',
    params: {
      token
    }
  })
}

// 用户登出
export function logout() {
  return request({
    url: '/admin/logout',
    method: 'post'
  })
}
