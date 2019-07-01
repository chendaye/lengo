<template>
  <el-row :gutter="10" class="app-container">
    <el-col :span="24">
      <el-col :span="6">
        <tree />
      </el-col>
      <el-col :span="18" /></el-col>
  </el-row>
</template>

<script>
import crud from '@/api/crud';
import tree from './components/Tree/index';

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

<style>
.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
}
</style>
