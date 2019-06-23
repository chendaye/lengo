<template>
  <el-container>
    <el-header>
      <el-button type="success" @click="dialogVisible = true">创建管理员</el-button>
      <pic></pic>
      <!-- 弹窗 -->
      <el-dialog :title="dialogTitle" :visible.sync="dialogVisible">
        <el-form
          ref="ruleForm"
          :model="ruleForm"
          :rules="rules"
          label-width="100px"
        >
          <el-form-item label="昵称" prop="name">
            <el-input v-model="ruleForm.name" />
          </el-form-item>
          <el-form-item label="邮箱" prop="email">
            <el-input v-model="ruleForm.email" />
          </el-form-item>
          <el-form-item label="密码" prop="password">
            <el-input v-model="ruleForm.password" />
          </el-form-item>
          <el-form-item label="个性签名" prop="remark">
            <el-input v-model="ruleForm.remark" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" plain @click="submitForm('ruleForm')">立即创建</el-button>
            <el-button type="info" plain @click="resetForm('ruleForm')">重置</el-button>
            <el-button type="warning" plain @click="dialogVisible = false">取 消</el-button>
          </el-form-item>
        </el-form>
      </el-dialog>
    </el-header>
    <el-main />
  </el-container>
</template>

<script>
import pic from '@/components/Upload/image';
export default {
  name: 'User',
  components: {
    pic
  },
  data() {
    return {
      dialogVisible: false,
      dialogTitle: '创建管理员',
      ruleForm: {
        name: '',
        region: '',
        date1: '',
        date2: '',
        delivery: false,
        type: [],
        resource: '',
        desc: ''
      },
      rules: {
        name: [
          { required: true, message: '请输入活动名称', trigger: 'blur' },
          { min: 6, max: 12, message: '长度在 6 到 12 个字符', trigger: 'blur' }
        ],
        email: [
          { required: true, type: 'email', message: '请填写正确的邮箱', trigger: 'change' }
        ],
        password: [
          { required: true, message: '请填写密码', trigger: 'change' },
          { min: 6, max: 12, message: '长度在 6 到 12 个字符', trigger: 'blur' }
        ]
      }
    };
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          alert('submit!');
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
.el-header,
.el-footer {
  line-height: 60px;
}

.el-aside {
  line-height: 200px;
}

.el-main {
  line-height: 160px;
}

body > .el-container {
  margin-bottom: 40px;
}

.el-container:nth-child(5) .el-aside,
.el-container:nth-child(6) .el-aside {
  line-height: 260px;
}

.el-container:nth-child(7) .el-aside {
  line-height: 320px;
}
</style>
