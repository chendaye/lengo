import {
  SCREEN_CHANGE, // 屏幕大小
  SHOW_RIGHT_NAV, // 右侧面板
  SET_ARTICLE_MENU, // 保存文章目录信息
  SET_ARTICLE_MENU_SOURCE, // 保存文章目录信息(未生成树的)
  SET_ARTICLE_MENU_TAG, // 保存当前为文章第几个目录
  SET_COMMENTS_INFO, // 保存评论填写的昵称邮箱信息
  SET_BLOG_INFO
} from './mutation-types'

const state = {
  screen: {
    width: 0,
    height: 0
  },
  showRightNav: false,
  articleMenu: false,
  articleMenuSource: [],
  articleMenuTag: '1.',
  blogInfo: {
    avatar: require("@/assets/logo.jpg"),
    blogName: 'Lengo',
    sign: 'Life is only inevitable, there is no chance.',
    articleCount: 0,
    categoryCount: 0,
    tagCount: 0,
    github: 'https://github.com/chendaye/lengo'
  }
}

const getters = {
  screen(state) {
    return state.screen
  },
  showRightNav(state) {
    return state.showRightNav
  },
  articleMenu(state) {
    return state.articleMenu
  },
  articleMenuSource(state) {
    return state.articleMenuSource
  },
  articleMenuTag(state) {
    return state.articleMenuTag
  },
  blogInfo(state) {
    return state.blogInfo
  }
}

const mutations = {
  [SCREEN_CHANGE](state, data) {
    state.screen = data
  },
  [SHOW_RIGHT_NAV](state, data) {
    state.showRightNav = data
  },
  [SET_ARTICLE_MENU](state, data) {
    state.articleMenu = data
  },
  [SET_ARTICLE_MENU_SOURCE](state, data) {
    state.articleMenuSource = data
  },
  [SET_ARTICLE_MENU_TAG](state, data) {
    state.articleMenuTag = data
  },
  [SET_COMMENTS_INFO](state, data) {
    state.commentsInfo = data
  },
  // 设置blog信息
  [SET_BLOG_INFO](state, data) {
    state.blogInfo = data
  }
}

const actions = {
  setShowRightNav(store, show) {
    store.state.showRightNav = show
  },
  setArticleMenu(store, articleMenu) {
    store.state.articleMenu = articleMenu
  },
  setArticleMenuSource(store, articleMenuSource) {
    store.state.articleMenuSource = articleMenuSource
  },
  setArticleMenuTag(store, articleMenuTag) {
    store.state.articleMenuTag = articleMenuTag
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
