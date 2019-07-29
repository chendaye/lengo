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
              <cover :article-id="articleId" @upcover="upcover" />
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
              <category
                :is-check="true"
                :article-id="articleId"
                :is-filter="true"
                @handchecked="handchecked"
                @handDetailChecked="handDetailChecked"
              />
            </el-card>
          </div>
        </el-col>
        <el-col :span="8">
          <div class="grid-content bg-purple">
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span class="header-attr">文章标签</span>
              </div>
              <tag
                :article-id="articleId"
                @check="check"
                @nocheck="nocheck"
                @defaultChecked="defaultChecked"
              />
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
          @imgDel="$imgDel"
          @save="$save"
          @change="change"
        />
      </div>
    </el-main>
    <el-footer>
      <el-button v-if="articleId === null" type="success" round plain @click="submit(0)">
        上传文章
        <i class="el-icon-upload el-icon--right" />
      </el-button>
      <el-button v-else type="success" round plain @click="update()">
        更新文章
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
import { findIndex } from "@/utils/index";
import { markdown } from '@/utils/markdown'
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
      baseUrl: process.env.VUE_APP_BASE_API,
      // 文章id
      articleId: null,
      detail: null,
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
      categorysNew: [], // 上一次保存的 分类
      // 选中的标签
      checks: [],
      checksNew: [], // articleId 非空时有值 上一次选中的 tag
      // 上传的封面
      coverImg: {
        filename: "",
        group_name: "",
        sortUrl: "",
        url: ""
      }
    };
  },
  created() {
    if (this.$route.params.id) {
      // 文章id
      this.articleId = this.$route.params.id;
      wtuCrud
        .get("detailArticle", {
          where: {
            id: { op: "=", va: this.articleId, ex: "cp" }
          }
        })
        .then(res => {
          if (res.status === 200) {
            this.article.title = res.data.title;
            this.article.abstract = res.data.abstract;
            this.content = res.data.content;
            this.html = res.data.html;
          }
        });
    }
  },
  methods: {
    // 将图片上传到服务器，返回地址替换到md中
    $imgAdd(pos, $file) {
      var formdata = new FormData();
      formdata.append("file", $file);

      wtuCrud.post("markDownPic", formdata).then(res => {
        if (res.status === 200) {
          this.$refs.md.$img2Url(pos, res.data.url);
        }
      });
    },
    // 删除图片
    $imgDel($file) {
      const sort = $file[0].replace(process.env.VUE_APP_PIC, "");
      wtuCrud.post("imgDel", { sort: sort }).then(res => {
        if (res.data.data) {
          this.$message.success("原有图片已经删除！");
        }
      });
    },
    // 存草稿
    $save() {
      if (this.articleId === null) {
        this.submit(1);
      } else {
        // 更新操作
        this.update();
      }
    },
    change(value, render) {
      // render 为 markdown 解析后的结果
      this.html = render;
    },
    // 把markdown转化为 html
    markdownHtml(str) {
      return markdown(str)
    },
    // update 更新文章
    update() {
      if (this.articleId !== null) {
        // 更新文章
        const article = {
          id: this.articleId,
          markdown: this.content,
          html: this.markdownHtml(this.content),
          cover: this.coverImg,
          tags: this.checks,
          tagsNew: this.checksNew, // 要更新的tag
          categorys: this.categorys,
          categorysNew: this.categorysNew, // 要更新的分类
          title: this.article.title,
          abstract: this.article.abstract
        };
        console.log("更新内容", article);
        wtuCrud.post("updateArticle", article).then(res => {
          if (res.status === 200) {
            const info = res.data.data;
            // 保存最新的的数据库里的值
            this.checks = info[0].tagsNew;
            this.categorys = info[0].categorysNew;
            this.$message({
              message: '"笔记更新成功，开始新的知识之旅吧！"',
              type: "success",
              duration: 1500,
              onClose: () => {
                this.$router.push({ path: "/admin/wtu/note/noteManage" });
              }
            });
          }
        });
      } else {
        this.$notify.info({
          title: "消息",
          message: "文章ID 为空！"
        });
      }
    },

    // 提交文章
    submit(draft) {
      if (!this.article.title || !this.article.abstract) {
        this.$notify.info({
          title: "消息",
          message: "请编辑文章标题和摘要！"
        });
        return false;
      } else if (this.content === "" || this.html === "") {
        this.$notify.info({
          title: "消息",
          message: "请编辑文章内容！"
        });
        return false;
      } else if (this.coverImg === {}) {
        this.$notify.info({
          title: "消息",
          message: "请编辑文章内容！"
        });
        return false;
      } else if (this.checks.length === 0) {
        this.$notify.info({
          title: "消息",
          message: "请选择文章标签！"
        });
        return false;
      } else if (this.categorys.length === 0) {
        this.$notify.info({
          title: "消息",
          message: "请选择文章分类！"
        });
        return false;
      } else {
        const article = {
          markdown: this.content,
          html: this.markdownHtml(this.content),
          cover: this.coverImg,
          tags: this.checks,
          category: this.categorys,
          title: this.article.title,
          abstract: this.article.abstract,
          draft: draft
        };
        wtuCrud.post("article", article).then(res => {
          // 文章已经保存，生成文章id
          this.articleId = res.data.data.article.id;
          this.$message({
            message: '"笔记更新成功，开始新的知识之旅吧！"',
            type: "success",
            duration: 1500,
            onClose: () => {
              this.$router.push({ path: "/admin/wtu/note/noteManage" });
            }
          });
        });
      }
    },

    // 封面上传
    upcover(data) {
      // todo： 注意浏览器图片缓存
      if (this.coverImg.sortUrl) {
        // 上传图片 删除原有图片
        wtuCrud.post("imgDel", { sort: this.coverImg.sortUrl }).then(res => {
          if (res.data.data) {
            this.$message.success("原有图片已经删除！");
          }
        });
      }
      this.coverImg = data;
    },

    // 选中分类树 一次性获取
    handchecked(data) {
      // 半选和全选都存入数据库
      if (data.check.checkedKeys.length > 0) {
        if (this.articleId === null) {
          // 新建文章
          this.categorys = data.check.checkedKeys.concat(
            data.check.halfCheckedKeys
          );
        } else {
          // 编辑文章
          this.categorysNew = data.check.checkedKeys.concat(
            data.check.halfCheckedKeys
          );
        }
      }
      console.log("cate", this.categorys);
      console.log("cateNew", this.categorysNew);
    },
    // 文章详情初始化分类
    handDetailChecked(data) {
      if (this.articleId > 0 && data.length > 0) {
        this.categorys = Object.assign([], data);
        this.categorysNew = Object.assign([], data);
      }
    },

    // 标签选中事件 逐个选取
    check(data) {
      if (this.articleId === null) {
        // todo: 新文章 两个数组同步
        this.checks.push(data);
        this.checksNew.push(data);
      } else {
        // todo: 文章保存过，在原有的标签上变动
        // todo: 文章编辑（同时初始化 checks checksNew），编辑在checksNew上变动
        this.checksNew.push(data);
      }
    },
    // 取消选中事件
    nocheck(data) {
      // todo: 同选中事件
      if (this.articleId === null) {
        this.checks.splice(findIndex(this.checks, data), 1);
        this.checksNew.splice(findIndex(this.checks, data), 1);
      } else {
        this.checksNew.splice(findIndex(this.checks, data), 1);
      }
    },
    // 文章详情默认选中标签
    defaultChecked(tag) {
      if (tag.length > 0) {
        // 同步
        this.checks = Object.assign([], tag);
        this.checksNew = Object.assign([], tag); // 按值传递， 直接赋值是引用
      }
    },
    // 检查标题是否重复
    title(data) {
      if (this.articleId === null) {
        wtuCrud.get("title", { title: this.article.title }).then(res => {
          if (res.status && res.data.data) {
            this.$message({
              message: "标题重复！",
              type: "warning"
            });
            this.article.title = "";
          }
        });
      }
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
