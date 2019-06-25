<template>
  <div class="app-container">
    <!--新建-->
    <div class="filter-container">
      <el-card class="filter-container">
        <el-form :model="listQuery" :inline="true" label-position="right">
          <el-form-item label="项目名称">
            <el-input
              v-model="listQuery.conditions.project_name"
              style="width: 200px"
              class="filter-item"
              placeholder="项目名称"
            />
          </el-form-item>
          <el-button class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">搜索</el-button>
          <el-button
            type="primary"
            icon="el-icon-circle-plus"
            style="margin-bottom:10px;"
            @click="handleAdd"
          >新建</el-button>
        </el-form>
      </el-card>
    </div>
    <el-dialog :title="dialogTitle" :visible.sync="dialogVisible" width="90%">
      <el-form
        ref="dialogForm"
        :rules="rules"
        :model="dialogFormData"
        label-position="right"
        label-width="120px"
      >
        <el-row :gutter="0">
          <el-col :md="8">
            <el-form-item label="项目名称" prop="project_name">
              <el-input v-model="dialogFormData.project_name" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="公司名称" prop="company_id">
              <el-select v-model="dialogFormData.company_id" @change="company_change" :loading="loading" filterable placeholder="请选择">
                <el-option
                  v-for="item in CompanyList"
                  :key="item.company_id"
                  :label="item.company_name"
                  :value="item.company_id" />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="联系电话" prop="telephone">
              <el-input v-model="dialogFormData.telephone" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="添加人" prop="project_add_user_id">
              <el-input v-model="dialogFormData.project_add_user_id" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="项目是否可用" prop="is_project_enabled">
              <el-select v-model="dialogFormData.is_project_enabled" placeholder="请选择">
                <el-option key="0" label="否" value="0"/>
                <el-option key="1" label="可用" value="1"/>
                <el-option key="2" label="待审核" value="2"/>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="虚拟项目部" prop="is_project_virtual">
              <el-switch
                v-model="dialogFormData.is_project_virtual"
                :active-value="1"
                :inactive-value="0"
              />
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="是否默认项目" prop="is_project_default">
              <el-switch
                v-model="dialogFormData.is_project_default"
                :active-value="1"
                :inactive-value="0"
              />
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="联系人" prop="contact">
              <el-input v-model="dialogFormData.contact" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="项目地址" prop="address">
              <el-input v-model="dialogFormData.address" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="8">
            <el-form-item label="项目经理" prop="manage">
              <el-input v-model="dialogFormData.manage" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="16">
            <el-form-item label="审核失败原因" prop="failed_reason">
              <el-input type="textarea" v-model="dialogFormData.failed_reason" auto-complete="off"/>
            </el-form-item>
          </el-col>
          <el-col :md="12">
            <el-form-item label="缩略图" prop="project_file_breviary">
              <el-upload
                class="avatar-uploader"
                :action="UPLOAD_URL"
                :data="{devicetype:'app'}"
                :show-file-list="false"
                :on-success="(res)=>{dialogFormData.project_file_breviary = res.data.url_name}"
                :before-upload="beforePicUpload">
                <img v-if="dialogFormData.project_file_breviary" :src="FILE_PATH+dialogFormData.project_file_breviary" class="avatar">
                <i v-else class="el-icon-plus avatar-uploader-icon"/>
              </el-upload>
            </el-form-item>
          </el-col>
          <el-col :md="12">
            <el-form-item label="项目文件" prop="project_file_url">
              <el-upload
                :action="UPLOAD_URL"
                :data="{devicetype:'app'}"
                :on-preview="handlePreview"
                :on-remove="handleRemove"
                :on-success="handleSuccess"
                :before-remove="beforeRemove"
                multiple
                :limit="5"
                :on-exceed="handleExceed"
                :file-list="dialogFormData.project_file_url">
                <el-button size="small" type="primary">点击上传</el-button>
                <div slot="tip" class="el-upload__tip">最多上传5个文件</div>
              </el-upload>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submit">确 定</el-button>
      </div>
    </el-dialog>
    <!--新建-->

    <!--table-->
    <el-table v-loading="listLoading" :data="list" border stripe fit style="width: 100%">
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.project_id }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="80px" label="项目名称">
        <template slot-scope="scope">
          <span>{{ scope.row.project_name }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="80px" label="公司名称">
        <template slot-scope="scope">
          <span>{{ scope.row.company_name }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="80px" label="联系人">
        <template slot-scope="scope">
          <span>{{ scope.row.contact }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="80px" label="联系电话">
        <template slot-scope="scope">
          <span>{{ scope.row.telephone }}</span>
        </template>
      </el-table-column>
      <el-table-column min-width="80px" label="项目地址">
        <template slot-scope="scope">
          <span>{{ scope.row.address }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" width="320">
        <template slot-scope="scope">
          <el-button
            type="primary"
            icon="el-icon-edit"
            size="mini"
            @click="handleEdit(scope.row)"
          >编辑</el-button>
          <el-button size="mini" type="danger" @click="handleDel(scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div class="pagination-container">
      <el-pagination
        :total="total"
        :current-page="listQuery.page"
        :page-size="listQuery.limit"
        :page-sizes="[10, 20, 30, 50]"
        background
        layout="total, sizes, prev, pager, next, jumper"
        @current-change="handleCurrentChange"
        @size-change="handleSizeChange"
      />
    </div>
  </div>
</template>

<script>
import crud from "@/api/crud";
import { deleteItem, updateItem } from "@/utils/index";
import { mapState } from "vuex";
const appCrud = crud.factory("project");
const companyCrud = crud.factory("company");
export default {
  data() {
    return {
      center: { lng: 0, lat: 0 },
      position: { lng: 0, lat: 0 },
      zoom: 3,
      keyword: '',
      dialogVisible: false,
      dialogType: "",
      dialogTextMap: {
        add: "新增项目",
        edit: "编辑项目"
      },
      dialogFormData: this.getEmptyFormData(),
      rules: {
        project_name: [{ required: true, message: "请输入项目名称", trigger: "blur" }],
        company_id: [{ required: true, message: "请选择公司名称", trigger: "change" }]
      },
      total: 0,
      list: [],
      listLoading: true,
      loading: false,
      listQuery: {
        conditions: {},
        page: 1,
        limit: 10
      }
    };
  },
  computed: {
    ...mapState({
      CompanyList: state => state.company.list
    }),
    dialogTitle() {
      return this.dialogTextMap[this.dialogType];
    }
  },
  created() {
    this.UPLOAD_URL = process.env.VUE_APP_UPLOAD_URL + '/index/uploadfile';
    this.FILE_PATH = process.env.VUE_APP_FILE_PATH + '/';
    this.$store.dispatch("company/getList");
    this.getList();
  },
  methods: {
    touchmap(e) {
      // console.log(e.target.Oe.lng)
      // console.log(e.target.Oe.lat)
      this.position.lng = e.point.lng;
      this.position.lat = e.point.lat;
    },
    handler({ BMap, map }) {
      console.log(BMap, map);
      this.center.lng = 116.404;
      this.center.lat = 39.915;
      this.zoom = 15;
    },
    company_change(val) {
      let obj = {};
      obj = this.CompanyList.find((item) => {
        return item.company_id === val;
      });
      this.dialogFormData.company_name = obj.company_name;
    },
    handleSuccess(response, file, fileList) {
      this.dialogFormData.project_file_url = fileList;
    },
    handleRemove(file, fileList) {
      this.dialogFormData.project_file_url = fileList;
    },
    handlePreview() {
      // console.log(this.dialogFormData.fileList);
    },
    handleExceed(files, fileList) {
      this.$message.warning(`当前限制选择 5 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
    },
    beforeRemove(file) {
      return this.$confirm(`确定移除 ${file.name}？`);
    },
    remoteMethod(query) {
      if (query !== '') {
        this.loading = true;
        var condition = {};
        if (query) {
          condition.company_name = ['LIKE', '%' + query + '%'];
        }
        companyCrud.getList(condition, 1, 10000).then(res => {
          this.loading = false;
          this.CompanyList = res.data.data;
        });
      } else {
        this.CompanyList = [];
      }
    },
    beforePicUpload(file) {
      const isJPG = file.type === 'image/jpeg';
      const isPNG = file.type === 'image/png';
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!(isJPG || isPNG)) {
        this.$message.error('上传头像图片只能是 JPG或PNG 格式!');
      }
      if (!isLt2M) {
        this.$message.error('上传头像图片大小不能超过 2MB!');
      }
      return (isJPG || isPNG) && isLt2M;
    },
    getList() {
      this.listLoading = true;
      let condition = {};
      condition = Object.assign({}, this.listQuery.conditions);
      if (condition.project_name) {
        condition.project_name = ['LIKE', '%' + this.listQuery.conditions.project_name + '%'];
      }
      appCrud.getList(condition, this.listQuery.page, this.listQuery.limit).then(res => {
        this.total = res.data.total;
        this.list = res.data.data;
        this.listLoading = false;
      });
    },
    handleDel(row) {
      this.$confirm(
        "此操作将永久删除该公司“" + row.company_name + "”, 是否继续?",
        "提示",
        {
          confirmButtonText: "删除",
          cancelButtonText: "取消",
          type: "warning"
        }
      ).then(() => {
        appCrud.del(row).then(() => {
          deleteItem(this.list, row.id);
          this.$message({
            type: "success",
            message: "删除成功!"
          });
        });
      }).catch(() => {
        this.$message({
          type: "info",
          message: "已取消删除"
        });
      });
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },

    handleSizeChange(val) {
      this.listQuery.limit = val;
      this.getList();
    },
    handleCurrentChange(val) {
      // 当前分页变化
      this.listQuery.page = val;
      this.getList();
    },
    getEmptyFormData() {
      return {
        project_file_breviary: "",
        project_file_url: [],
        is_project_default: 1
      };
    },
    handleAdd() {
      this.dialogVisible = true;
      this.dialogType = "add";
      this.dialogFormData = this.getEmptyFormData();
      this.$nextTick(() => {
        this.$refs["dialogForm"].clearValidate();
      });
    },
    handleEdit(item) {
      this.dialogVisible = true;
      this.dialogType = "edit";
      this.dialogFormData = item;
      this.$nextTick(() => {
        this.$refs["dialogForm"].clearValidate();
      });
    },
    submit() {
      this.$refs["dialogForm"].validate(valid => {
        if (valid) {
          if (this.dialogType === "add") {
            this.add();
          }

          if (this.dialogType === "edit") {
            this.update();
          }
        }
      });
    },
    // 增加应用
    add() {
      appCrud.add(this.dialogFormData).then(res => {
        this.dialogFormData.project_id = res.data.project_id;
        this.list.unshift(this.dialogFormData);
        this.$message({
          type: "success",
          message: "添加成功"
        });
        this.dialogVisible = false;
      });
    },
    update() {
      appCrud.update(this.dialogFormData).then(res => {
        if (res.code === 2000) {
          updateItem(this.list, this.dialogFormData);
          this.dialogVisible = false;
          this.$message({
            message: "角色已编辑修改",
            type: "success"
          });
        } else {
          this.$message({
            message: res.message,
            type: "error"
          });
        }
      });
    }
  }
};
</script>
<style>
  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409EFF;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
  .bm-view {
    width: 80%;
    height: 500px;
  }
</style>
