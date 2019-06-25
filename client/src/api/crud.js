import request from '@/utils/request';

export default {
  factory: (module, clientAdmin = 'admin') => {
    const common = {
      get: (action, params) => {
        return request({
          url: `${clientAdmin}/${module}/${action}`,
          method: 'get',
          params: params
        });
      },
      post: (action, data) => {
        return request({
          url: `${clientAdmin}/${module}/${action}`,
          method: 'post',
          data: data
        });
      }
    }
    return {
      get: common.get,
      post: common.post,
      getList: (where = [], page = 1, limit = 10, order = []) => {
        return common.get('index', {
          where: where,
          page: page,
          limit: limit,
          order: order
        });
      },
      detail: (where) => {
        return common.get('detail', {
          where: where
        });
      },
      del: data => {
        return common.post('del', data);
      },
      add: data => {
        return common.post('add', data);
      },
      update: data => {
        return common.post('update', data);
      }
    };
  }
};
