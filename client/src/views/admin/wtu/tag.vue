<template>
  <el-container>
    <el-header>
      <el-button type="success" @click="dialogVisible = true">创建管理员</el-button>
      <!-- 弹窗 -->
      <el-dialog :title="dialogTitle" :visible.sync="dialogVisible">
        <el-form ref="ruleForm" :model="ruleForm" :rules="rules" label-width="100px">
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
          <el-form-item label="上传头像" prop="avatar">
            <!-- <pic @getAvatar="getAvatar" /> -->
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
import { validUsername, validEmail } from '@/utils/validate';
// import pic from './components/avatar';
import crud from '@/api/crud';
const rbacCrud = crud.factory('rbac');
export default {
  name: 'User',
  components: {
    // pic
  },
  data() {
    // 自定义账号姓名验证
    const validateUsername = (rule, value, callback) => {
      if (!validUsername(value)) {
        callback(new Error('用户名必须由6-12个字母或数字构成！'));
      } else {
        callback();
      }
    };
    const validateEmail = (rule, value, callback) => {
      if (!validEmail(value)) {
        callback(new Error('请输入正确的邮箱！'));
      } else {
        callback();
      }
    };
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error('密码不少于6个字符！'));
      } else {
        callback();
      }
    };
    return {
      dialogVisible: false,
      dialogTitle: '创建管理员',
      ruleForm: {
        name: '',
        email: '',
        password: '',
        remark: '',
        avatar: ''
      },
      rules: {
        name: [
          { required: true, trigger: 'blur', validator: validateUsername }
        ],
        email: [{ required: true, trigger: 'blur', validator: validateEmail }],
        password: [
          { required: true, trigger: 'blur', validator: validatePassword }
        ]
      }
    };
  },
  methods: {
    // 上传头像
    getAvatar(msg) {
      this.ruleForm.avatar = msg.group_name + '/' + msg.filename;
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          console.log(this.ruleForm);
          rbacCrud.post('add', this.ruleForm).then(res => {
            if (res.status === 200) {
              this.dialogVisible = false;
              this.$message({
                message: '创建管理员成功！',
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
