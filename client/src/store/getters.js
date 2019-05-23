const getters = {
  // client
  client_token: state => state.client.token,
  client_avatar: state => state.client.avatar,
  client_name: state => state.client.name,
  client_remark: state => state.client.remark,
  client_email: state => state.client.email,
  // admin
  admin_sidebar: state => state.app.sidebar,
  admin_device: state => state.app.device,
  admin_token: state => state.admin.token,
  admin_avatar: state => state.admin.avatar,
  admin_name: state => state.admin.name,
  admin_remark: state => state.admin.remark,
  admin_email: state => state.admin.email
}
export default getters
