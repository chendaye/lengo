<template>
  <div>
    <el-row :gutter="5">
      <el-col :span="12"><div class="grid-content bg-purple">
        <el-button plain type="success" icon="el-icon-edit" @click="rootVisible = true">新建根分类</el-button>
      </div></el-col>
      <el-col :span="12"><div class="grid-content bg-purple">
        <el-input
          v-model="filterText"
          placeholder="输入关键字"
        />
      </div></el-col>

    </el-row>

    <el-tree
      v-if="data.length > 0"
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

    <!-- 添加根分类 -->
    <el-dialog
      title="添加分类"
      :visible.sync="rootVisible"
      width="50%"
    >
      <span>
        <el-form ref="rootForm" :model="rootForm" :rules="rules" label-width="100px" class="demo-rootForm">
          <el-form-item label="分类名" prop="desc">
            <el-input v-model="rootForm.desc" />
          </el-form-item>
        </el-form>
      </span>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" plain @click="submitRootForm('rootForm')">立即创建</el-button>
        <el-button type="info" plain @click="resetForm('rootForm')">重置</el-button>
        <el-button type="warning" plain @click="rootVisible = false">取 消</el-button>
      </span>
    </el-dialog>

  </div>
</template>

<script>
import crud from '@/api/crud';
const wtuCrud = crud.factory('wtu');
import { mapGetters } from 'vuex';

let id = 1000;
export default {

  data() {
    return {
      filterText: '',
      defaultProps: {
        children: 'children',
        label: 'label'
      },
      data: [],
      rootVisible: false,
      // 添加根分类
      rootForm: {
        desc: '',
        pid: 0,
        level: 1,
        user_id: null
      },
      rules: {
        desc: [
          { required: true, message: '请输入分类名称', trigger: 'blur' },
          { min: 1, max: 50, message: '长度在 3 到 50 个字符', trigger: 'blur' }
        ]
      }
    };
  },
  computed: {
    // 当前登录的管理员id
    ...mapGetters([
      'admin_id'
    ])
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    }
  },

  created() {
    wtuCrud.get('tree', {}).then(res => {
      if (res.status === 200) {
        this.data = res.data.data;
      } else {
        this.$message({
          message: '获取分类失败！',
          type: 'error'
        });
      }
    });
    // 当前登录管理员
    this.rootForm.user_id = this.admin_id;
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.label.indexOf(value) !== -1;
    },
    // 添加根分类
    addRoot() {
      this.rootVisible = true;
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
    },
    // 创建根分类
    submitRootForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          console.log(this.rootForm);
          wtuCrud.post('addRoot', this.rootForm).then(res => {
            console.log(res);
          });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
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
