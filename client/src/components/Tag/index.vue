<template>
  <div>
    <el-button
      :style="{'background-color': colorVal}"
      class="tag"
      plain
      round
      :icon="icon"
      :size="size[sizeVal]"
      @click="check"
    >{{ tag.tag }}</el-button>
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
    idVal: {
      // 标签id
      type: Number,
      required: true
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
        "antiquewhite",
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
        "lavenderblush",
        "lightsalmon",
        "lightyellow",
        "mistyrose"
      ],
      tag: {},
      icon: null
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
    this.$nextTick(function() {
      wtuCrud
        .get("detailTag", { where: { op: "id", va: this.idVal, ex: "=" }})
        .then(res => {
          if (res.status === 200) {
            this.tag = res.data;
          }
        });
    });
  },
  methods: {
    check() {
      if (this.icon === null) {
        this.icon = "el-icon-star-on";
      } else {
        this.icon = null;
      }
    }
  }
};
</script>

<style scope>
.tag {
  /* background-color:mistyrose */
}
</style>
