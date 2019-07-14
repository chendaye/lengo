<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="listQuery.title"
        placeholder="Title"
        style="width: 200px;"
        prefix-icon="el-icon-search"
        @input="handleFilter"
      />
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
      <el-table-column label="Cover" width="100">
        <template slot-scope="{row}">
          <img :src="baseApi+row.cover" style="width: 65px;hight:65px">
        </template>
      </el-table-column>
      <el-table-column label="Title" width="150" align="center">
        <template slot-scope="{row}">
          <span>{{ row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Abstract" width="225" align="center">
        <template slot-scope="{row}">
          <span>{{ row.abstract }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Markdown" width="300" align="center">
        <template slot-scope="{row}">
          <span>{{ row.content | spliceArticle }}</span>
        </template>
      </el-table-column>
      <el-table-column label="View" width="60" align="center">
        <template slot-scope="{row}">
          <span>{{ row.view }}</span>
        </template>
      </el-table-column>
      <el-table-column label="评论" width="60" align="center">
        <template slot-scope="{row}">
          <span>{{ row.comment }}</span>
        </template>
      </el-table-column>
      <el-table-column label="作者" width="120" align="center">
        <template slot-scope="{row}">
          <span>{{ row.user_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Status" class-name="status-col" width="100">
        <template slot-scope="{row}">
          <el-tag :type="row.draft | statusFilter">
            {{ row.draft | statusName }}
          </el-tag>
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
          <el-button type="success" size="mini" @click="handlePublish(row)">Publish</el-button>
          <el-button type="primary" size="mini">
            <router-link :to="{ name: 'NoteCreate', params: { id: row.id }}">Edit</router-link>
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
import crud from '@/api/crud';
import waves from '@/directive/waves'; // waves directive
import Pagination from '@/components/Pagination';
import { deleteItem, updateItem } from '@/utils/index';
const wtuCrud = crud.factory('wtu');

export default {
  name: 'Article',
  components: { Pagination },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        0: 'success',
        1: 'info'
      }
      return statusMap[status]
    },
    statusName(status) {
      const statusMap = {
        0: '发表',
        1: '草稿'
      }
      return statusMap[status]
    },
    spliceArticle(data) {
      return data.substring(0, 32) + '......';
    }
  },
  data() {
    return {
      baseApi: process.env.VUE_APP_PIC,
      dataForm: {
        tag: ''
      },
      // table
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
        order: {},
        where: {},
        title: null
      },
      dialogStatus: ''
    };
  },
  created() {
    this.getList();
  },
  methods: {
    submitForm(formName) {
      if (this.dialogStatus === 'create') {
        this.createData();
      } else {
        this.updateData();
      }
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },

    // table
    getList() {
      this.listLoading = true;
      this.listQuery.order = { id: 'desc', created_at: 'asc' };
      this.listQuery.where.created_at = { op: '!=', va: '', ex: 'cp' };
      if (this.listQuery.title !== null) {
        this.listQuery.where.title = { op: 'like', va: '%' + this.listQuery.title + '%', ex: 'cp' };
      }
      wtuCrud.get('indexArticle', this.listQuery).then(res => {
        this.list = res.data.data;
        this.total = res.data.total;
        console.log(this.listQuery);

        this.listLoading = false;
      });
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },
    resetDataForm() {
      this.dataForm = {
        id: null,
        tag: null
      }
    },
    // 删除标签
    handleDelete(row) {
      wtuCrud
        .post('articleDel', {
          where: { id: { op: '=', va: row.id, ex: 'cp' }}
        })
        .then(res => {
          if (res.status === 200) {
            deleteItem(this.list, row.id);
            this.$notify({
              title: 'Success',
              message: 'Delete Successfully',
              type: 'success',
              duration: 2000
            });
          }
        });
    },
    // 发布文章
    handlePublish(row) {
      this.$confirm('确定发表此文章此?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        wtuCrud.post('articleUpdate', {
          where: { id: { op: '=', va: row.id, ex: 'cp' }},
          data: { draft: 0 }
        }).then(res => {
          row.draft = 0;
          updateItem(this.list, row);
          this.$notify({
            title: 'Success',
            message: 'Delete Successfully',
            type: 'success',
            duration: 2000
          });
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消发布'
        });
      });
    },
    // 行颜色
    tableRowClassName({ row, rowIndex }) {
      if (row.draft === 1) {
        return 'warning-row';
      } else {
        return 'success-row';
      }
    }
  }
};
</script>

<style scoped>

</style>
