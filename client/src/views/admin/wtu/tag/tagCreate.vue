<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button type="success" class="filter-item" icon="el-icon-edit" @click="handleCreate">新建标签</el-button>
      <el-input
        v-model="listQuery.tag"
        placeholder="Tag"
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
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column label="ID" prop="id" align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Tag" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.tag }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Count" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.count }}</span>
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

    <!-- tag弹窗 -->
    <el-dialog :title="dialogTitle[dialogStatus]" :visible.sync="dialogVisible">
      <el-form ref="dataForm" :model="dataForm" :rules="rules" label-width="100px">
        <el-form-item label="标签名" prop="tag">
          <el-input v-model="dataForm.tag" />
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
  name: 'Tag',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      dialogVisible: false,
      dialogTitle: {
        update: '更新标签',
        create: '创建标签'
      },
      dataForm: {
        tag: ''
      },
      rules: {
        name: [{ required: true, message: '请输标签名', trigger: 'blur' }]
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
        tag: null
      },
      dialogFormVisible: false,
      dialogStatus: '',

      dialogPvVisible: false,
      pvData: [],
      downloadLoading: false
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
      this.listQuery.order = { count: 'desc', id: 'desc' };
      this.listQuery.where.created_at = { op: '!=', va: '', ex: 'cp' };
      if (this.listQuery.tag !== null) {
        this.listQuery.where.tag = { op: 'like', va: '%' + this.listQuery.tag + '%', ex: 'cp' };
      }
      wtuCrud.get('indexTag', this.listQuery).then(res => {
        this.list = res.data.data;
        this.total = res.data.total;
        console.log(this.list);

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
          wtuCrud.post('addTag', this.dataForm).then(res => {
            if (res.status === 200) {
              console.log(res.data);
              updateItem(this.list, res.data.data);
              this.dialogVisible = false;
              this.$message({
                message: '创建标签成功！',
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
    // 更新标签
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
            .post('updateTag', {
              data: this.dataForm,
              where: { id: { op: '=', va: this.dataForm.id, ex: 'cp' }}
            })
            .then(res => {
              if (res.status === 200) {
                updateItem(this.list, this.dataForm);
                this.dialogVisible = false;
                this.$notify({
                  title: 'Success',
                  message: '标签更新成功！',
                  type: 'success',
                  duration: 2000
                });
              } else {
                this.$notify({
                  title: 'Success',
                  message: '标签更新失败！',
                  type: 'fail',
                  duration: 2000
                });
              }
            });
        }
      });
    },
    // 删除标签
    handleDelete(row) {
      this.$notify({
        title: 'Success',
        message: 'Delete Successfully',
        type: 'success',
        duration: 2000
      });
      wtuCrud
        .post('delTag', {
          where: { id: { op: '=', va: row.id, ex: 'cp' }}
        })
        .then(res => {
          if (res.status === 200) {
            deleteItem(this.list, row.id);
            console.log(res);
          }
        });
    }
  }
};
</script>

<style scoped>

</style>
