<template>
  <div id="home" v-loading="loading">
    <article-card v-for="(article, index) in articleList" :key="index" :article="article" />
    <!-- 分页 -->
    <div v-show="total > 0" class="pagination">
      <pagination
        v-show="total>0"
        :total="total"
        :page.sync="listQuery.page"
        :limit.sync="listQuery.limit"
        @pagination="getList"
      />
    </div>
    <!-- 分页 结束 -->
    <no-data v-if="total === 0" text="没有找到文章~" />
  </div>
</template>

<script>
// import { scroll } from "@/layoutClient/mixin/scroll";
import Pagination from "@/components/Pagination";
import articleCard from "@/components/articleCard/articleCard";
import noData from "@/components/noData/noData";
import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", "client");

export default {
  name: "Home",
  components: {
    articleCard,
    noData,
    Pagination
  },
  // mixins: [scroll],
  data() {
    return {
      // table
      articleList: [],
      total: 0,
      loading: false,
      listQuery: {
        page: 1,
        limit: 10,
        order: {},
        where: {}
      }
    };
  },
  created() {
    this.getList();
  },
  methods: {
    getList() {
      this.loading = true;
      wtuCrud
        .get("indexArticle", this.listQuery)
        .then(data => {
          if (data.status === 200 && data.data.total > 0) {
            this.total = data.data.total;
            this.articleList = data.data.data;
            this.loading = false;
          }
        })
        .catch(() => {
          this.articleList = [];
          this.loading = false;
        });
    }
  }
};
</script>

<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../../stylus/color.styl';

#home {
  position: relative;
  padding: 30px 10px;
  min-height: 100px;

  .pagination {
    width: 100%;
    padding: 10px 0;
    display: flex;
    display: -webkit-flex;
    flex-direction: row;
    justify-content: center;
    background-color: $color-white;
  }
}

.slide-fade-enter, .slide-fade-leave-to {
  opacity: 0;
}
</style>
