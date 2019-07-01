<template>
  <div>
    <el-input
      v-model="filterText"
      placeholder="输入关键字进行过滤"
    />

    <el-tree
      ref="tree"
      class="filter-tree"
      :props="defaultProps"
      :filter-node-method="filterNode"
      :data="data"
      node-key="id"
    >
      <span slot-scope="{ node, data }" class="custom-tree-node">
        <span>{{ node.label }}</span>
        <span>

          <el-tooltip class="item" effect="dark" content="添加功能权限" placement="top">
            <i class="el-icon-circle-plus" @click.stop="() => append(data)" />
          </el-tooltip>
          <el-tooltip class="item" effect="dark" content="编辑" placement="top">
            <i class="el-icon-edit" @click.stop="() => append(data)" />
          </el-tooltip>

          <el-tooltip class="item" effect="dark" content="删除" placement="top">
            <i class="el-icon-delete" @click.stop="() => append(data)" />
          </el-tooltip>
        </span>
      </span>
    </el-tree>
  </div>
</template>

<script>
import crud from '@/api/crud';
const wtuCrud = crud.factory('wtu');

let id = 1000;
export default {

  data() {
    return {
      filterText: '',
      defaultProps: {
        children: 'children',
        label: 'label'
      },
      data: [{
        id: 1,
        label: '一级 1',
        children: [{
          id: 4,
          label: '二级 1-1',
          children: [{
            id: 9,
            label: '三级 1-1-1'
          }, {
            id: 10,
            label: '三级 1-1-2'
          }]
        }]
      }, {
        id: 2,
        label: '一级 2',
        children: [{
          id: 5,
          label: '二级 2-1'
        }, {
          id: 6,
          label: '二级 2-2'
        }]
      }, {
        id: 3,
        label: '一级 3',
        children: [{
          id: 7,
          label: '二级 3-1'
        }, {
          id: 8,
          label: '二级 3-2'
        }]
      }]
    };
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    }
  },

  created() {
    wtuCrud.get('tree', {}).then(res => {
      this.data = res.data.data;
      console.log(res.data.data);
    })
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.label.indexOf(value) !== -1;
    },
    append(data) {
      const newChild = { id: id++, label: 'testtest', children: [] };
      if (!data.children) {
        this.$set(data, 'children', []);
      }
      data.children.push(newChild);
    },

    remove(node, data) {
      const parent = node.parent;
      const children = parent.data.children || parent.data;
      const index = children.findIndex(d => d.id === data.id);
      children.splice(index, 1);
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
