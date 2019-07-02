<template>
  <div>
    <el-row :gutter="5">
      <el-col :span="12">
        <div class="grid-content bg-purple">
          <el-button plain type="success" icon="el-icon-edit" @click="addRoot">新建根分类</el-button>
        </div>
      </el-col>
      <el-col :span="12">
        <div class="grid-content bg-purple">
          <el-input v-model="filterText" placeholder="输入关键字" />
        </div>
      </el-col>
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
          <el-tooltip class="item" effect="dark" content="添加" placement="top">
            <i class="el-icon-circle-plus" @click.stop="() => append(data)" />
          </el-tooltip>
          <el-tooltip class="item" effect="dark" content="编辑" placement="top">
            <i class="el-icon-edit" @click.stop="() => edit(node, data)" />
          </el-tooltip>

          <el-tooltip class="item" effect="dark" content="删除" placement="top">
            <i class="el-icon-delete" @click.stop="() => remove(node, data)" />
          </el-tooltip>
        </span>
      </span>
    </el-tree>

    <!-- 添加根分类 -->
    <el-dialog :title="dialogTitle[dialogStatus]" :visible.sync="rootVisible" width="50%">
      <span>
        <el-form
          ref="categoryForm"
          :model="categoryForm"
          :rules="rules"
          label-width="100px"
          class="demo-categoryForm"
        >
          <el-form-item label="分类名" prop="desc">
            <el-input v-model="categoryForm.desc" />
          </el-form-item>
        </el-form>
      </span>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" plain @click="submitcategoryForm('categoryForm')">立即创建</el-button>
        <el-button type="info" plain @click="resetForm('categoryForm')">重置</el-button>
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
      categoryForm: {
        desc: '',
        pid: null,
        level: null,
        user_id: null
      },
      // 弹框状态
      dialogStatus: 'root',
      // 弹框标题
      dialogTitle: {
        root: '添加根分类',
        son: '添加次级分类',
        edit: '编辑分类'
      },
      // 验证规则
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
    ...mapGetters(['admin_id'])
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
    this.categoryForm.user_id = this.admin_id;
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.label.indexOf(value) !== -1;
    },
    // 添加根分类
    addRoot() {
      this.categoryForm.desc = '';
      this.categoryForm.pid = 0;
      this.categoryForm.level = 1;
      this.rootVisible = true;
      this.dialogStatus = 'root';
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

    // 编辑分类
    edit(node, data) {
      console.log('node', node);
      console.log('data', data);
    },

    // 创建根分类
    submitcategoryForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          console.log(this.categoryForm);
          wtuCrud.post('addCategory', this.categoryForm).then(res => {
            if (res.status === 200) {
              const tmp = res.data.data;
              tmp.label = tmp.desc;
              this.data.push(tmp);
              this.$notify({
                title: '成功',
                message: '根分类创建成功',
                type: 'success'
              });
              this.rootVisible = false;
            } else {
              this.$notify.error({
                title: '错误',
                message: '根节点添加失败！'
              });
            }
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
