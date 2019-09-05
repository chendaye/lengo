<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button type="success" class="filter-item" icon="el-icon-edit" @click="handleCreate">Add Cover</el-button>
      <el-input
        v-model="listQuery.desc"
        placeholder="Desc"
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
      <el-button
        v-waves
        class="filter-item"
        type="warning"
        icon="el-icon-delete"
        @click="dialogDelVisible = true"
      >删除图片</el-button>
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
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Desc" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.desc }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Desc" min-width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.url }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Url" width="100">
        <template slot-scope="{row}">
          <img :src="baseApi+row.url" style="width: 65px;hight:65px">
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

    <!-- Cover弹窗 -->
    <el-dialog :title="dialogTitle[dialogStatus]" :visible.sync="dialogVisible" width="50%" hight="50%">
      <el-form ref="dataForm" :model="dataForm" :rules="rules" label-width="80px" label-position="top">
        <el-form-item label="描述" prop="desc">
          <el-input v-model="dataForm.desc" />
        </el-form-item>
        <el-form-item label="图片" prop="url">
          <!-- 封面图片上传 -->
          <cover ref="cover" :cover="dataForm.url" :select-cover="false" @upcover="upcover" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <div slot="footer" class="dialog-footer">
          <el-button
            type="primary"
            plain
            @click="submitForm('dataForm')"
          >{{ dialogTitle[dialogStatus] }}</el-button>
          <el-button type="info" plain @click="resetUrl()">重置</el-button>
          <el-button type="warning" plain @click="dialogVisible = false">取 消</el-button>
        </div>
      </div>
    </el-dialog>
    <!-- Cover弹窗 -->
    <!-- 删除图片 -->
    <el-dialog title="删除图片" :visible.sync="dialogDelVisible" width="50%" hight="50%">
      <el-form ref="dataDelForm" :model="dataDelForm" :rules="rulesDel" label-width="80px" label-position="top">
        <el-form-item label="图片地址" prop="url">
          <el-input v-model="dataDelForm.url" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <div slot="footer" class="dialog-footer">
          <el-button
            type="primary"
            plain
            @click="submitDelForm('dataDelForm')"
          >删除</el-button>
          <el-button type="warning" plain @click="dialogDelVisible = false">取 消</el-button>
        </div>
      </div>
    </el-dialog>
    <!-- 删除图片 -->

  </div>
</template>

<script>
import crud from '@/api/crud';
import waves from '@/directive/waves'; // waves directive
import Pagination from '@/components/Pagination';
import { deleteItem, updateItem } from '@/utils/index';
const wtuCrud = crud.factory('wtu');
import cover from "@/components/cover/index";

export default {
  name: 'Cover',
  components: { Pagination, cover },
  directives: { waves },
  data() {
    return {
      baseApi: process.env.VUE_APP_PIC,
      dialogVisible: false,
      dialogDelVisible: false,
      dialogTitle: {
        update: '更新封面',
        create: '上传封面'
      },
      dataForm: {
        url: null,
        desc: null
      },
      dataDelForm: {
        url: null
      },
      rules: {
        desc: [{ required: true, message: '请输封面描述', trigger: 'blur' }]
      },
      rulesDel: {
        url: [{ required: true, message: '请输入图片地址', trigger: 'blur' }]
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
        desc: null
      },
      dialogStatus: '',
      oldImg: null
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
    submitDelForm(formName) {
      this.dialogDelVisible = true;
      this.$refs['dataDelForm'].validate(valid => {
        if (valid) {
          this.imgDel(this.dataDelForm.url); // 删除原图
          this.$notify({
            title: 'Success',
            message: 'Delete Successfully',
            type: 'success',
            duration: 2000
          });
          this.dialogDelVisible = true;
        }
      });
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
      if (this.listQuery.desc !== null) {
        this.listQuery.where.desc = { op: 'like', va: '%' + this.listQuery.desc + '%', ex: 'cp' };
      }
      wtuCrud.get('indexCover', this.listQuery).then(res => {
        this.list = res.data.data;
        this.total = res.data.total;
        this.listLoading = false;
      });
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },
    resetDataForm() {
      this.dataForm = {
        url: null,
        desc: null
      }
    },
    resetUrl() {
      this.$refs.cover.resetUrl();
    },
    imgDel(url) {
      // 删除图片
      wtuCrud.post("imgDel", { sort: url }).then(res => {
        if (res.data.data) {
          this.$message.success("图片已经删除！");
        }
      });
    },
    handleCreate() {
      this.dialogStatus = 'create';
      this.dialogVisible = true;
      this.resetDataForm();
    },
    // 更新封面
    handleUpdate(row) {
      this.dialogStatus = 'update';
      this.dialogVisible = true
      this.dataForm = Object.assign({}, row); // copy obj
      this.oldImg = row.url;
    },
    createData() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          wtuCrud.post('addCover', this.dataForm).then(res => {
            if (res.status === 200) {
              updateItem(this.list, res.data.data);
              this.dialogVisible = false;
              this.$message({
                message: '创建封面成功！',
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
    updateData() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          wtuCrud
            .post('updateCover', {
              data: this.dataForm,
              where: { id: { op: '=', va: this.dataForm.id, ex: 'cp' }}
            })
            .then(res => {
              if (res.status === 200) {
                if (this.oldImg !== null) {
                  this.imgDel(this.oldImg);
                }
                updateItem(this.list, this.dataForm);
                this.dialogVisible = false;
                this.$notify({
                  title: 'Success',
                  message: '封面更新成功！',
                  type: 'success',
                  duration: 2000
                });
              } else {
                this.$notify({
                  title: 'Success',
                  message: '封面更新失败！',
                  type: 'fail',
                  duration: 2000
                });
              }
            });
        }
      });
    },
    // 删除封面
    handleDelete(row) {
      wtuCrud
        .post('delCover', {
          where: { id: { op: '=', va: row.id, ex: 'cp' }}
        })
        .then(res => {
          if (res.status === 200) {
            this.imgDel(row.url); // 删除原图
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
    },
    // 封面上传
    upcover(data) {
      this.dataForm.url = data.sortUrl;
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
