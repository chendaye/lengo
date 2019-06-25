<template>
  <el-row :gutter="10" class="app-container">
    <el-col :span="24">
      <el-card>
        <el-form ref="form" label-width="80px">
          <el-form-item label="选择应用">
            <el-select v-model="currentApp" placeholder="请选择应用">
              <el-option v-for="(item, index) in appList" :key="index" :label="item.name" :value="item.id" />
            </el-select>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
    <el-col :span="6">
      <el-card>
        <el-tree
          ref="permsTree"
          :data="treeData"
          :props="{label: 'name', isLeaf: (data, node) => {return data.is_leaf === 1}}"
          highlight-current
          node-key="id"
          @node-click="getMenuPerms"
        >
          <span slot-scope="{ node, data }" class="custom-tree-node">
            <span>{{ node.label }}</span>
            <span>
              <el-tooltip class="item" effect="dark" content="添加功能权限" placement="top">
                <i v-if="isLeaf(data)" class="el-icon-share" @click.stop="() => handleAddPerms(data)" />
              </el-tooltip>
              <el-tooltip class="item" effect="dark" content="添加根节点" placement="top">
                <i v-if="data.pid === 0" class="el-icon-menu" @click.stop="() => handleAddMenu({id: 0, name: '根节点'})" />
              </el-tooltip>
              <el-tooltip class="item" effect="dark" content="添加子菜单" placement="top">
                <i class="el-icon-circle-plus" @click.stop="() => handleAddMenu(data)" />
              </el-tooltip>
              <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                <i class="el-icon-edit" @click.stop="() => handleEditMenu(data)" />
              </el-tooltip>
              <el-tooltip class="item" effect="dark" content="删除" placement="top">
                <i v-if="isLeaf(data)" class="el-icon-delete" @click.stop="() => handleDelMenu(data)" />
              </el-tooltip>
            </span>
          </span>
        </el-tree>
      </el-card>
    </el-col>
    <el-col :span="18">
      <el-table :data="permsList" stripe border>
        <el-table-column label="序号" prop="id"/>
        <el-table-column label="权限名称" prop="name"/>
        <el-table-column label="权限标识" prop="flag"/>
        <el-table-column label="操作" prop="id">
          <template slot-scope="scope">
            <el-button type="info" size="small" @click="handleEditPerms(scope.row)">编辑</el-button>
            <el-button type="danger" size="small" @click="handleDelPerms(scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-col>
    <el-dialog :title="addMenuDialogTitle" :visible.sync="addMenuDialogVisible" width="860px" center>
      <el-form
        ref="addMenuDialogForm"
        :rules="addMenuDialogRules"
        :model="addMenuDialogFormData"
        label-position="right"
        label-width="120px"
      >
        <el-row :gutter="10">
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单名称" prop="name">
              <el-input
                v-model="addMenuDialogFormData.name"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单图标" prop="icon">
              <el-input
                v-model="addMenuDialogFormData.icon"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单路径" prop="flag">
              <el-input
                v-model="addMenuDialogFormData.flag"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="侧边栏显示" prop="show_in_sidebar">
              <el-switch
                v-model="addMenuDialogFormData.show_in_sidebar"
                :active-value="1"
                :inactive-value="0"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="所属应用" prop="app_id">
              <el-select v-model="currentApp" disabled placeholder="所属应用">
                <el-option v-for="(item, index) in appList" :key="index" :label="item.name" :value="item.id" />
              </el-select>
            </el-form-item>
          </el-col>
            <el-col :sm="24" :md="12">
            <el-form-item label="排序" prop="sort">
              <el-input
                v-model="addMenuDialogFormData.sort"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addMenuDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submitAddMenu">确 定</el-button>
      </div>
    </el-dialog>
    <el-dialog :title="editMenuDialogTitle" :visible.sync="editMenuDialogVisible" width="860px" center>
      <el-form
        ref="editMenuDialogForm"
        :rules="editMenuDialogRules"
        :model="editMenuDialogFormData"
        label-position="right"
        label-width="120px"
      >
        <el-row :gutter="10">
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单名称" prop="name">
              <el-input
                v-model="editMenuDialogFormData.name"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单图标" prop="icon">
              <el-input
                v-model="editMenuDialogFormData.icon"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单路径" prop="flag">
              <el-input
                v-model="editMenuDialogFormData.flag"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="侧边栏显示" prop="show_in_sidebar">
              <el-switch
                v-model="editMenuDialogFormData.show_in_sidebar"
                :active-value="1"
                :inactive-value="0"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="所属应用" prop="app_id">
              <el-select v-model="editMenuDialogFormData.app_id" disabled placeholder="所属应用">
                <el-option v-for="(item, index) in appList" :key="index" :label="item.name" :value="item.id" />
              </el-select>
            </el-form-item>
          </el-col>
           <el-col :sm="24" :md="12">
            <el-form-item label="排序" prop="sort">
              <el-input
                v-model="editMenuDialogFormData.sort"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editMenuDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submitEditMenu">确 定</el-button>
      </div>
    </el-dialog>
    <el-dialog :title="addPermsDialogTitle" :visible.sync="addPermsDialogVisible" width="860px" center>
      <el-form
        ref="addPermsDialogForm"
        :rules="addPermsDialogRules"
        :model="addPermsDialogFormData"
        label-position="right"
        label-width="120px"
      >
        <el-row :gutter="10">
          <el-col :sm="24" :md="12">
            <el-form-item label="权限名称" prop="name">
              <el-input
                v-model="addPermsDialogFormData.name"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单标识" prop="flag">
              <el-input
                v-model="addPermsDialogFormData.flag"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addPermsDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submitAddPerms">确 定</el-button>
      </div>
    </el-dialog>
    <el-dialog :title="editPermsDialogTitle" :visible.sync="editPermsDialogVisible" width="860px" center>
      <el-form
        ref="editPermsDialogForm"
        :rules="editPermsDialogRules"
        :model="editPermsDialogFormData"
        label-position="right"
        label-width="120px"
      >
        <el-row :gutter="10">
          <el-col :sm="24" :md="12">
            <el-form-item label="权限名称" prop="name">
              <el-input
                v-model="editPermsDialogFormData.name"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
          <el-col :sm="24" :md="12">
            <el-form-item label="菜单标识" prop="flag">
              <el-input
                v-model="editPermsDialogFormData.flag"
                auto-complete="off"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editPermsDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submitEditPerms">确 定</el-button>
      </div>
    </el-dialog>
  </el-row>
