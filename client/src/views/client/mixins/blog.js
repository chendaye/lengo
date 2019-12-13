import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", "client");
var toPath = {
  data() {
    return {
    }
  },
  methods: {
    showArticle() {
      wtuCrud.post('view', {
        id: this.article.id
      }).then(res => {
        if (res.data.data === true) {
          const { href } = this.$router.resolve({
            name: 'Article',
            query: {
              id: this.article.id
            }
          })
          window.open(href, '_blank')
        }
      })
    },
    toList(type, id) {
      const { href } = this.$router.resolve({
        name: 'List',
        query: {
          type: type,
          id: id
        }
      })
      window.open(href, '_blank')
    },
    toEdit(id) {
      const { href } = this.$router.resolve({
        name: 'Write',
        query: {
          id: id
        }
      })
      window.open(href, '_blank')
    }
  }
}

export {
  toPath
}
