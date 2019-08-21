<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button type="success" class="filter-item" icon="el-icon-edit" @click="handleCreate">Add Link</el-button>
      <el-select v-model="listQuery.pid" placeholder="请选分类" @change="handleFilter">
        <el-option v-for="item in parents" :key="item.id" :label="item.name" :value="item.id" />
      </el-select>
      <el-input
        v-model="listQuery.name"
        placeholder="Name"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <el-button
        v-waves
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      >Search</el-button>

    </div>

    <el-table
      ref="filterTable"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :row-class-name="tableRowClassName"
    >
      <el-table-column
        label="ID"
        prop="id"
        align="center"
        width="80"
        :filters="[{text: '分类', value: 0}, {text: '链接', value: 1}]"
        :filter-method="filterHandler"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Name" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Desc" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.desc }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Link" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.link }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Pid" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.pid }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Created Date" width="300px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Actions"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)">Edit</el-button>
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

    <!-- link弹窗 -->
    <el-dialog :title="dialogTitle[dialogStatus]" :visible.sync="dialogVisible">
      <el-form ref="dataForm" :model="dataForm" :rules="rules" label-width="100px">
        <el-form-item label="所属分类" prop="pid">
          <el-select v-model="dataForm.pid" placeholder="请选分类">
            <el-option v-for="item in parents" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="链接名" prop="name">
          <el-input v-model="dataForm.name" />
        </el-form-item>
        <el-form-item label="链接" prop="link">
          <el-input v-model="dataForm.link" />
        </el-form-item>
        <el-form-item label="描述" prop="desc">
          <el-input v-model="dataForm.desc" type="textarea" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button
          type="primary"
          plain
          @click="submitForm('dataForm')"
        >{{ dialogTitle[dialogStatus] }}</el-button>
        <el-button type="info" plain @click="resetForm('dataForm')">重置</el-button>
        <el-button type="warning" plain @click="dialogVisible = false">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import crud from '@/api/crud';
import waves from '@/directive/waves'; // waves directive
import Pagination from '@/components/Pagination';
import { deleteItem, updateItem } from '@/utils/index';
const wtuCrud = crud.factory('wtu');

export default {
  name: 'Link',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      dialogVisible: false,
      dialogTitle: {
        update: '更新链接',
        create: '创建链接'
      },
      dataForm: {
        link: '',
        name: '',
        desc: '',
        pid: 0
      },
      rules: {
        name: [{ required: true, message: '请输链接名', trigger: 'blur' }],
        link: [{ required: true, message: '请输链接', trigger: 'blur' }],
        desc: [{ required: true, message: '请输链接描述', trigger: 'blur' }]
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
        name: null,
        pid: null
      },
      dialogStatus: '',
      // 一级链接分类
      parents: []
    };
  },
  created() {
    this.getList();
    this.getPLink();
  },
  methods: {
    filterHandler(value, row, column) {
      if (value === 0) {
        console.log(row)
        return row['pid'] === 0;
      } else {
        return row['pid'] > 0
      }
    },
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
    // 表格颜色
    tableRowClassName({ row, rowIndex }) {
      if (row.pid > 0) {
        return 'success-row';
      } else {
        return 'warning-row';
      }
    },

    // table
    getList() {
      this.listLoading = true;
      this.listQuery.order = { id: 'desc' };
      this.listQuery.where.created_at = { op: '!=', va: '', ex: 'cp' };
      if (this.listQuery.name !== null) {
        this.listQuery.where.name = { op: 'like', va: '%' + this.listQuery.name + '%', ex: 'cp' };
      }
      if (this.listQuery.pid !== null) {
        this.listQuery.where.pid = { op: '=', va: this.listQuery.pid, ex: 'cp' };
      }
      wtuCrud.get('indexLink', this.listQuery).then(res => {
        this.list = res.data.data;
        this.total = res.data.total;
        this.listLoading = false;
      });
    },
    // 获取一级分类
    getPLink() {
      wtuCrud.get('pLink', {
        where: { pid: {
          op: '=',
          va: 0,
          ex: 'cp'
        }},
        order: { id: 'desc' }
      }).then(res => {
        if (res.status === 200) {
          this.parents = res.data;
        }
      });
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },
    resetDataForm() {
      this.dataForm = {
        id: null,
        name: null,
        link: null,
        desc: null,
        pid: 0
      }
    },
    handleCreate() {
      this.dialogStatus = 'create';
      this.resetDataForm();
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    createData() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          wtuCrud.post('addLink', this.dataForm).then(res => {
            if (res.status === 200) {
              updateItem(this.list, res.data.data);
              if(res.data.data.pid === 0){
                updateItem(this.parents, res.data.data);
              }
              this.dialogVisible = false;
              this.$message({
                message: '创建链接成功！',
                type: 'success'
              });
            }
          });
        } else {
          this.$message({
            message: 'error submit!!',
            type: 'error'
          });
          return false;
        }
      });
    },
    // 更新链接
    handleUpdate(row) {
      this.dataForm = Object.assign({}, row); // copy obj
      this.dialogStatus = 'update';
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    updateData() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          wtuCrud
            .post('updateLink', {
              data: this.dataForm,
              where: { id: { op: '=', va: this.dataForm.id, ex: 'cp' }}
            })
            .then(res => {
              if (res.status === 200) {
                updateItem(this.list, this.dataForm);
                this.dialogVisible = false;
                this.$notify({
                  title: 'Success',
                  message: '链接更新成功！',
                  type: 'success',
                  duration: 2000
                });
              } else {
                this.$notify({
                  title: 'Success',
                  message: '链接更新失败！',
                  type: 'fail',
                  duration: 2000
                });
              }
            });
        }
      });
    },
    // 删除链接
    handleDelete(row) {
      wtuCrud
        .post('delLink', {
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
          } else {
            this.$notify({
              title: 'fail',
              message: 'Delete fail',
              type: 'error',
              duration: 2000
            });
          }
        });
    }
  }
};
</script>

<style scoped>
.el-table .warning-row {
    background: oldlace;
  }

  .el-table .success-row {
    background: #f0f9eb;
  }
</style>
