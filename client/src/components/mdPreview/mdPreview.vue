<template>
  <div id="md-preview">
    <link href="//cdn.bootcss.com/github-markdown-css/2.4.1/github-markdown.css" rel="stylesheet">
    <section id="markdown-preview-body" v-highlight class="preview markdown-body" v-html="contents" />
  </div>
</template>

<script>
import {
  mapActions
} from 'vuex'
import { markdown } from '@/utils/markdown'

export default {
  name: 'MdPreview',
  components: {
  },
  props: ['contents'],
  data() {
    return {
      imgList: []
    }
  },
  watch: {
    contents(content) {
      // this.setArticleMenu(false)
      setTimeout(this.init, 1000)
    }
  },
  created() {
    setTimeout(this.init, 1000)
  },
  mounted() {
    setTimeout(this.init, 1000)
  },
  beforeDestroy() {
    this.setArticleMenuTag('1.')
    this.setArticleMenuSource([])
    this.setArticleMenu(false)
  },
  methods: {
    ...mapActions('blog', [
      'setArticleMenu',
      'setArticleMenuSource',
      'setArticleMenuTag'
    ]),
    init() {
      document.body.scrollTop = document.documentElement.scrollTop = 0
      this.getImg()
      this.getMenu()
    },
    // todo: 查看文章大图
    getImg() {
      const imgDomList = document.getElementById('markdown-preview-body').getElementsByTagName('img')
      this.imgList = []
      Array.prototype.slice.call(imgDomList).forEach((img, index) => {
        img.indexTag = index
        img.onclick = this.showBigImg
        this.imgList.push({
          src: img.src,
          w: img.width,
          h: img.height,
          target: img
        })
      })
    },
    showBigImg(e) {
      this.$photoPreview.open(e.target.indexTag, this.imgList)
    },
    // todo: 获取文章目录
    getMenu() {
      // 获取所有 h 标签
      const headNodes = document.getElementById('markdown-preview-body').getElementsByClassName('my-blog-head')
      const headList = []
      const pos = 0
      // 获取标题
      Array.prototype.forEach.call(headNodes, item => {
        headList.push({
          id: item.id,
          index: parseInt(item.tagName.replace('H', '')),
          title: item.innerText
        })
      })
      // 生成目录树
      let tree = this.treeify(headList, 0)
      if (tree.length === 0) {
        tree = false
      }
      console.log('tree', tree)
      // 把当前文章目录存进store
      const source = JSON.parse(JSON.stringify(headList))
      source.forEach(item => {
        item.children = []
      })
      this.setArticleMenuTag('1.') // tag
      this.setArticleMenuSource(source) // 原始标题
      this.setArticleMenu(tree) // 目录树
    },
    // 生成目录树
    treeify(data, tag) {
      const tree = []
      let index = 0
      data.forEach(item => {
        item.children = []
        const len = tree.length
        if (len === 0) {
          item.tag = tag + (++index) + '.'
          tree.push(item) // 第一个元素，直接放进tree
        } else {
          const last = tree[len - 1]
          if (item.index <= last.index) { // 如果index比tree最后一个的index小或等于，说明是同级存进去
            item.tag = tag + (++index) + '.'
            tree.push(item)
          } else {
            last.children.push(item) // 否则存进最后一个元素的children
          }
        }
      })
      // 因为上面一层循环，只能处理两层，所以需要遍历孩子节点，出现index不一样的说明不是同级，需要对孩子节点再递归调用生成
      tree.forEach(item => {
        const children = item.children
        const ids = []
        index = 0
        // 判断是否存在index不一样的
        children.forEach(child => {
          child.tag = item.tag + (++index) + '.'
          if (ids.indexOf(child.index) === -1) {
            ids.push(child.index)
          }
        })
        if (ids.length > 1) {
          // ids的元素大于1说明存在，需要再递归孩子节点
          item.children = this.treeify(children, item.tag)
        }
      })
      return tree
    }
  }
}
</script>

<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus">
@import '../../stylus/color.styl'
#md-preview
  position: relative
  width: 100%
  margin-top: 10px
</style>
