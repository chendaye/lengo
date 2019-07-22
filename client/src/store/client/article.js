import {
  SCREEN_CHANGE, // 排名大小
  SHOW_RIGHT_NAV, // 右侧面板
  SET_ARTICLE_MENU, // 保存文章目录信息
  SET_ARTICLE_MENU_SOURCE, // 保存文章目录信息(未生成树的)
  SET_ARTICLE_MENU_TAG, // 保存当前为文章第几个目录
  SET_COMMENTS_INFO // 保存评论填写的昵称邮箱信息
} from './mutation-types'

const state = {
  screen: {
    width: 0,
    height: 0
  },
  showRightNav: false,
  articleMenu: false,
  articleMenuSource: [],
  articleMenuTag: '1.'
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
  }
}

const mutations = {
  [SCREEN_CHANGE](state, data) {
    state.screen = data
  },
  [SHOW_RIGHT_NAV](state, data) {
    state.showRightNav = data
  },
  [IS_ADMIN_WRAP](state, data) {
    state.isAdminWrap = data
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
  }
}

const actions = {
  setShowRightNav(store, show) {
    store.state.showRightNav = show
  },
  setIsAdminWrap(store, isAdminWrap) {
    store.state.isAdminWrap = isAdminWrap
  },
  setArticleMenu(store, articleMenu) {
    store.state.articleMenu = articleMenu
  },
  setArticleMenuSource(store, articleMenuSource) {
    store.state.articleMenuSource = articleMenuSource
  },
  setArticleMenuTag(store, articleMenuTag) {
    store.state.articleMenuTag = articleMenuTag
  },
  setCommentsInfo(store, commentsInfo) {
    cachedCommentsInfo.save(commentsInfo)
    store.state.commentsInfo = commentsInfo
  },
  uploadToQiniu(store, params) {
    return api.uploadToQiniu(params)
      .then((data) => {
        return Promise.resolve(data)
      })
      .catch((error) => {
        return Promise.reject(error)
      })
  }
}

export default {
  state,
  mutations,
  actions,
  getters
}
