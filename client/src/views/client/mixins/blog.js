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
          this.$router.push({
            name: 'Article',
            query: {
              id: this.article.id
            }
          })
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
    },
    toEdit(id) {
      this.$router.push({
        name: 'Write',
        query: {
          id: id
        }
      })
    }
  }
}

export {
  toPath
}
