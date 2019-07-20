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
        "cornflowerblue",
        "darkcyan",
        "darkgreen",
        "darkkhaki",
        "darkseagreen",
        "forestgreen",
        "goldenrod",
        "rgb(187, 81, 20)",
        "rgb(233, 148, 99)",
        "rgb(212, 187, 75)",
        "rgb(152, 168, 57)",
        "rgb(89, 139, 30)",
        "rgb(141, 189, 86)",
        "rgb(72, 158, 83)",
        "rgb(47, 90, 88)",
        "rgb(34, 134, 129)",
        "rgb(34, 111, 134)",
        "rgb(64, 160, 189)",
        "rgb(137, 178, 231)",
        "rgb(50, 82, 124)",
        "rgb(50, 55, 124)",
        "rgb(138, 112, 199)",
        "rgb(119, 63, 63)",
        "rgb(190, 69, 69)",
        "rgb(179, 123, 151)",
        "rgb(189, 107, 148)"
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
  created() {
    if (this.content.checked === true) {
      this.icon = "el-icon-star-on";
      this.tagId = this.content.id;
    }
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
          this.$emit("search", this.tagId);
        }
        if (this.isCheck) {
          // 触发选中
          this.$emit("check", this.tagId);
        }
      } else {
        // 取消选中
        if (this.isSearch) {
          // 取消搜索
          this.$emit("nosearch", this.tagId);
        }
        if (this.isCheck) {
          // 取消选中
          this.$emit("nocheck", this.tagId);
        }
        this.icon = null;
        this.tagId = null;
        this.content.count--;
      }
    }
  }
};
</script>

<style scoped>
.tag {
  /* background-color:saddlebrown */
}
.test {
  color: rgb(187, 81, 20);
}
</style>
