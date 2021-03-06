<template>
  <div id="archives" v-loading="loading">
    <div class="archives-wrap">
      <div class="block">
        <el-date-picker
          v-model="time"
          type="daterange"
          align="right"
          unlink-panels
          value-format="yyyy-MM-dd"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
          :picker-options="pickerOptions"
        />
      </div>
      <div class="time-line" />
      <div class="list-content">
        <p class="normal-node">目前共计 {{ total }} 篇文章~</p>
        <div v-for="(year, key, index) in articleList" :key="index" class="bold-node">
          <p>{{ key }}</p>
          <div v-for="(month, key, index) in year" :key="index" class="bold-node month">
            <p>{{ key }}</p>
            <article-card2 v-for="(article, index) in month" :key="index" :article="article" />
          </div>
        </div>
      </div>
    </div>
    <no-data v-if="total === 0" text="没有找到文章~" />
  </div>
</template>

<script>
import { mapActions } from "vuex";

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
  data() {
    return {
      articleList: [],
      total: 0,
      loading: false,
      // 日期选择
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          }
        }]
      },
      time: ''
    };
  },
  watch: {
    time: function() {
      this.getList();
    }
  },
  created() {
    this.getList();
  },
  methods: {
    ...mapActions(["getBlogArticleArchives"]),
    pageChange(currentPage) {
      this.scrollToTarget(0, false);
      this.page = currentPage - 1;
      this.currentPage = currentPage;
      this.getList();
    },
    // table
    getList() {
      this.loading = true;
      wtuCrud.get("archives", {
        time: this.time
      }).then(res => {
        if (res.data.status === true) {
          this.total = res.data.data.total;
          this.articleList = res.data.data.blog;
          this.loading = false;
        }
      });
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

    .list-content {
      .normal-node {
        position: relative;
        color: #555555;
        padding: 0 15px;
        font-size: 16px;
        margin-bottom: 20px;

        @media (max-width: 768px) {
          font-size: 14px;
        }

        &:before {
          position: absolute;
          left: -4px;
          top: 5px;
          content: '';
          width: 10px;
          height: 10px;
          border-radius: 10px;
          background-color: #999999;

          @media (max-width: 768px) {
            left: -3px;
            top: 4px;
            width: 8px;
            height: 8px;
            border-radius: 8px;
          }
        }
      }

      .bold-node {
        position: relative;
        color: #555555;
        padding: 0 15px;
        font-size: 28px;
        margin-bottom: 20px;

        @media (max-width: 768px) {
          font-size: 22px;
        }

        &:before {
          position: absolute;
          left: -4px;
          top: 10px;
          content: '';
          width: 10px;
          height: 10px;
          border-radius: 10px;
          background-color: #999999;

          @media (max-width: 768px) {
            left: -3px;
            top: 8px;
            width: 8px;
            height: 8px;
            border-radius: 8px;
          }
        }

        > p {
          margin-bottom: 20px;
        }

        .month {
          color: #666666;
          font-size: 26px;

          @media (max-width: 768px) {
            font-size: 20px;
          }

          &:before {
            left: -19px;

            @media (max-width: 768px) {
              left: -18px;
            }
          }
        }
      }
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
