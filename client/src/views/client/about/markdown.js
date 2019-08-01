import {
  markdown
} from '@/utils/markdown'
var mark = {
  data() {
    return {
      markdownContent: `# 一级标题
![ce66dc0e63cac68e1a7bc0aa90623b20.jpg](http://www.lengo.top:8888/group1/M00/00/00/rBIABl1AZmCAOXLmAAbQ89KxSSQ1153835)
## 二级标题
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cubGVuZ28udG9wXC9hcGlcL2NsaWVudFwvbG9naW4iLCJpYXQiOjE1NjQ0OTk2ODksImV4cCI6MTU2NDUwNjg4OSwibmJmIjoxNTY0NDk5Njg5LCJqdGkiOiJoRjlYYlhIUkcxdTVFbExRIiwic3ViIjo1LCJwcnYiOiI0MWVmYjdiYWQ3ZjZmNjMyZTI0MDViZDNhNzkzYjhhNmJkZWM2Nzc3In0.hEB03kjFkNNnm9kPCCG0-2oc1mrd5LjaIxx4jvGhLqE
# 一级标题
`
    }
  },
  methods: {
    // 把markdown转化为 html
    markdownHtml(str) {
      return markdown(str)
    }
  }
}

export {
  mark
}
