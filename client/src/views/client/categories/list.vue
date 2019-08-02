<template>
  <div>
    <List :tag="tag" :category="category" :title="title" />
  </div>
</template>

<script>
import List from '../components/List'

export default {
  name: 'ArticleList',
  components: {
    List
  },
  data() {
    return {
      tag: [],
      category: [],
      title: ''
    };
  },
  computed: {
    key() {
      return this.$route.name !== undefined ? this.$route.name + +new Date() : this.$route + +new Date()
    }
  },
  watch: {
    $route(route) {
      this.initParams()
    }
  },
  created() {
    this.initParams();
  },
  methods: {
    initParams() {
      // 标签
      if (this.$route.query.type === 'tag') {
        this.tag = [this.$route.query.id];
      }
      // 分类
      if (this.$route.query.type === 'category') {
      // 获取所有子分类
        this.category = [this.$route.query.id];
      }
    }
  }
}
</script>
