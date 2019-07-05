<template>
  <div>
    <el-input v-model="filterText" placeholder="输入关键字" />
    <el-tree
      v-if="treeData.length > 0"
      ref="tree"
      class="filter-tree"
      :props="mapProps"
      :filter-node-method="filterNode"
      :data="treeData"
      node-key="id"
      show-checkbox
    >
      <span slot-scope="{ node, data }" class="custom-tree-node">
        <span><b class="node-font">{{ node.label }}</b></span>
      </span>
    </el-tree>
  </div>
</template>

<script>
import crud from "@/api/crud";
const wtuCrud = crud.factory("wtu");
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      filterText: "",
      treeData: [
      ],
      mapProps: {
        children: "children",
        label: "desc"
      }
    };
  },
  computed: {
    // 当前登录的管理员id
    ...mapGetters(["admin_id"])
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    }
  },

  created() {
    wtuCrud.get("tree", {}).then(res => {
      if (res.status === 200) {
        this.treeData = res.data.data;
      } else {
        this.$message({
          message: "获取分类失败！",
          type: "error"
        });
      }
    })
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.desc.indexOf(value) !== -1;
    },
    handleNodeClick(data) {
      console.log(data);
    }
  }
};
</script>

<style scope>
.node-font{
  font-style: italic;
  font-size: 14px
}
</style>
