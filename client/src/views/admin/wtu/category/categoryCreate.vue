<template>
  <el-row :gutter="10" class="app-container">
    <el-col :span="16">
      <tree :is-create="true" :is-drag="true" :is-root="true" />
    </el-col>
  </el-row>
</template>

<script>
import tree from '@/components/Tree/index';

export default {
  components: {
    tree
  },
  data() {
    return {

    }
  },

  computed: {
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    }
  },
  created() {
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.label.indexOf(value) !== -1;
    }
  }

};
</script>

<style scope>
.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
}
.app-container {
  /* background-color: rgb(92, 168, 73) */
}
</style>
