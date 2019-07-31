<template>
  <div id="comments">
    <!-- 评论框 -->
    <div id="comments-input-top" v-loading="loading" class="input-wrap">
      <div class="input-top" />
      <el-input
        id="comments-content-area"
        v-model="content"
        class="input-area"
        type="textarea"
        size="mini"
        :rows="5"
        resize="none"
        :placeholder="placeholder"
      />
      <div class="btn-wrap" :style="{paddingBottom: showEmoji ? '96px' : '0px'}">
        <span class="emoji-btn" :class="{active: showEmoji}" @click="showEmoji = !showEmoji">表情</span>
        <div class="action-btn">
          <span v-show="content !== ''" class="cancel-btn" @click="content = ''">取消</span>
          <span class="send-btn" @click="check">发送~</span>
        </div>
      </div>
      <!-- 表情 -->
      <transition name="slide-fade">
        <ul v-if="showEmoji" class="emoji-wrap" style="top:143px">
          <li
            v-for="(emoji, index) in emojiList"
            :key="index"
            class="emoji-item"
            :title="emoji.title"
            @click="choseEmoji(emoji.title)"
          >
            <img :src="gifUrl(emoji.name)" alt>
          </li>
        </ul>
      </transition>
    </div>
    <!-- 评论展示 -->
    <p class="count">{{ count }}条{{ id === '-1' ? '留言' : '评论' }}</p>
    <no-data v-if="commentsList.length === 0" :text="getNoDataText()" />
    <ul class="comments-wrap">
      <li v-for="(comments, index) in commentsList" :key="index" class="comments-item">
        <div class="comments-info">
          <img class="avatar" :src="getAvatar(comments)">
          <div class="name-time">
            <p class="name">
              {{ getName(comments) }}
              <span @click="reply(comments)">回复</span>
            </p>
            <p class="time">{{ comments.created_at }}</p>
          </div>
        </div>
        <p class="content">
          <span v-for="(item, index) in JSON.parse(comments.content)" :key="index">
            {{ item.type === 'text' ? item.content : '' }}
            <img
              v-if="item.type === 'emoji'"
              class="content-emoji"
              :src="gifUrl(item.content)"
              alt
            >
          </span>
        </p>
        <ul v-if="comments.children.length > 0" class="comments-children">
          <li v-for="(child, index) in comments.children" :key="index" class="comments-child">
            <div class="comments-info">
              <img class="avatar" :src="getAvatar(child)">
              <div class="name-time">
                <p class="name">
                  {{ getName(child) }}
                  <span @click="reply(child)">回复</span>
                </p>
                <p class="time">{{ child.created_at }}</p>
              </div>
            </div>
            <p class="content">
              <span v-for="(item, index) in JSON.parse(child.content)" :key="index">
                {{ item.type === 'text' ? item.content : '' }}
                <img
                  v-if="item.type === 'emoji'"
                  class="content-emoji"
                  :src="gifUrl(item.content)"
                  alt
                >
              </span>
            </p>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";

import noData from "@/components/noData/noData";
import { scroll } from "@/layoutClient/mixin/scroll";
import { emoji } from "@/layoutClient/mixin/emoji";
import { importJs } from "@/utils/index";
import crud from "@/api/crud";

const wtuCrud = crud.factory("blog", "client");

