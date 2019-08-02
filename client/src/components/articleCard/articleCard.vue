<template>
  <div id="article-card">
    <div class="article-card-wrap">
      <div
        class="article-cover"
        :style="{
          backgroundImage: 'url(' + baseApi + getCover + ')'
        }"
      >
        <div class="article-title">
          <span @click="showArticle">{{ article.title }}</span>
        </div>
      </div>
      <div class="article-info">
        <i class="iconfont icon-calendar" />
        发表于 {{ article.created_at }} •
        <!-- <i class="iconfont icon-folder" />
        <span class="classify" @click="toList('category', article.id)">{{ article.title }}</span> • -->
        <i class="iconfont icon-eye" />
        {{ article.view }}次浏览
      </div>
      <div class="article-sub-message">{{ article.abstract }}</div>
      <div v-if="tags.length > 0" class="tags">
        <div
          v-for="(tag, index) in tags"
          :key="index"
          class="tag"
          @click="toList('tag', tag.id)"
        >
          <i class="iconfont icon-tag" />
          {{ tag.tag }}
        </div>
      </div>
      <div class="read-more" @click="showArticle">阅读全文 >></div>
    </div>
  </div>
</template>

<script>
import { toPath } from '@/views/client/mixins/blog'
import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", "client");
export default {
  name: 'ArticleCard',
  mixins: [toPath],
  props: ['article'],
  data() {
    return {
      baseApi: process.env.VUE_APP_PIC,
      defaultCover: require("@/assets/logo.jpg"),
      tags: []
    }
  },
  computed: {
    getCover() {
      return this.article.cover ? this.article.cover : this.defaultCover;
    }
  },
  created() {
    wtuCrud.get('tags', { article_id: this.article.id }).then(res => {
      if (res.status === 200) {
        this.tags = res.data.data.list;
      }
    });
  },
  methods: {
  }
}
</script>
<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../stylus/color.styl'
#article-card
  position: relative
  background-color: $color-white
  padding: 20px
  margin-bottom: 20px
  &:last-child
    margin-bottom: 0px
  box-shadow: 0px 0px 5px 0px rgba(38, 42, 48, .1)
  min-height: 603px
  @media (max-width: 768px)
    min-height: 285.5px
    padding: 10px
  line-height: 1.2
  animation: show .8s
  .article-card-wrap
    position: relative
    .article-cover
      position: relative
      width: 100%
      background-position: center
      background-size: cover
      &:before
        top: 0
        left: 0
        width: 100%
        padding-top: 50%
        content: ' '
        background: rgba(0, 0, 0, .3)
        display: block
      .article-title
        position: absolute
        font-size: 24px
        width: 100%
        height: 100%
        top: 0
        left: 0
        @media (max-width: 768px)
          font-size: 18px
        font-weight: bold
        color: $color-white
        display: flex
        align-items: center
        justify-content: center
        padding: 10px
        span
          position: relative
          cursor: pointer
          -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
          &:after
            content: ""
            position: absolute
            bottom: 0px
            left: 0
            width: 100%
            height: 2px
            background-color: $color-white
            visibility: hidden
            transform: scaleX(0)
            transition-duration: .2s
            transition-timing-function: ease
          &:hover
            &:after
              visibility: visible
              transform: scaleX(1)
              transition-duration: .2s
              transition-timing-function: ease

    .article-info
      font-size: 14px
      @media (max-width: 768px)
        font-size: 12px
      margin: 20px 0px
      color: #666666
      display: flex
      flex-direction: row
      justify-content: center
      flex-wrap: wrap
      .classify
        color: #444444
        border-bottom: 1px solid $color-main
        cursor: pointer
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
        margin-right: 5px
        &:hover
          color: $color-main
      .iconfont
        font-size: 14px
        @media (max-width: 768px)
          font-size: 12px
        margin: 0 5px
        &:first-child
          margin-left: 0
    .article-sub-message
      color: #666666
      border-left: 2px solid #666666
      padding-left: 5px
      font-size: 16px
      @media (max-width: 768px)
        font-size: 14px
    .read-more
      position: relative
      display: inline-block
      font-size: 14px
      margin-top: 20px
      @media (max-width: 768px)
        font-size: 12px
      cursor: pointer
      -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
      &:after
        content: ""
        position: absolute
        bottom: -2px
        left: 0
        width: 100%
        height: 1px
        background-color: $color-main
        visibility: visible
        transform: scaleX(1)
        transition-duration: .2s
        transition-timing-function: ease
      &:hover
        &:after
          visibility: hidden
          transform: scaleX(0)
          transition-duration: .2s
          transition-timing-function: ease
    .tags
      width: 100%
      padding: 10px 0px
      display: flex
      flex-direction: row
      align-items: center
      flex-wrap: wrap
      border-bottom: 1px solid #eeeeee
      margin-bottom: 10px
      .tag
        color: $color-white
        padding: 5px
        background-color: $color-main
        font-size: 12px
        margin-right: 5px
        border-radius: 5px
        transition: all .3s
        position: relative
        margin-left: 10px
        margin-top: 10px
        line-height: 1
        cursor: pointer
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
        &:hover
          &:before
            border-right: 12px solid lighten($color-main, 20%)
          background-color: lighten($color-main, 20%)
        &:before
          position: absolute
          left: -9px
          top: 0
          width: 0
          height: 0
          content: ""
          border-top: 11px solid transparent
          border-bottom: 11px solid transparent
          border-right: 12px solid $color-main
          transition: all .3s
        .iconfont
          font-size: 12px

@keyframes show {
  from {
    margin-top: -10px;
    opacity: 0;
  }
  to {
    margin-top: 0px;
    opacity: 1;
  }
}
</style>
