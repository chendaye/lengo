<template>
  <div class="components-container">
    <pan-thumb :image="image" />

    <el-button
      type="success"
      plain
      icon="upload"
      style="position: absolute;bottom: 15px;margin-left: 40px;"
      @click="imagecropperShow=true"
    >选择图片</el-button>

    <image-cropper
      v-show="imagecropperShow"
      :key="imagecropperKey"
      :width="300"
      :height="300"
      :url="url"
      lang-type="en"
      @close="close"
      @crop-upload-success="cropSuccess"
    />
  </div>
</template>

<script>
import ImageCropper from '@/components/ImageCropper';
import PanThumb from '@/components/PanThumb';

export default {
  name: 'AvatarUploadDemo',
  components: { ImageCropper, PanThumb },
  data() {
    return {
      imagecropperShow: false,
      imagecropperKey: 0,
      image: 'http://www.lengo.top:8888/group1/M00/00/00/rBIACF0Qm5mAQ03_AAIe6opn5zE3287916'
    };
  },
  computed: {
    url: function() {
      return process.env.VUE_APP_BASE_API + '/admin/rbac/avatar';
    }
  },
  created() {
    // let env = app.get('env')
    console.log(this.url);
  },
  methods: {
    cropSuccess(resData) {
      this.imagecropperShow = false;
      this.imagecropperKey = this.imagecropperKey + 1;
      this.image = resData.files.avatar;
    },
    close() {
      this.imagecropperShow = false;
    }
  }
};
</script>

<style scoped>
.avatar {
  width: 200px;
  height: 200px;
  border-radius: 50%;
}
</style>

