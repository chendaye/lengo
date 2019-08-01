<template>
  <div id="archives" v-loading="loading">
    <div class="archives-wrap">
      <article-card2 v-for="(article, index) in articleList" :key="index" :article="article" />
    </div>
    <!-- 分页 -->
    <div v-show="total > 0" class="pagination">
      <el-pagination
        background
        layout="prev, pager, next"
        :page-size="pageSize"
        :current-page="currentPage"
        :total="total"
        @current-change="pageChange"
      />
    </div>
    <!-- 分页 结束 -->
    <no-data v-if="total === 0" text="没有找到文章~" />
  </div>
</template>

<script>
import { son } from "@/utils/index";
import { scroll } from "@/layoutClient/mixin/scroll";
import articleCard2 from "@/components/articleCard/articleCard2";
import noData from "@/components/noData/noData";
import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", "client");

export default {
  name: "Archives",
  components: {
    articleCard2,
    noData
  },
  mixins: [scroll],
  props: {
    tag: {
      type: Array,
      default: function() {
        return [];
      }
    },
    category: {
      type: Array,
      default: function() {
        return [];
      }
    },
    title: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
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
  watch: {
    $route(route) {
      this.getList();
    },
    title: function() {
      this.listQuery.where.title = this.title;
      this.handleFilter();
    },
    category: function() {
      if (this.category !== []) {
        let cate = [];
        // 分类
        cate = son(this.category);
        this.listQuery.where.category = cate;
        this.handleFilter();
      }
    },
    tag: function() {
      console.log('tag', this.tag);
      this.listQuery.where.tag = this.tag;
      this.handleFilter();
    }
  },
  created() {
    this.getList();
  },
  methods: {
    // table
    getList() {
      this.listLoading = true;
      wtuCrud.get("articleList", this.listQuery).then(res => {
        this.articleList = res.data.data;
        this.total = res.data.total;
        this.listLoading = false;
      });
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    }
  }
};
</script>

<style lang="stylus" scoped>
@import '../../../stylus/color.styl';

#archives {
  position: relative;
  padding: 30px 10px;
  .pagination {
    width: 100%;
    padding: 10px 0;
    display: flex;
    display: -webkit-flex;
    flex-direction: row;
    justify-content: center;
    background-color: $color-white;
  }

  .archives-wrap {
    position: relative;
    width: 100%;
    padding: 10px 30px;
    animation: show 0.8s;

    @media (max-width: 768px) {
      padding: 5px;
    }

    .time-line {
      position: absolute;
      left: 30px;

      @media (max-width: 768px) {
        left: 5px;
      }

      top: 15px;
      bottom: 0;
      width: 2px;
      background-color: #eeeeee;
    }
  }
}

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
