<template>
  <el-container>
    <el-header>
      <h2>文章编辑</h2>
    </el-header>
    <el-main class="cover">
      <el-row>
        <el-col :span="24">
          <div class="grid-content bg-purple">
            <el-input v-model="article.title" placeholder="请输入内容">
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
              <cover />
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
              <category />
            </el-card>
          </div>
        </el-col>
        <el-col :span="8">
          <div class="grid-content bg-purple">
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span class="header-attr">文章标签</span>
              </div>
              <!-- 标签选择 -->
              <tag />
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
          style="min-height: 600px"
          @imgAdd="$imgAdd"
          @change="change"
        />
      </div>
    </el-main>
    <el-footer>
      <el-button type="success" round plain @click="submit">
        上传文章
        <i class="el-icon-upload el-icon--right" />
      </el-button>
      <el-button type="info" round plain @click="submit">
        上传草稿
        <i class="el-icon-upload el-icon--right" />
      </el-button>
    </el-footer>
  </el-container>
</template>

<script>
import { mavonEditor } from "mavon-editor";
import "mavon-editor/dist/css/index.css";
import cover from "../components/cover/index";
import category from "../components/category/index";
import tag from "../components/tag/index";

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
      configs: {},

      // 文章
      article: {
        title: "",
        abstract: ""
      }
    };
  },
  created() {},
  methods: {
    // 将图片上传到服务器，返回地址替换到md中
    $imgAdd(pos, $file) {
      var formdata = new FormData();
      formdata.append("file", $file);
      // 这里没有服务器供大家尝试，可将下面上传接口替换为你自己的服务器接口
      this.$axios({
        url: "/common/upload",
        method: "post",
        data: formdata,
        headers: { "Content-Type": "multipart/form-data" }
      }).then(url => {
        this.$refs.md.$img2Url(pos, url);
      });
    },
    change(value, render) {
      // render 为 markdown 解析后的结果
      this.html = render;
    },
    submit() {
      console.log(this.content);
      console.log(this.html);
      this.$message.success("提交成功！");
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
  font-family: 'Courier New', Courier, monospace;
}
.clearfix {
  text-align: center;
}
</style>