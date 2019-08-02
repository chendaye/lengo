
var toPath = {
  data() {
    return {
    }
  },
  methods: {
    showArticle() {
      this.$router.push({
        name: 'Article',
        query: {
          id: this.article.id
        }
      })
    },
    toList(type, id) {
      this.$router.push({
        name: 'List',
        query: {
          type: type,
          id: id
        }
      })
    }
  }
}

export {
  toPath
}
