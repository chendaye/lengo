import {
  markdown
} from '@/utils/markdown'
var mark = {
  data() {
    return {
      markdownContent: `# 关于我
**一个不想做程序员的程序员**
# 我的梦想
**吃饭睡觉打豆豆**
# 学历
**Postgraduate students of Wuhan University**
# 网站初衷
1. 记录学习中遇到的问题，心得
2. 记录新的知识
3. 还没想好。。。。
# 技术栈
## 后台
- 开发框架：Laravel + Vue
- 页面开发：ElementUI+Vuex+Axios+vue-router
- API开发：Dingo+JWT
## 客户端
- 页面开发： Vue+ElementUI
## 已经实现的功能
### 后端
- 添加管理员
- 文章分类管理
- 文章标签管理
- 外链管理
- 文章创建与管理以及简单搜索
### 客户端
- 文章列表
- 文章搜索
- 友联链接
- 文章归档
## 计划实现的功能
- 分布式全文检索 ElasticSearch
- 缓存： Redis+Memcache
- 日志文件存储：MongoDB
- 还有....想到再写....

# 后期添加的模块
**和媳妇儿的美好生活**
# 还有
*暂时就这么多*`
    }
  },
  methods: {
    // 把markdown转化为 html
    markdownHtml(str) {
      return markdown(str)
    }
  }
}

export {
  mark
}
