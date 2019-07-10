<template>
  <el-container>
    <el-header>
      <h2>文章编辑</h2>
    </el-header>
    <el-main class="cover">
      <el-row>
        <el-col :span="24">
          <div class="grid-content bg-purple">
            <el-input v-model="article.title" placeholder="请输入内容" @blur="title">
              <template slot="prepend">文章标题：</template>
            </el-input>
          </div>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24">
          <div class="grid-content bg-purple">
            <el-input v-model="article.abstract" placeholder="请输入内容">
              <template slot="prepend">文章摘要：</template>
            </el-input>
          </div>
        </el-col>
      </el-row>
      <el-row :gutter="20">
        <el-col :span="8">
          <div class="grid-content bg-purple">
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span class="header-attr">文章封面</span>
              </div>
              <!-- 封面图片上传 -->
              <cover @upcover="upcover" />
            </el-card>
          </div>
        </el-col>
        <el-col :span="8">
          <div class="grid-content bg-purple">
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span class="header-attr">文章分类</span>
              </div>
              <!-- 分类选择 -->
              <category :is-check="true" :is-filter="true" @handchecked="handchecked" />
            </el-card>
          </div>
        </el-col>
        <el-col :span="8">
          <div class="grid-content bg-purple">
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span class="header-attr">文章标签</span>
              </div>
              <tag @check="check" @nocheck="nocheck" />
            </el-card>
          </div>
        </el-col>
      </el-row>
    </el-main>
    <el-main>
      <div class="container">
        <mavon-editor
          ref="md"
          v-model="content"
          :ishljs="true"
          style="min-height: 600px"
          @imgAdd="$imgAdd"
          @save="$save"
          @change="change"
        />
      </div>
    </el-main>
    <el-footer>
      <el-button type="success" round plain @click="submit(0)">
        上传文章
        <i class="el-icon-upload el-icon--right" />
      </el-button>
    </el-footer>
  </el-container>
</template>

<script>
import { mavonEditor } from "mavon-editor";
import "mavon-editor/dist/css/index.css";
import cover from "@/components/cover/index";
import category from "@/components/Tree/index";
import tag from "@/components/Tag/tagFilter";
import crud from "@/api/crud";
const wtuCrud = crud.factory("wtu");

export default {
  name: "Markdown",
  components: {
    mavonEditor,
    cover,
    category,
    tag
  },
  data: function() {
    return {
      // markDown
      content: "",
      html: "",
      // 文章
      article: {
        title: "",
        abstract: ""
      },
      // 所有分类
      categorys: [],
      // 上传的封面
      coverImg: {},
      // 选中的标签
      checks: []
    };
  },
  methods: {
    // 将图片上传到服务器，返回地址替换到md中
    $imgAdd(pos, $file) {
      var formdata = new FormData();
      formdata.append("file", $file);

      wtuCrud.post("markDownPic", formdata).then(res => {
        if (res.status === 200) {
          this.$refs.md.$img2Url(pos, res.data.url);
          console.log(res.data.url);
        }
      });
    },
    // 存草稿
    $save() {
      this.submit(1);
    },
    change(value, render) {
      // render 为 markdown 解析后的结果
      this.html = render;
    },
    // 提交文章
    submit(draft) {
      if (!this.article.title || !this.article.abstract) {
        this.$notify.info({
          title: '消息',
          message: '请编辑文章标题和摘要！'
        });
        return false;
      } else if (this.content === '' || this.html === '') {
        this.$notify.info({
          title: '消息',
          message: '请编辑文章内容！'
        });
        return false;
      } else if (this.coverImg === {}) {
        this.$notify.info({
          title: '消息',
          message: '请编辑文章内容！'
        });
        return false;
      } else if (this.checks.length === 0) {
        this.$notify.info({
          title: '消息',
          message: '请选择文章标签！'
        });
        return false;
      } else if (this.categorys.length === 0) {
        this.$notify.info({
          title: '消息',
          message: '请选择文章分类！'
        });
        return false;
      } else {
        wtuCrud.post('article', {
          markdown: this.content,
          html: this.html,
          cover: this.coverImg,
          tags: this.checks,
          category: this.categorys,
          title: this.article.title,
          abstract: this.article.abstract,
          draft: draft
        }).then(res => {
          console.log(res);
          this.$message.success("笔记保存成功，开始新的知识之旅吧！");
        })
      }
    },
    // 选中分类树
    handchecked(data) {
      // 半选和全选都存入数据库
      if (data.check.checkedKeys.length > 0) {
        this.categorys = data.check.checkedKeys.concat(
          data.check.halfCheckedKeys
        );
      } else {
        this.$notify.error({
          title: "错误",
          message: "选择出错！"
        });
      }
    },

    // 封面上传
    upcover(data) {
      this.coverImg = data;
      console.log(data);
    },

    // 标签选中事件
    check(data) {
      this.checks.push(data);
    },
    // 取消选中事件
    nocheck(data) {
      let index = null;
      for (let i = 0; i < this.checks.length; i++) {
        if (this.checks[i] === data) {
          index = i;
        }
      }
      this.checks.splice(index, 1);
    },

    // 检查标题是否重复
    title(data) {
      wtuCrud.post('title', { title: this.article.title }).then(res => {
        if (res.status && res.data) {
          this.$message({
            message: '标题重复！',
            type: 'warning'
          });
          this.article.title = '';
        }
      });
    }
  }
};
</script>
<style scoped>
.editor-btn {
  margin-top: 20px;
}
.el-row {
  margin-bottom: 20px;
  &:last-child {
    margin-bottom: 0;
  }
}
.el-col {
  border-radius: 4px;
}

.el-header {
  color: #333;
  text-align: center;
  line-height: 60px;
}
.cover {
  overflow-x: hidden;
  overflow-y: hidden;
}
.el-footer {
  text-align: center;
  margin-bottom: 80px;
}
.box-card {
  width: 100%;
  min-width: 400px;
}

.content-title {
  font-weight: 400;
  line-height: 50px;
  margin: 10px 0;
  font-size: 22px;
  color: #1f2f3d;
}
.header-attr {
  font-size: 18px;
  font-weight: bold;
  font-family: "Courier New", Courier, monospace;
}
.clearfix {
  text-align: center;
}
</style>
