const getters = {
  // client
  client_token: state => state.client.token,
  client_avatar: state => state.client.avatar,
  client_id: state => state.client.id,
  client_name: state => state.client.name,
  client_remark: state => state.client.remark,
  client_email: state => state.client.email,
  // admin
  admin_sidebar: state => state.app.sidebar,
  admin_token: state => state.admin.token,
  admin_avatar: state => state.admin.avatar,
  admin_id: state => state.admin.id,
  admin_name: state => state.admin.name,
  admin_remark: state => state.admin.remark,
  admin_email: state => state.admin.email,
  admin_roles: state => state.admin.roles,
  permission_routes: state => state.routers.routes,
  // app
  device: state => state.app.device

}
export default getters