export default {
  name: "Comments",
  components: {
    noData
  },
  mixins: [scroll, emoji],
  props: ["id"],
  data() {
    return {
      placeholder: "写下您的评论~",
      content: "",
      showEmoji: false,
      count: 0,
      name: "",
      email: "",
      commentsList: [],
      replyId: 0,
      replyName: "",
      avatar: require("@/assets/logo.jpg"),
      captcha: false,
      loading: false
    };
  },
  computed: {
    ...mapGetters(["blogInfo"]),
    // 登录人信息
    client: function() {
      return this.$store.state.client;
    }
  },
  watch: {
    content(value) {
      if (this.replyName !== "") {
        if (value.indexOf(this.replyName) !== 0) {
          this.replyId = 0;
          this.replyName = "";
        }
      }
    },
    replyName(value) {
      this.content = this.replyName;
    },
    id(value) {
      if (value !== "") {
        this.init();
      }
    }
  },
  created() {
    importJs("https://ssl.captcha.qq.com/TCaptcha.js");
    if (this.id !== "") {
      this.init();
    }
    if (this.id === "-1") {
      this.placeholder = "写下您的留言~";
    }
  },
  methods: {
    gifUrl(name) {
      return require("../../assets/emoji/" + name);
    },
    getNoDataText() {
      return this.id === "-1" ? "还没有留言~" : "还没有评论~";
    },
    getName(comments) {
      return comments.name + (comments.user_id !== 1 ? "" : "（作者）");
    },
    getAvatar(comments) {
      return comments.pid === 0 ? this.avatar : this.blogInfo.avatar;
    },
    init() {
      this.count = 0;
      this.commentsList = [];
      this.replyId = 0;
      this.replyName = "";
      this.content = "";
      this.getList();
    },
    // todo: 评论列表
    getList() {
      wtuCrud
        .get("comments", { id: this.id })
        .then(res => {
          if (res.data.status) {
            this.count = res.data.data.length;
            this.commentsList = res.data.data;
          }
        })
        .catch(() => {});
    },
    // todo: 创建评论
    addComments(params) {
      console.log('评论', params);

      this.loading = true;
      wtuCrud
        .post("addComments", { params })
        .then(res => {
          console.log(res);
          this.$notify.success({
            title: "消息",
            message: '评论成功！'
          });
          this.init()
          this.loading = false;
        })
        .catch(err => {
          this.$notify.error({
            title: "消息",
            message: err || "创建评论失败！"
          });
          this.loading = false;
        });
    },
    choseEmoji(title) {
      this.content += title;
    },
    // todo: 表单验证
    check() {
      // 评论内容
      if (this.content === "" || this.content === `@${this.replyName} `) {
        const msg = this.id == "-1" ? "留言" : "评论";
        this.$notify.info({
          title: "消息",
          message: `${msg}内容不能为空`
        });
        return;
      }
      // 验证码
      if (this.captcha) {
        this.captcha.show();
        return;
      } else {
        // 自己申请腾讯验证码，2034464857 0wt-IbkrRHHb5eEmViY9Rvg**
        this.captcha = new TencentCaptcha("2034464857", res => {
          if (res.ret === 0) {
            // todo： 前端验证通过， 把ticket传到后端 https://ssl.captcha.qq.com/ticket/verify 验证
            this.send(res.ticket, res.randstr);
            return;
          }
          this.$notify.error({
            title: "消息",
            message: "未通过验证"
          });
        });
        this.captcha.show();
      }
    },
    // todo: 提交评论
    send(ticket, randstr) {
      const params = {
        article_id: this.id,
        name: this.client.name,
        email: this.client.email,
        user_id: this.client.id,
        pid: this.replyId,
        content: this.analyzeEmoji(this.content),
        raw_content: this.content,
        ticket: ticket,
        randstr: randstr
      };
      this.addComments(params)
    },
    // todo: 回复评论
    reply(comments) {
      this.replyId = comments.id;
      this.replyName = `@${comments.name} `;
      let top = document
        .getElementById("comments-input-top")
        .getBoundingClientRect().top;
      top += document.body.scrollTop || document.documentElement.scrollTop;
      this.scrollToTarget(top);
      document.getElementById("comments-content-area").focus();
    }
  }
};
</script>
<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../stylus/color.styl';

