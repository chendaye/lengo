<template>
  <div>
    <el-row :gutter="5">
      <el-col :span="24">
        <div class="grid-content bg-purple">
          <el-input v-model="filterText" size="mini" placeholder="输入关键字" />
        </div>
      </el-col>
    </el-row>
    <el-row :gutter="5">
      <el-col :span="24">
        <div class="grid-content bg-purple">
          <div v-for="item in tags" :key="item.id" class="tag-content">
            <tag :size-val="size" :content="item" :is-check="true" @check="check" @nocheck="nocheck" />
          </div>
        </div>
      </el-col>
    </el-row>
  </div>

</template>

<script>
import tag from '@/components/Tag/index'
import crud from "@/api/crud";
import { bubbleItem } from '@/utils/index';
const wtuCrud = crud.factory("wtu");

export default {
  name: 'TagFilter',
  components: {
    tag
  },
  data: function() {
    return {
      // 筛选
      filterText: '',
      // 标签尺寸
      size: 'mini',
      // 所有标签
      tags: [],
      list: []
    }
  },
  watch: {
    filterText: function(newVal, oldVal) {
      this.tags = this.list.filter(item => {
        return item.tag.indexOf(newVal) > -1;
      });
      // wtuCrud
      //   .get("listTag", {
      //     order: { id: 'desc', created_at: 'asc' },
      //     where: { tag: { op: 'like', va: '%' + newVal + '%', ex: 'cp' }}
      //   })
      //   .then(res => {
      //     if (res.status === 200) {
      //       this.tags = res.data;
      //     }
      //   });
    }
  },
  created() {
    // 获取标签
    wtuCrud
      .get("listTag", {
        order: { id: 'desc', created_at: 'asc' },
        where: { created_at: { op: '!=', va: '', ex: 'cp' }}
      })
      .then(res => {
        if (res.status === 200) {
          this.tags = res.data;
          this.list = res.data;
        }
      });
  },
  methods: {
    // 标签选中事件
    check(data) {
      // 把选中的元素移动到最前面
      bubbleItem(this.tags, data);
      // 出发父组件
      this.$emit('check', data);
    },
    // 取消选中事件
    nocheck(data) {
      // 把取消选中的元素，移动到最后面
      bubbleItem(this.tags, data, false);
      // 出发父组件
      this.$emit('nocheck', data);
    }

  }
}
</script>

<style scope>
  .tag-content {
    float:left; margin:6px;
  }
</style>
