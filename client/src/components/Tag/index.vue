<template>
  <div>
    <el-button
      :style="{'background-color': colorVal}"
      round
      type="info"
      class="tag"
      :icon="icon"
      :size="size[sizeVal]"
      @click="check"
    >{{ content.tag }}[{{ content.count }}]</el-button>
  </div>
</template>

<script>
import crud from "@/api/crud";
const wtuCrud = crud.factory("wtu");

export default {
  name: "Tag",
  props: {
    sizeVal: {
      // size-val
      type: String,
      default: "mini"
    },
    content: {
      // 标签id
      type: Object,
      required: true
    },
    isSearch: {
      type: Boolean,
      default: false
    },
    isCheck: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      size: {
        medium: "medium",
        small: "small",
        mini: "mini"
      },
      color: [
        "blue",
        "brown",
        "burlywood",
        "cornflowerblue",
        "darkcyan",
        "darkgreen",
        "darkkhaki",
        "darkseagreen",
        "forestgreen",
        "goldenrod",
        "greenyellow",
        "crimson",
        "darkred",
        "olive",
        "orange",
        "saddlebrown"
      ],
      tag: {},
      icon: null,
      tagId: null
    };
  },
  computed: {
    // 随机颜色
    colorVal: function() {
      const len = this.color.length;
      const index = Math.floor(Math.random() * len);
      return this.color[index];
    }
  },
  created() {},
  mounted: function() {
    // 页面渲染完之后，再执行
    this.$nextTick(function() {
      // 获取标签detail
      // wtuCrud
      //   .get("detailTag", { where: { op: "id", va: this.idVal, ex: "cp" }})
      //   .then(res => {
      //     if (res.status === 200) {
      //       this.tag = res.data;
      //     }
      //   });
    });
  },
  methods: {
    check() {
      if (this.icon === null) {
        // 选中
        this.icon = "el-icon-star-on";
        this.tagId = this.content.id;
        this.content.count++;
        if (this.isSearch) {
          // 触发搜索
          this.$emit('search', this.tagId);
        }
        if (this.isCheck) {
          // 触发选中
          this.$emit('check', this.tagId);
        }
      } else {
        // 取消选中
        if (this.isSearch) {
          // 取消搜索
          this.$emit('nosearch', this.tagId);
        }
        if (this.isCheck) {
          // 取消选中
          this.$emit('nocheck', this.tagId);
        }
        this.icon = null;
        this.tagId = null;
        this.content.count--;
      }
    }
  }
};
</script>

<style scope>

.tag {
  /* background-color:saddlebrown */
}
</style>
