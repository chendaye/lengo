<template>
  <div
    v-show="show"
    id="right-nav"
    :style="{
      'width': showRightNav ? '320px' : '0px',
      'transition': 'all .3s'
    }"
  >
    <!-- 内容 -->
    <div
      class="right-nav-wrap"
      :style="{
        'width': showRightNav ? '320px' : '0px',
        'transition': 'all .3s'
      }"
    >
      <!-- 内容头部 -->
      <div v-if="articleMenu" class="menu-info-head">
        <span :class="{'active': showCatalog}" @click="showCatalog = true">文章目录</span>
        |
        <span :class="{'active': !showCatalog}" @click="showCatalog = false">站点信息</span>
      </div>
      <div class="content-wrap">
        <!-- 文章菜单 -->
        <transition name="slide-fade">
          <article-menu v-show="showCatalog" class="article-menu" :menu="articleMenu" :start="0" />
        </transition>
        <!-- 博客信息 -->
        <transition name="slide-fade">
          <div v-show="!showCatalog" class="info-wrap">
            <img class="avatar" :src="blogInfo.avatar">
            <p class="name">{{ blogInfo.blogName || '博客' }}</p>
            <p class="motto">{{ blogInfo.sign || '-' }}</p>
            <div class="menu-wrap">
              <span class="menu-item" @click="toView('Archives')">
                <p class="count">{{ articleCount || 0 }}</p>
                <p>文章</p>
              </span>
              <span class="menu-item" @click="toView('Categories')">
                <p class="count">{{ categoryCount || 0 }}</p>
                <p>分类</p>
              </span>
              <span class="menu-item" @click="toView('Categories')">
                <p class="count">{{ tagCount || 0 }}</p>
                <p>标签</p>
              </span>
            </div>
            <div class="social-wrap">
              <a v-if="blogInfo.github" class="social-item" :href="blogInfo.github" target="_blank">
                <i class="iconfont icon-github" />
                github
              </a>
              <span  class="logout-item" @click="logout">Logout</span>
            </div>
          </div>
        </transition>
      </div>
    </div>
    <!-- 抽屉开关 -->
    <div class="toggle" @click="toggle" @mouseover="setLineData" @mouseout="setLineData">
      <span
        v-for="(line, index) in toggleLineData"
        :key="index"
        class="toggle-line"
        :style="{
          width: line.width,
          top: line.top,
          transform: line.transform,
          opacity: line.opacity,
          transition: 'all .3s'
        }"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", "client");
import ArticleMenu from "@/components/articleMenu/articleMenu.vue";

export default {
  name: "RightNav",
  components: {
    ArticleMenu
  },
  data() {
    return {
      show: true,
      articleCount: 0, // 文章数量
      categoryCount: 0, // 分类数量
      tagCount: 0, // 标签数量
      lineStyle: {
        normalLineData: [
          {
            width: "100%",
            top: "0px",
            transform: "rotateZ(0deg)",
            opacity: "1"
          },
          {
            width: "100%",
            top: "0px",
            transform: "rotateZ(0deg)",
            opacity: "1"
          },
          {
            width: "100%",
            top: "0px",
            transform: "rotateZ(0deg)",
            opacity: "1"
          }
        ],
        closeLineData: [
          {
            width: "100%",
            top: "6px",
            transform: "rotateZ(-45deg)",
            opacity: "1"
          },
          {
            width: "100%",
            top: "0px",
            transform: "rotateZ(0deg)",
            opacity: "0"
          },
          {
            width: "100%",
            top: "-6px",
            transform: "rotateZ(45deg)",
            opacity: "1"
          }
        ],
        arrowLineData: [
          {
            width: "50%",
            top: "3px",
            transform: "rotateZ(-45deg)",
            opacity: "1"
          },
          {
            width: "100%",
            top: "0px",
            transform: "rotateZ(0deg)",
            opacity: "1"
          },
          {
            width: "50%",
            top: "-3px",
            transform: "rotateZ(45deg)",
            opacity: "1"
          }
        ]
      },
      toggleLineData: [],
      showCatalog: false
    };
  },
  computed: {
    ...mapGetters(["screen", "showRightNav", "blogInfo", "articleMenu"])
  },
  watch: {
    screen(value) {
      this.show = true;
      if (value.width <= 990) {
        this.show = false;
      }
    },
    articleMenu(value) {
      if (value) {
        this.showCatalog = true;
        this.setShowRightNav(true);
        this.toggleLineData = this.lineStyle.closeLineData;
      } else {
        this.showCatalog = false;
        this.setShowRightNav(false);
        this.toggleLineData = this.lineStyle.normalLineData;
      }
    }
  },
  created() {
    this.toggleLineData = this.lineStyle.normalLineData;
    // 获取文章数量信息
    wtuCrud.get('number', {}).then(res => {
      if (res.status === 200) {
        this.articleCount = res.data.data.articleCount;
        this.categoryCount = res.data.data.categoryCount;
        this.tagCount = res.data.data.tagCount;
      }
    });
  },
  mounted() {},
  methods: {
    ...mapActions("blog", ["setShowRightNav", "setArticleMenu"]),
    async logout() {
      await this.$store.dispatch('client/logout');
      this.$router.push(`client/login?redirect=${this.$route.fullPath}`);
    },
    // 抽屉开关
    toggle() {
      // 是否打开抽屉头部
      // this.setArticleMenu(!this.articleMenu);
      // 是否显示左边栏
      this.setShowRightNav(!this.showRightNav);
      // 开关样式
      this.toggleLineData = this.showRightNav
        ? this.lineStyle.closeLineData
        : this.lineStyle.normalLineData;
    },
    setLineData(e) {
      if (this.showRightNav) {
        return;
      }
      if (e.type === "mouseover") {
        this.toggleLineData = this.lineStyle.arrowLineData;
      } else {
        this.toggleLineData = this.lineStyle.normalLineData;
      }
    },
    toView(to) {
      this.$router.push({
        name: to
      });
    }
  }
};
</script>
<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../../stylus/color.styl';

