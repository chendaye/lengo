import hljs from 'highlight.js'
import 'highlight.js/styles/dracula.css'

const Highlight = {}
Highlight.install = function(Vue, options) {
  Vue.directive('highlight', function(el) {
    const blocks = el.querySelectorAll('pre code')
    blocks.forEach((block) => {
      hljs.highlightBlock(block)
    })
  })
}
export default Highlight
