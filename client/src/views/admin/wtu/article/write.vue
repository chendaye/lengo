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
              <category :is-check="true" @handchecked="handchecked" />
            </el-card>
          </div>
        </el-col>
        <el-col :span="8">
          <div class="grid-content bg-purple">
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span class="header-attr">文章标签</span>
              </div>
              <div v-for="item in tags" :key="item.id" class="tag-content">
                <tag :size-val="size" :content="item" :is-check="true" @check="check" @nocheck="nocheck" />
              </div>
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
import cover from "@/components/cover/index";
import category from "@/components/Tree/index";
import tag from '@/components/Tag/index'
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
      configs: {},
      // 标签尺寸
      size: 'mini',

      // 文章
      article: {
        title: "",
        abstract: ""
      },
      // 选中的标签
      checks: [],
      // 所有标签
      tags: [],
      // 所有分类
      categorys: [],
      // 上传的封面
      coverImg: ''
    };
  },
  created() {
    // 获取标签
    wtuCrud
      .get("listTag", {
        order: { id: 'desc', created_at: 'asc' },
        where: { created_at: { op: '!=', va: '', ex: 'cp' }}
      })
      .then(res => {
        if (res.status === 200) {
          this.tags = res.data;
        }
      });
  },
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

    // 选中分类树
    handchecked(data) {
      // 半选和全选都存入数据库
      if (data.check.checkedKeys.length > 0) {
        this.categorys = data.check.checkedKeys.concat(data.check.halfCheckedKeys);
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

.tag-content {
  float:left; margin:6px;
}
</style>
