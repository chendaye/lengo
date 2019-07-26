<template>
  <div v-loading="loading" class="infinite-list-wrapper" style="overflow:auto;">
    <ul
      v-infinite-scroll="load"
      class="list"
      infinite-scroll-disabled="disabled"
    >
      <li
        v-for="(article, index) in articleList"
        :key="index"
        class="list-item"
      >
        <article-card :article="article" />
      </li>
    </ul>
    <p v-if="noMore">没有更多了</p>
    <no-data v-if="total === 0" text="没有找到文章~" />
  </div>
</template>

<script>
import { scroll } from '@/layoutClient/mixin/scroll';
import articleCard from '@/components/articleCard/articleCard';
import noData from '@/components/noData/noData';
import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", 'client');

export default {
  name: 'Home',
  components: {
    articleCard,
    noData
  },
  mixins: [scroll],
  data() {
    return {
      // table
      articleList: [],
      count: 10,
      total: 0,
      loading: false,
      listQuery: {
        page: 1,
        limit: 10,
        order: {},
        where: {}
      }
    }
  },
  computed: {
    noMore() {
      return this.count >= 20
    },
    disabled() {
      return this.loading || this.noMore
    }
  },
  methods: {
    pageChange(currentPage) {
      this.scrollToTarget(0, false)
      this.page = currentPage - 1
      this.currentPage = currentPage
      this.getList()
    },
    load() {
      this.loading = true
      wtuCrud.get("indexArticle", this.listQuery).then((data) => {
        if (data.data.data.length > 0) {
          this.total = data.count
          this.articleList = data.data.data
          this.loading = false
        }
      })
        .catch(() => {
          this.articleList = []
          this.loading = false
        })
    }
  }
}
</script>

<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../../stylus/color.styl'
#home
  position: relative
  padding: 30px 10px
  min-height: 100px
  .pagination
    width: 100%
    padding: 10px 0
    display: flex
    display: -webkit-flex
    flex-direction: row
    justify-content: center
    background-color: $color-white

.slide-fade-enter
.slide-fade-leave-to
  opacity: 0
</style>
