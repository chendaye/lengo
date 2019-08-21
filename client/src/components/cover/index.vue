<template>
  <div>
    <div v-if="modelSrc === null" class="cover" @click="dialogVisible = true">
      <i class="el-icon-plus avatar-uploader-icon" />
    </div>
    <div v-else @click="dialogVisible = true">
      <img width="100%" :src="modelSrc" alt="">
    </div>

    <el-dialog title="裁剪图片" :visible.sync="dialogVisible" width="50%">
      <div v-show="model" class="model" @click="model = false">
        <div class="model-show">
          <img :src="modelSrc" alt>
        </div>
      </div>
      <div class="cut">
        <vue-cropper
          ref="cropper"
          :img="option.img"
          :output-size="option.size"
          :output-type="option.outputType"
          :info="true"
          :full="option.full"
          :can-move="option.canMove"
          :can-move-box="option.canMoveBox"
          :fixed-box="option.fixedBox"
          :original="option.original"
          :auto-crop="option.autoCrop"
          :auto-crop-width="option.autoCropWidth"
          :auto-crop-height="option.autoCropHeight"
          :center-box="option.centerBox"
          :high="option.high"
          @real-time="realTime"
          @img-load="imgLoad"
        />
      </div>
      <!-- 预览 -->
      <div
        class="show-preview"
        :style="{'width': previews.w + 'px', 'height': previews.h + 'px', 'overflow': 'hidden', 'margin': '5px'}"
      >
        <div :style="previews.div">
          <img :src="previews.url" :style="previews.img">
        </div>
      </div>
      <!-- 操作按钮 -->
      <div class="test-button">
        <el-button type="primary" plain for="uploads">
          <label for="uploads">选择图片</label>
        </el-button>

        <input
          id="uploads"
          type="file"
          style="position:absolute; clip:rect(0 0 0 0);"
          accept="image/png, image/jpeg, image/gif, image/jpg"
          @change="uploadImg($event)"
        >
        <el-button type="success" plain @click="upToServer('base64')">上传封面</el-button>
        <el-button type="warning" plain @click="changeScale(1)">放大</el-button>
        <el-button type="warning" plain @click="changeScale(-1)">缩小</el-button>
        <el-button type="info" plain @click="refreshAndclear()">刷新</el-button>
        <!-- <button class="btn" @click="upToServer('blob')">上传blob</button>
        <button class="btn" @click="clearCrop">clear</button>
        <button class="btn" @click="refreshCrop">刷新</button>
        <button class="btn" @click="rotateLeft">左旋</button>
        <button class="btn" @click="rotateRight">右旋</button> -->
        <!-- <button v-if="!crap" class="btn" @click="startCrop">start</button>
        <button v-else class="btn" @click="stopCrop">stop</button>
        <button class="btn" @click="finish('base64')">预览(base64)</button>
        <button class="btn" @click="finish('blob')">预览(blob)</button>
        <a v-show="false" class="btn" @click="down('base64')">下载(base64)</a>
        <a v-show="false" class="btn" @click="down('blob')">下载(blob)</a> -->
      </div>
    </el-dialog>
  </div>
</template>

<script>
// import mimes from '../ImageCropper/utils/mimes.js'
// import data2blob from '../ImageCropper/utils/data2blob.js'
import crud from "@/api/crud";
const wtuCrud = crud.factory("wtu");
const blogCrud = crud.factory("blog", "client");
import { VueCropper } from "vue-cropper";

