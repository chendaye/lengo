<template>
  <div class="app-container">
    <div class="filter-container">

      <el-row :gutter="10">
        <el-col :span="6"><div class="grid-content bg-purple" />
          <category :is-filter="true" :is-search="true" @searchCategory="searchCategory" />
        </el-col>
        <el-col :span="10"><div class="grid-content bg-purple" />
          <tag
            :is-search="true"
            @check="check"
            @nocheck="nocheck"
          />
        </el-col>
        <el-col :span="6"><div class="grid-content bg-purple" />
          <el-input
            v-model="title"
            placeholder="Title"
            size="mini"
            prefix-icon="el-icon-search"
          />
        </el-col>
        <el-col :span="2"><div class="grid-content bg-purple" />
          <el-button type="info" size="mini" plain @click="resetFilter">重置</el-button>
        </el-col></el-row>
    </div>

    <el-table
      v-loading="listLoading"
      :data="list"
      border
      fit
      :row-class-name="tableRowClassName"
      style="width: 100%"
    >
      <el-table-column label="ID" prop="id" align="center" width="60">
        <template slot-scope="{row}">
          <span>{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Cover" width="150">
        <template slot-scope="{row}">
          <img :src="baseApi+row.cover" style="width: 65px;hight:65px">
        </template>
      </el-table-column>
      <el-table-column label="Title" width="200" align="center">
        <template slot-scope="{row}">
          <span>{{ row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Abstract" width="275" align="center">
        <template slot-scope="{row}">
          <span>{{ row.abstract }}</span>
        </template>
      </el-table-column>
      <!-- <el-table-column label="Markdown" width="300" align="center">
        <template slot-scope="{row}">
          <span>{{ row.content | spliceArticle }}</span>
        </template>
      </el-table-column> -->
      <el-table-column label="View" width="110" align="center">
        <template slot-scope="{row}">
          <span>{{ row.view }}</span>
        </template>
      </el-table-column>
      <el-table-column label="评论" width="110" align="center">
        <template slot-scope="{row}">
          <span>{{ row.comment }}</span>
        </template>
      </el-table-column>
      <el-table-column label="作者" width="170" align="center">
        <template slot-scope="{row}">
          <span>{{ row.user_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Status" class-name="status-col" width="150">
        <template slot-scope="{row}">
          <el-tag :type="row.draft | statusFilter">{{ row.draft | statusName }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="更新时间" width="160" align="center">
        <template slot-scope="{row}">
          <span>{{ row.updated_at }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Actions"
        align="center"
        width="250"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row}">
          <el-button v-if="row.draft === 1" type="success" size="mini" @click="handlePublish(row)">Publish</el-button>
          <el-button type="primary" size="mini">
            <router-link :to="{ name: 'NoteCreate', query: {id: row.id }}">Edit</router-link>
          </el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row)">Delete</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination
      v-show="total>0"
      :total="total"
      :page.sync="listQuery.page"
      :limit.sync="listQuery.limit"
      @pagination="getList"
    />
  </div>
</template>

<script>
import crud from "@/api/crud";
import waves from "@/directive/waves"; // waves directive
import Pagination from "@/components/Pagination";
import { deleteItem, updateItem, son, findIndex } from "@/utils/index";
import category from "@/components/Tree/index";
import tag from "@/components/Tag/tagFilter";
const wtuCrud = crud.factory("wtu");

export default {
  name: "Article",
  components: { Pagination, category, tag },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        0: "success",
        1: "info"
      };
      return statusMap[status];
    },
    statusName(status) {
      const statusMap = {
        0: "发表",
        1: "草稿"
      };
      return statusMap[status];
    },
    spliceArticle(data) {
      if (data.length > 32) {
        return data.substring(0, 32) + "......";
      } else {
        return data;
      }
    }
  },
  data() {
    return {
      baseApi: process.env.VUE_APP_PIC,
      // table
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
        order: {},
        where: {}
      },
      title: '',
      tag: [],
      category: [],
      dialogStatus: ""
    };
  },
  watch: {
    title: function() {
      this.listQuery.where.title = this.title;
      this.handleFilter();
    },
    category: function() {
      if (this.category !== []) {
        let cate = [];
        // 分类
        cate = son(this.category);
        this.listQuery.where.category = cate;
        this.handleFilter();
      }
    },
    tag: function() {
      console.log('tag', this.tag);
      this.listQuery.where.tag = this.tag;
      this.handleFilter();
    }
  },
  created() {
    this.getList();
  },
  methods: {
    // table
    getList() {
      this.listLoading = true;
      wtuCrud.get("indexArticle", this.listQuery).then(res => {
        console.log('fuck', res)
        this.list = res.data.data;
        this.total = res.data.total;
        this.listLoading = false;
      });
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },
    // 删除文章
    handleDelete(row) {
      wtuCrud
        .post("articleDel", {
          where: { id: { op: "=", va: row.id, ex: "cp" }}
        })
        .then(res => {
          if (res.status === 200) {
            deleteItem(this.list, row.id);
            this.$notify({
              title: "Success",
              message: "Delete Successfully",
              type: "success",
              duration: 2000
            });
          }
        });
    },
    // 发布文章
    handlePublish(row) {
      this.$confirm("确定发表此文章此?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          wtuCrud
            .post("articleUpdate", {
              where: { id: { op: "=", va: row.id, ex: "cp" }},
              data: { draft: 0 }
            })
            .then(res => {
              row.draft = 0;
              updateItem(this.list, row);
              this.$notify({
                title: "Success",
                message: "Delete Successfully",
                type: "success",
                duration: 2000
              });
            });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消发布"
          });
        });
    },
    // 行颜色
    tableRowClassName({ row, rowIndex }) {
      if (row.draft === 1) {
        return "warning-row";
      } else {
        return "success-row";
      }
    },
    // 搜索分类
    searchCategory(data) {
      this.category = data;
    },
    // 标签搜搜
    check(data) {
      this.tag.push(data);
    },
    nocheck(data) {
      this.tag.splice(findIndex(this.tag, data), 1);
    },
    // 重置搜索
    resetFilter() {
      this.listQuery.where = {};
      this.handleFilter();
    }
  }
};
</script>

<style scoped>
</style>
