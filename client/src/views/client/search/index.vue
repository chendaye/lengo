<template>
  <div id="search" v-loading="loading">
    <div class="search-input-wrap">
      <!-- <input
        id="search-input"
        v-model="searchValue"
        type="search"
        placeholder="输入关键字搜索..."
        class="search-real-input"
        @keyup.enter="toSearch()"
      > -->
      <el-row :gutter="10">
        <el-col :span="8"><div class="grid-content bg-purple" />
          <category
            :is-filter="true"
            :is-check="true"
            :is-search="true"
            @handchecked="handchecked"
          />
        </el-col>
        <el-col :span="8"><div class="grid-content bg-purple" />
          <tag
            :is-search="true"
            @check="check"
            @nocheck="nocheck"
          />
        </el-col>
        <el-col :span="4"><div class="grid-content bg-purple" />
          <el-input
            v-model="title"
            placeholder="Title"
            size="mini"
            prefix-icon="el-icon-search"
          />
        </el-col>
        <el-col :span="2"><div class="grid-content bg-purple" />
          <el-button type="info" size="mini" plain @click="resetFilter">搜索</el-button>
        </el-col>
        <el-col :span="2"><div class="grid-content bg-purple" />
          <el-button type="info" size="mini" plain @click="resetFilter">重置</el-button>
        </el-col></el-row>
    </div>
    <div class="search-article-wrap">
      <List v-if="show" :tag="tag" :category="category" :title="title" />
    </div>
  </div>
</template>

<script>
import List from '../components/List'
import {
  mapActions
} from 'vuex'
import { scroll } from '@/layoutClient/mixin/scroll'
import { son, findIndex } from "@/utils/index";
import category from "@/components/Tree/index";
import tag from "@/components/Tag/tagFilter";

export default {
  name: 'Search',
  components: {
    List,
    category,
    tag
  },
  mixins: [scroll],
  data() {
    return {
      show: true,
      tag: [],
      category: [],
      title: '',
      loading: false
    }
  },
  watch: {
    $route(route) {
      this.initData()
    }
  },
  created() {
  },
  mounted() {
    this.initData()
  },
  methods: {
    ...mapActions([
      'searchArticle'
    ]),
    initData() {

    },
    toSearch() {
      if (this.searchValue === '') {
        this.$toast('搜索内容不能为空', 'error')
        return
      }
      this.$router.push({
        name: 'search',
        query: {
          value: this.searchValue
        }
      })
    },
    // 搜索分类
    handchecked(data) {
      // 半选和全选都存入数据库
      if (data.check.checkedKeys.length > 0) {
        this.category = data.check.checkedKeys.concat(
          data.check.halfCheckedKeys
        );
      }
    },
    // 标签搜搜
    check(data) {
      this.tag.push(data);
    },
    nocheck(data) {
      this.tag.splice(findIndex(this.tag, data), 1);
    },
    // 重置搜索
    resetFilter() {
      this.listQuery.where = {};
      this.handleFilter();
    }
  }
}
</script>

<style lang="stylus" scoped>
@import '../../../stylus/color.styl'
#search
  position: relative
  padding: 30px 10px
  display: flex
  flex-direction: column
  align-items: center
  .search-input-wrap
    width: 100%
    // padding: 0px 0px 60px
    max-width: 900px
    // min-height: 30px
    // height: 30px
    border-radius: 5px
    border: 1px solid #eeeeee
    .search-real-input
      width: 100%
      height: 28px
      padding: 5px 10px
      border-radius: 5px
      border: none
      font-size: 14px
      background-color: $color-white
      &::placeholder
        color: $text-tip
  .search-article-wrap
    position: relative
    width: 100%
    padding: 10px 30px
    animation: show .8s
    @media (max-width: 768px)
      padding: 5px
    .time-line
      position: absolute
      left: 30px
      @media (max-width: 768px)
        left: 5px
      top: 15px
      bottom: 0
      width: 2px
      background-color: #eeeeee

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