export default {
  components: {
    VueCropper
  },
  props: {
    articleId: {
      type: Number,
      default: null
    }
  },
  data: function() {
    return {
      baseUrl: process.env.VUE_APP_PIC,
      dialogVisible: false,
      model: false,
      modelSrc: null,
      crap: false,
      previews: {},
      option: {
        img: '',
        size: 1,
        full: false, // 是否输出原图比例的截图
        outputType: "png", // 输出图片格式 jpeg  png webp
        canMove: true, // 能否拖动图片
        fixedBox: false, // 截图框大小固定
        original: false, // 上传图片是否显示原始宽高 (针对大图 可以铺满)
        canMoveBox: true, // 能否拖动截图框
        autoCrop: true, // 是否自动生成截图
        // 只有自动截图开启 宽度高度才生效
        autoCropWidth: 200,
        autoCropHeight: 150,
        centerBox: false, // 截图框是否限制在图片里(只有在自动生成截图框时才能生效)
        high: true // 是否根据dpr生成适合屏幕的高清图片
      },
      show: true
    };
  },
  created() {
    // todo: 查询接口
    this.$route.path.indexOf('admin') > -1 ? this.api = wtuCrud : this.api = blogCrud;
    // 文章id-> 封面
    if (this.articleId !== null) {
      this.api.get('detailArticle', {
        where: {
          id: { op: '=', va: this.articleId, ex: 'cp' }
        }
      }).then(res => {
        if (res.status === 200) {
          const img = this.baseUrl + res.data.cover;
          this.modelSrc = img;
          this.$emit('upcover', {
            filename: '',
            group_name: '',
            url: img,
            sortUrl: res.data.cover
          });
        }
      });
    }
  },
  mounted: function() {
    this.$nextTick(function() {
      // 开启截图
      // this.$refs.cropper.startCrop();
    })
  },
  methods: {
    startCrop() {
      // start 截图 鼠标显示截图框
      this.crap = true;
      this.$refs.cropper.startCrop();
    },
    stopCrop() {
      //  stop 截图 鼠标 显示图片拖拽框
      this.crap = false;
      this.$refs.cropper.stopCrop();
    },
    clearCrop() {
      // clear 截图
      this.$refs.cropper.clearCrop();
    },
    refreshCrop() {
      // clear
      this.$refs.cropper.refresh();
    },
    // 清楚并且刷新
    refreshAndclear() {
      this.clearCrop();
      this.refreshCrop();
    },
    changeScale(num) {
      num = num || 1;
      this.$refs.cropper.changeScale(num);
    },
    rotateLeft() {
      this.$refs.cropper.rotateLeft();
    },
    rotateRight() {
      this.$refs.cropper.rotateRight();
    },
    // 预览截图
    finish(type) {
      // 输出
      // var test = window.open('about:blank')
      // test.document.body.innerHTML = '图片生成中..'
      if (type === "blob") {
        this.$refs.cropper.getCropBlob(data => {
          var img = window.URL.createObjectURL(data);
          this.model = true;
          this.modelSrc = img;
        });
      } else {
        this.$refs.cropper.getCropData(data => {
          this.model = true;
          this.modelSrc = data;
        });
      }
    },
    // 实时预览函数
    realTime(data) {
      this.previews = data;
    },
    // 下载图片
    down(type) {
      // event.preventDefault()
      var aLink = document.createElement("a");
      aLink.download = "demo";
      // 输出
      if (type === "blob") {
        this.$refs.cropper.getCropBlob(data => {
          this.downImg = window.URL.createObjectURL(data);
          aLink.href = window.URL.createObjectURL(data);
          aLink.click();
        });
      } else {
        this.$refs.cropper.getCropData(data => {
          this.downImg = data;
          aLink.href = data;
          aLink.click();
        });
      }
    },

    // 上传图片
    uploadImg(e) {
      // todo： 选择上传的图片  e.target.value 本地绝对路径
      var file = e.target.files[0];
      if (!/\.(gif|jpg|jpeg|png|bmp|GIF|JPG|PNG)$/.test(e.target.value)) {
        this.$notify.error({
          title: "上传失败",
          message: "图片类型必须是.gif,jpeg,jpg,png,bmp中的一种"
        });
        return false;
      }
      // 开始截图
      this.$refs.cropper.startCrop();
      /**
       * | readAsArrayBuffer(file) | 按字节读取文件内容，结果用ArrayBuffer对象表示 |
        | readAsBinaryString(file) | 按字节读取文件内容，结果为文件的二进制串 |
        | readAsDataURL(file) | 读取文件内容，结果用data:url的字符串形式表示 readAsDataURL会将文件内容进行base64编码后输出 |
        | readAsText(file,encoding) | 按字符读取文件内容，结果用字符串形式表示  readAsText(file,encoding)可按指定编码方式读取文件 |
        | abort() | 终止文件读取操作 |
       */
      var reader = new FileReader();
      // todo： onload : 读取文件内容完成时 的回调函数
      /**
       * | onabort | 当读取操作被中止时调用 |
        | onerror | 当读取操作发生错误时调用 |
        | onload | 当读取操作成功完成时调用 |
        | onloadend | 当读取操作完成时调用，无论成功或失败 |
        | onloadstart | 当读取操作开始时调用 |
        | onprogress | 在读取数据过程中周期性调用 |
       */
      reader.onload = e => {
        let data;
        if (typeof e.target.result === "object") {
          // 把Array Buffer转化为blob 如果是base64不需要
          data = window.URL.createObjectURL(new Blob([e.target.result]));
        } else {
          data = e.target.result;
        }
        this.option.img = data;
      };

      // todo：读取文件内容并且转化为base64
      // reader.readAsDataURL(file);
      // todo: 转化为blob
      reader.readAsArrayBuffer(file);
    },

    /**
     * 把图片存到服务器
     */
    upToServer(type = "blob") {
      if (type === "blob") {
        this.$refs.cropper.getCropBlob(data => {
          var img = window.URL.createObjectURL(data);
          const fmData = new FormData();
          fmData.append("cover", img);
          this.api.post("cover", fmData).then(res => {
            if (res.status === 200) {
              this.modelSrc = res.data.data.url;
              this.$notify({
                title: 'Success',
                message: '封面上传成功！',
                type: 'success',
                duration: 2000
              });
              this.dialogVisible = false;
              // 父组件回调
              this.$emit('upcover', res.data.data);
            } else {
              this.$notify.error({
                title: "错误",
                message: "上传失败！"
              });
            }
          });
        });
      } else {
        this.$refs.cropper.getCropData(data => {
          const fmData = new FormData();
          fmData.append("cover", data);
          this.api.post("cover", fmData).then(res => {
            if (res.status === 200) {
              this.modelSrc = res.data.data.url;
              this.$notify({
                title: 'Success',
                message: '封面上传成功！',
                type: 'success',
                duration: 2000
              });
              this.dialogVisible = false;
              // 父组件回调
              this.$emit('upcover', res.data.data);
            } else {
              this.$notify.error({
                title: "错误",
                message: "上传失败！"
              });
            }
          });
        });
      }
    },

    // 上传图片的回调函数 返回结果success, error
    imgLoad(msg) {
      console.log(msg);
    }
  }
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
}