</template>

<script>
import permsApi from "@/api/perms";
import accountApi from "@/api/account";
import crud from "@/api/crud";
import { updateItem, deleteItem } from "@/utils/index";
const permsCrud = crud.factory("perms");

export default {
  data() {
    return {
      currentApp: 1, // 当前选中的App
      treeData: [],
      permsList: [],
      appList: [],
      current: {},
      setRoles: [],
      addMenuDialogVisible: false,
      addMenuDialogFormData: {
        show_in_sidebar: 1
      },
      addMenuDialogRules: {
        name: [
          { required: true, message: "请输入菜单名称", trigger: "blur" }
        ],
        icon: [
          { required: true, message: "请输入菜单图标", trigger: "blur" }
        ],
        flag: [
          { required: true, message: "请输入菜单路径", trigger: "blur" }
        ]
      },
      editMenuDialogVisible: false,
      editMenuDialogFormData: {},
      editMenuDialogRules: {
        name: [
          { required: true, message: "菜单名称不能为空", trigger: "blur" }
        ],
        icon: [
          { required: true, message: "菜单图标不能为空", trigger: "blur" }
        ],
        flag: [
          { required: true, message: "菜单路径不能为空", trigger: "blur" }
        ]
      },
      addPermsDialogVisible: false,
      addPermsDialogFormData: {},
      addPermsDialogRules: {
        name: [
          { required: true, message: "请输入权限名称", trigger: "blur" }
        ],
        flag: [
          { required: true, message: "请输入权限标识", trigger: "blur" }
        ]
      },
      editPermsDialogVisible: false,
      editPermsDialogFormData: {},
      editPermsDialogRules: {
        name: [
          { required: true, message: "权限名称不能为空", trigger: "blur" }
        ],
        flag: [
          { required: true, message: "权限标识不能为空", trigger: "blur" }
        ]
      }
    };
  },
  computed: {
    addMenuDialogTitle() {
      return `给【${this.current.name}】添加子菜单`;
    },
    editMenuDialogTitle() {
      return `编辑【${this.current.name}】`;
    },
    addPermsDialogTitle() {
      return `给【${this.current.name}】添加权限`;
    },
    editPermsDialogTitle() {
      return `编辑【${this.current.name}】`;
    }
  },
  watch: {
    currentApp: {
      handler(val) {
        this.getPermsByAppId(val);
      }
    }
  },
  created() {
    accountApi.getApp().then(res => {
      this.appList = res.data;
    });
    this.getPermsByAppId(this.currentApp);
  },
  methods: {
    // 获取应用的所有权限
    getPermsByAppId(appId) {
      permsApi.getAllPerms({ app_id: appId }).then(res => {
        this.treeData = res.data;
      });
    },
    isLeaf(row) { // 判断菜单是否可以删除,只有叶子节点可以删除
      return typeof (row.children) === 'undefined' || row.children.length <= 0;
    },
    handleAddMenu(data) { // 添加菜单
      this.current = data;
      this.addMenuDialogVisible = true;
    },
    submitAddMenu() { // 添加菜单提交
      this.$refs["addMenuDialogForm"].validate(valid => {
        if (valid) {
          // 设置父级ID
          this.addMenuDialogFormData.pid = this.current.id;
          this.addMenuDialogFormData.type = 1; // 设置权限类型为菜单
          this.addMenuDialogFormData.app_id = this.currentApp;
          permsCrud.add(this.addMenuDialogFormData).then(res => {
            this.$refs.permsTree.append(res.data, this.current.id);
            this.$message({
              type: "success",
              message: "菜单添加成功"
            });
            this.addMenuDialogVisible = false;
          });
        }
      });
    },
    handleEditMenu(data) { // 编辑菜单
      this.current = data;
      this.editMenuDialogVisible = true;
      this.editMenuDialogFormData = data;
    },
    submitEditMenu() {
      this.$refs["editMenuDialogForm"].validate(valid => {
        if (valid) {
          permsCrud.update(this.editMenuDialogFormData).then(() => {
            this.$message({
              type: "success",
              message: "菜单编辑成功"
            });
            this.editMenuDialogVisible = false;
          });
        }
      });
    },
    handleDelMenu(row) { // 删除菜单
      this.$confirm(`此操作将永久删除菜单【${row.name}】, 是否继续?`, '提示', {
        confirmButtonText: '删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        permsCrud.del({ id: row.id }).then(() => {
          this.$refs.permsTree.remove(row.id);
          this.$message({
            type: 'success',
            message: '删除成功!'
          });
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        });
      });
    },
    getMenuPerms(data) { // 获取该菜单节点的功能权限列表
      permsCrud.getList({
        pid: data.id,
        type: 2
      }, 1, 100).then(res => {
        this.permsList = res.data.data;
      });
    },
    handleAddPerms(data) { // 添加功能权限
      this.current = data;
      this.addPermsDialogVisible = true;
    },
    submitAddPerms() { // 添加菜单提交
      this.$refs["addPermsDialogForm"].validate(valid => {
        if (valid) {
          // 设置父级ID
          this.addPermsDialogFormData.pid = this.current.id;
          this.addPermsDialogFormData.type = 2; // 设置权限类型为功能
          this.addPermsDialogFormData.app_id = this.currentApp;
          permsCrud.add(this.addPermsDialogFormData).then(res => {
            this.permsList.unshift(res.data);
            this.$message({
              type: "success",
              message: "功能权限添加成功"
            });
            this.addPermsDialogVisible = false;
          });
        }
      });
    },
    handleEditPerms(data) { // 编辑菜单
      this.current = data;
      this.editPermsDialogVisible = true;
      this.editPermsDialogFormData = data;
    },
    submitEditPerms() {
      this.$refs["editPermsDialogForm"].validate(valid => {
        if (valid) {
          permsCrud.update(this.editPermsDialogFormData).then(() => {
            updateItem(this.permsList, this.editPermsDialogFormData);
            this.$message({
              type: "success",
              message: "权限编辑成功"
            });
            this.editPermsDialogVisible = false;
          });
        }
      });
    },
    handleDelPerms(row) { // 删除菜单
      this.$confirm(`此操作将永久删除权限【${row.name}】, 是否继续?`, '提示', {
        confirmButtonText: '删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        permsCrud.del({ id: row.id }).then(() => {
          deleteItem(this.permsList, row.id);
          this.$message({
            type: 'success',
            message: '删除成功!'
          });
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        });
      });
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