#right-nav {
  position: relative;
  width: 320px;

  .right-nav-wrap {
    position: fixed;
    right: 0;
    top: 0;
    bottom: 0;
    width: 320px;
    background-color: $color-main;
    color: $color-white;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 30px;
    overflow: hidden;

    .menu-info-head {
      margin-bottom: 10px;

      > span {
        color: #999999;
        padding: 5px;
        font-weight: bold;
        cursor: pointer;

        &:hover, &.active {
          color: $color-white;
        }
      }
    }

    .content-wrap {
      position: relative;
      width: 100%;
      max-height: calc(100vh - 150px);
      overflow-y: auto;

      .article-menu {
        // position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 5px;
      }

      .info-wrap {
        // position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;

        .avatar {
          border: 4px solid $color-white;
          border-radius: 50%;
          width: 100px;
          height: 100px;
        }

        .name {
          color: $color-white;
          padding: 15px;
          font-size: 18px;
          font-weight: bold;
        }

        .motto {
          color: #999999;
          padding: 5px 15px;
          font-size: 14px;
          font-weight: bold;
          text-align: center;
        }

        .menu-wrap {
          display: flex;
          flex-direction: row;
          align-items: center;
          margin-top: 15px;

          .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 1px solid #555555;
            font-size: 14px;
            padding: 0 15px;
            color: #999999;
            transition: all 0.3s;
            cursor: pointer;
            font-weight: bold;

            &:last-child {
              border-right: 0px;
            }

            &:hover {
              color: $color-white;
            }

            .count {
              margin-bottom: 5px;
              font-size: 20px;
            }
          }
        }

        .social-wrap {
          padding: 20px;
          display: flex;
          flex-direction: row;
          align-items: center;
          flex-wrap: wrap;

          .social-item {
            padding: 8px;
            margin-right:20px;
            border: 1px solid #fc6423;
            border-radius: 5px;
            font-size: 14px;
            line-height: 1;
            color: #fc6423;
            transition: all 0.3s;
            cursor: pointer;

            &:hover {
              background-color: #fc6423;
              color: $color-white;
            }

            .iconfont {
              font-size: 14px;
            }
          }

          .logout-item {
            padding: 8px;
            border: 1px solid #1E90FF;
            border-radius: 5px;
            font-size: 14px;
            line-height: 1;
            color: #1E90FF;
            transition: all 0.3s;
            cursor: pointer;

            &:hover {
              background-color: #1E90FF;
              color: $color-white;
            }

            .iconfont {
              font-size: 14px;
            }
          }
        }
      }
    }
  }

  .toggle {
    position: fixed;
    width: 24px;
    height: 24px;
    background-color: $color-main;
    right: 10px;
    bottom: 45px;
    padding: 5px;
    z-index: 1050;
    cursor: pointer;
    line-height: 0;

    .toggle-line {
      position: relative;
      display: inline-block;
      vertical-align: top;
      width: 100%;
      height: 2px;
      margin-top: 4px;
      background-color: $color-white;

      &:first-child {
        margin-top: 0px;
      }
    }
  }
}

.slide-fade-enter, .slide-fade-leave-to {
  opacity: 0;
}
</style>
