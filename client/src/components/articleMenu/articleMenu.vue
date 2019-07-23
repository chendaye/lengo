<template>
  <ul class="article-menu">
    <li v-for="item in menu" :key="item.tag">
      <span :class="{'active': is(item) || isParent(item)}" @click="toMenu(item)">{{ item.tag }} {{ item.title }}</span>
      <article-menu v-show="isParent(item)" :menu="item.children" />
      <!-- <article-menu :menu="item.children" /> -->
    </li>
  </ul>
</template>
<script>
import {
  mapActions,
  mapGetters
} from 'vuex'
import articleMenu from '@/components/articleMenu/articleMenu.vue'
import { scroll } from '@/layoutClient/mixin/scroll'

export default {
  name: 'ArticleMenu',
  components: {
    articleMenu
  },
  mixins: [scroll],
  props: ['menu'],
  computed: {
    ...mapGetters([
      'articleMenuTag'
    ])
  },
  methods: {
    toMenu(item) {
      let top = document.getElementById(item.id).getBoundingClientRect().top
      top += document.body.scrollTop || document.documentElement.scrollTop
      this.scrollToTarget(top)
    },
    is(item) {
      return item.tag === this.articleMenuTag
    },
    isParent(item) {
      return this.articleMenuTag.indexOf(item.tag) === 0
    }
  }
}
</script>
<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../stylus/color.styl'
.article-menu
  width: 100%
  line-height: 1.8
  font-weight: bold
  font-size: 14px
  color: #999
  > li
    margin-left: 10px
    > span
      cursor: pointer
      transition: all .3s
      border-bottom: 1px solid #555555
      &.active
      &:hover
        color: $color-white
</style>

