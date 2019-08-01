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
  created() {
    if (this.$route.query.tag > 0) {
      this.tag.push(this.$route.query.tag);
    }
    if (this.$route.query.category > 0) {
      this.category.push(this.$route.query.category);
    }
    if (this.$route.query.title) {
      this.title = this.$route.query.title
    }
  }
}
</script>