#comments {
  margin-top: 20px;
  position: relative;
  background-color: $color-white;
  animation: show 0.8s;

  .input-wrap {
    position: relative;

    .input-top {
      margin-bottom: 5px;

      .top-item {
        min-width: 194px;
        margin-top: 5px;
      }
    }

    .btn-wrap {
      position: relative;
      margin-top: 10px;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: flex-end;
      transition: padding-bottom 0.3s;

      .emoji-btn {
        position: relative;
        padding: 5px;
        background-color: $color-main;
        border-radius: 5px;
        color: $color-white;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        transition: all 0.3s;

        &:hover {
          background-color: lighten($color-main, 30%);
        }

        &.active {
          z-index: 101;
          border-bottom-left-radius: 0px;
          border-bottom-right-radius: 0px;
          border: 1px solid #eeeeee;
          border-bottom: none;
          color: $color-main;
          background-color: $color-white;
        }
      }

      .action-btn {
        .cancel-btn {
          font-size: 14px;
          cursor: pointer;
          color: lighten($color-main, 30%);
          transition: color 0.3s;
          margin-right: 10px;

          &:hover {
            color: $color-main;
          }
        }

        .send-btn {
          display: inline-block;
          padding: 8px 18px;
          background-color: #409EFF;
          border-radius: 5px;
          color: $color-white;
          font-size: 14px;
          font-weight: bold;
          cursor: pointer;
          -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
          transition: background-color 0.3s;

          &:hover {
            background-color: lighten(#409EFF, 30%);
          }
        }
      }
    }

    .emoji-wrap {
      position: absolute;
      background-color: $color-white;
      padding: 5px;
      top: 214px;
      left: 0;
      max-width: 770px;
      height: 96px;
      transition: opacity 0.3s;
      z-index: 100;
      overflow-y: auto;
      border: 1px solid #eeeeee;
      border-radius: 0 5px 5px 5px;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;

      .emoji-item {
        padding: 5px;
        cursor: pointer;
        border-radius: 5px;
        width: 28px;
        height: 28px;
        background-color: $color-white;
        transition: background-color 0.3s;

        &:hover {
          background-color: #999999;
        }

        > img {
          width: 100%;
          height: 100%;
        }
      }
    }
  }

  .count {
    margin-top: 10px;
    font-size: 14px;
    color: $color-main;
    font-weight: bold;
    border-bottom: 1px solid #eeeeee;
    padding: 5px 0px;
  }

  .comments-wrap {
    .comments-item {
      padding: 10px 5px;
      transition: background-color 0.3s;

      &:hover {
        background-color: #eeeeee;
      }

      .comments-info {
        display: flex;
        flex-direction: row;
        margin-bottom: 5px;

        .avatar {
          width: 32px;
          height: 32px;
          margin-right: 5px;
          border-radius: 50%;
        }

        .name-time {
          flex: 1;

          .name {
            font-size: 14px;
            color: #555555;
            margin-bottom: 2px;

            > span {
              font-size: 12px;
              float: right;
              cursor: pointer;
              color: lighten($color-main, 30%);

              &:hover {
                color: $color-main;
              }
            }
          }

          .time {
            font-size: 12px;
            color: #999999;
          }
        }
      }

      .content {
        padding-left: 40px;
        font-size: 14px;
        color: #555555;
        line-height: 16px;
        word-break: break-all;
      }
    }
  }

  .comments-children {
    margin-left: 12px;
    border-left: 2px solid #999999;
    margin-top: 5px;

    .comments-child {
      padding: 10px 5px;
      padding-left: 17px;
      transition: background-color 0.3s;

      &:hover {
        background-color: #dddddd;
      }

      .comments-info {
        display: flex;
        flex-direction: row;
        margin-bottom: 5px;

        .avatar {
          width: 32px;
          height: 32px;
          margin-right: 5px;
          border-radius: 50%;
        }

        .name-time {
          flex: 1;

          .name {
            font-size: 14px;
            color: #555555;
            margin-bottom: 2px;

            > span {
              font-size: 12px;
              float: right;
              cursor: pointer;
              color: lighten($color-main, 30%);

              &:hover {
                color: $color-main;
              }
            }
          }

          .time {
            font-size: 12px;
            color: #999999;
          }
        }
      }

      .content {
        padding-left: 40px;
        font-size: 14px;
        color: #555555;
        line-height: 16px;
        word-break: break-all;
      }
    }
  }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}

@keyframes show {
  from {
    margin-top: -10px;
    opacity: 0;
  }

  to {
    margin-top: 0px;
    opacity: 1;
  }
}
</style>
<style lang="stylus">
.content-emoji {
  width: 14px;
  height: 14px;
  margin: 1px;
}
</style>
