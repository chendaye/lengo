<template>
  <div>
    <div class="crop-demo">
      <img :src="cropImg" class="pre-img">
      <div class="crop-demo-btn">选择文章封面
        <input class="crop-input" type="file" name="image" accept="image/*" @change="setImage">
      </div>
    </div>

    <el-dialog title="裁剪图片" :visible.sync="dialogVisible" width="30%">
      <vue-cropper
        ref="cropper"
        :src="imgSrc"
        :ready="cropImage"
        :zoom="cropImage"
        :cropmove="cropImage"
        :info="option.info"
        :output-size="option.outputSize"
        :output-type="option.outputType"
        :can-move="option.canMove"
        :auto-crop="option.autoCrop"
        :auto-crop-width="option.autoCropWidth"
        :auto-crop-height="option.autoCropHeight"
        :fixed-box="option.fixedBox"
        :original="option.original"
        :info-true="option.infoTrue"
        :center-box="option.centerBox"
        :can-move-box="option.canMoveBox"
        :can-scale="option.canScale"
        :fixed="option.fixed"
        :fixed-number="option.fixedNumber"
        style="width:100%;height:300px;"
      />
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelCrop">取 消</el-button>
        <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';
export default {
  name: 'Upload',
  components: {
    VueCropper
  },
  data: function() {
    return {
      defaultSrc: null,
      fileList: [],
      imgSrc: '',
      cropImg: '',
      dialogVisible: false,

      option: {
        outputSize: 1, // 裁剪生成图片的质量 0.1 - 1
        outputType: 'jepg', //	裁剪生成图片的格式 jpeg || png || webp
        canScale: true, // 图片是否允许滚轮缩放 默认true
        autoCrop: true, // 是否默认生成截图框 默认false
        canMove: true, // 上传图片是否可以移动 默认true
        autoCropWidth: 750, // 默认生成截图框宽度	容器的80%	0~max
        autoCropHeight: 256, // 默认生成截图框高度	容器的80%	0~max
        // fixedBox: true, // 固定截图框大小 不允许改变	false	true | false
        fixed: true, // 是否开启截图框宽高固定比例
        original: false, // 上传图片按照原始比例渲染	false	true | false
        infoTrue: true, // 为展示真实输出图片宽高 false 展示看到的截图框宽高	false	true | false
        centerBox: true, // 截图框是否被限制在图片里面	false	true | false
        canMoveBox: true, // 截图框能否拖动	true	true | false
        fixedNumber: [750, 256] // 截图框的宽高比例 [宽度, 高度]
      }
    }
  },
  created() {
    this.cropImg = this.defaultSrc;
  },
  methods: {
    setImage(e) {
      const file = e.target.files[0];
      if (!file.type.includes('image/')) {
        return;
      }
      const reader = new FileReader();
      reader.onload = (event) => {
        this.dialogVisible = true;
        this.imgSrc = event.target.result;
        this.$refs.cropper && this.$refs.cropper.replace(event.target.result);
      };
      reader.readAsDataURL(file);
    },
    cropImage() {
      this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
    },
    cancelCrop() {
      this.dialogVisible = false;
      this.cropImg = this.defaultSrc;
    },
    imageuploaded(res) {
      console.log(res)
    },
    handleError() {
      this.$notify.error({
        title: '上传失败',
        message: '图片上传接口上传失败，可更改为自己的服务器接口'
      });
    }
  }
}
</script>

<style scoped>
    .content-title{
        font-weight: 400;
        line-height: 50px;
        margin: 10px 0;
        font-size: 22px;
        color: #1f2f3d;
    }
    .pre-img{
        width: 150px;
        height: 150px;
        background: #f8f8f8;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    .crop-demo{
        display: flex;
        align-items: flex-end;
    }
    .crop-demo-btn{
        position: relative;
        width: 130px;
        height: 40px;
        line-height: 40px;
        padding: 0 20px;
        margin-left: 30px;
        background-color: #409eff;
        color: #fff;
        font-size: 14px;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .crop-input{
        position: absolute;
        width: 100px;
        height: 40px;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
    }
</style>
