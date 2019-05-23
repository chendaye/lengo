import Cookies from 'js-cookie'

const ClientToken = 'client_token'
const AdminToken = 'admin_token'

// 获取token
export function getToken(type = 'admin') {
  if (type === 'client') {
    return Cookies.get(ClientToken)
  } else {
    return Cookies.get(AdminToken)
  }
}

// 保存token
export function setToken(token, type = 'admin') {
  if (type === 'client') {
    return Cookies.set(ClientToken, token)
  } else {
    return Cookies.set(AdminToken, token)
  }
}

// 删除token
export function removeToken(type = 'admin') {
  if (type === 'client') {
    return Cookies.remove(ClientToken)
  } else {
    return Cookies.remove(AdminToken)
  }
}