.cut {
  width: 500px;
  height: 500px;
  margin: 30px auto;
}

.c-item {
  max-width: 800px;
  margin: 10px auto;
  margin-top: 20px;
}

.content {
  margin: auto;
  max-width: 1200px;
  margin-bottom: 100px;
}

.test-button {
  display: flex;
  flex-wrap: wrap;
  align-content: center;
  justify-content: center;
}

.btn {
  display: inline-block;
  line-height: 1;
  white-space: nowrap;
  cursor: pointer;
  background: #fff;
  border: 1px solid #c0ccda;
  color: #1f2d3d;
  text-align: center;
  box-sizing: border-box;
  outline: none;
  margin: 20px 10px 0px 0px;
  padding: 9px 15px;
  font-size: 14px;
  border-radius: 4px;
  color: #fff;
  background-color: #50bfff;
  border-color: #50bfff;
  transition: all 0.2s ease;
  text-decoration: none;
  user-select: none;
}

.des {
  line-height: 30px;
}

code.language-html {
  padding: 10px 20px;
  margin: 10px 0px;
  display: block;
  background-color: #333;
  color: #fff;
  overflow-x: auto;
  font-family: Consolas, Monaco, Droid, Sans, Mono, Source, Code, Pro, Menlo,
    Lucida, Sans, Type, Writer, Ubuntu, Mono;
  border-radius: 5px;
  white-space: pre;
}

.show-info {
  margin-bottom: 50px;
}

.show-info h2 {
  line-height: 50px;
}

/*.title, .title:hover, .title-focus, .title:visited {
        color: black;
      }*/

.title {
  display: block;
  text-decoration: none;
  text-align: center;
  line-height: 1.5;
  margin: 20px 0px;
  background-image: -webkit-linear-gradient(
    left,
    #3498db,
    #f47920 10%,
    #d71345 20%,
    #f7acbc 30%,
    #ffd400 40%,
    #3498db 50%,
    #f47920 60%,
    #d71345 70%,
    #f7acbc 80%,
    #ffd400 90%,
    #3498db
  );
  color: transparent;
  -webkit-background-clip: text;
  background-size: 200% 100%;
  animation: slide 5s infinite linear;
  font-size: 40px;
}

.test {
  height: 500px;
}

.model {
  position: fixed;
  z-index: 10;
  width: 100vw;
  height: 100vh;
  overflow: auto;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.8);
}

.model-show {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100vw;
  height: 100vh;
}

.model img {
  display: block;
  margin: auto;
  max-width: 80%;
  user-select: none;
  background-position: 0px 0px, 10px 10px;
  background-size: 20px 20px;
  background-image: linear-gradient(
      45deg,
      #eee 25%,
      transparent 25%,
      transparent 75%,
      #eee 75%,
      #eee 100%
    ),
    linear-gradient(45deg, #eee 25%, white 25%, white 75%, #eee 75%, #eee 100%);
}

.c-item {
  display: block;
  user-select: none;
}

@keyframes slide {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: -100% 0;
  }
}

.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 100%;
  height: 100%;
  line-height: 100px;
  min-width: 110px;
  text-align: center;
}
.cover {
  text-align: center;
}
</style>

