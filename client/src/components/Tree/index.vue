<template>
  <div>
    <el-row v-if="isRoot" :gutter="5">
      <el-col :span="12">
        <div class="grid-content bg-purple">
          <el-button plain type="success" icon="el-icon-edit" @click="addRoot">新建根分类</el-button>
        </div>
      </el-col>
      <el-col :span="12">
        <div class="grid-content bg-purple">
          <el-input v-model="filterText"  placeholder="输入关键字" />
        </div>
      </el-col>
    </el-row>

    <el-row v-if="isFilter" :gutter="5">
      <el-col :span="24">
        <div class="grid-content bg-purple">
          <el-input v-model="filterText" size="mini" placeholder="输入关键字" />
        </div>
      </el-col>
    </el-row>

    <el-tree
      v-if="treeData.length > 0"
      ref="tree"
      class="filter-tree background"
      :props="mapProps"
      :filter-node-method="filterNode"
      :data="treeData"
      node-key="id"
      :draggable="isDrag"
      :show-checkbox="isCheck"
      :allow-drop="allowDrop"
      :allow-drag="allowDrag"
      @node-drop="handleDrop"
      @check="handChecked"
    >
      <span slot-scope="{ node, data }" class="custom-tree-node">
        <span><b class="node-font">{{ node.label }}</b></span>
        <span v-if="isCreate">
          <el-tooltip class="item" effect="dark" content="添加" placement="top">
            <i class="el-icon-circle-plus" @click.stop="() => addSon(data)" />
          </el-tooltip>
          <el-tooltip class="item" effect="dark" content="编辑" placement="top">
            <i class="el-icon-edit" @click.stop="() => editNode(node, data)" />
          </el-tooltip>

          <el-tooltip class="item" effect="dark" content="删除" placement="top">
            <i class="el-icon-delete" @click.stop="() => delNode(data)" />
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
        <el-button
          type="primary"
          plain
          @click="submitcategoryForm('categoryForm')"
        >{{ dialogTitle[buttonStatus] }}</el-button>
        <el-button type="info" plain @click="resetForm('categoryForm')">重置</el-button>
        <el-button type="warning" plain @click="rootVisible = false">取 消</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import crud from "@/api/crud";
const wtuCrud = crud.factory("wtu");
import { mapGetters } from "vuex";

export default {
  name: 'Tree',
  props: {
    isCreate: {
      type: Boolean,
      default: false
    },
    isCheck: {
      type: Boolean,
      default: false
    },
    isDrag: {
      type: Boolean,
      default: false
    },
    isFilter: {
      type: Boolean,
      default: false
    },
    isRoot: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      filterText: "",
      mapProps: {
        children: "children",
        label: "desc" // desc 映射为 label
      },
      treeData: [],
      rootVisible: false,
      // 添加根分类
      categoryForm: {
        desc: "",
        pid: null,
        level: null,
        user_id: null
      },
      // 整棵树的数据
      nodeData: {},
      // 弹框状态
      dialogStatus: "root",
      buttonStatus: "root",
      // 弹框标题
      dialogTitle: {
        root: "添加根分类",
        son: "添加子分类",
        edit: "编辑分类"
      },
      buttonTitle: {
        root: "新建根分类",
        son: "新建子分类",
        exit: "编辑分类"
      },
      // 验证规则
      rules: {
        desc: [
          { required: true, message: "请输入分类名称", trigger: "blur" },
          { min: 1, max: 50, message: "长度在 3 到 50 个字符", trigger: "blur" }
        ]
      }
    };
  },
  computed: {
    // 当前登录的管理员id
    ...mapGetters(["admin_id"])
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    }
  },

  created() {
    wtuCrud.get("tree", {}).then(res => {
      if (res.status === 200) {
        this.treeData = res.data.data;
      } else {
        this.$message({
          message: "获取分类失败！",
          type: "error"
        });
      }
    });
    // 当前登录管理员
    this.categoryForm.user_id = this.admin_id;
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.desc.indexOf(value) !== -1;
    },
    // 添加根分类
    addRoot() {
      this.categoryForm.desc = "";
      this.categoryForm.pid = 0;
      this.categoryForm.level = 1;
      this.rootVisible = true;
      this.dialogStatus = "root";
      this.buttonStatus = "root";
    },
    // 添加子分类
    addSon(data) {
      this.categoryForm.desc = "";
      this.categoryForm.pid = data.id;
      this.categoryForm.level = data.level + 1;
      this.rootVisible = true;
      this.dialogStatus = "son";
      this.buttonStatus = "son";
    },
    // 编辑分类
    editNode(node, data) {
      this.nodeData = node; // 保存整棵树的属性
      this.categoryForm = data; // 保存整棵树的数据
      this.rootVisible = true;
      this.dialogStatus = "edit";
      this.buttonStatus = "edit";
    },
    // 删除分类
    delNode(data) {
      this.$confirm(
        `此操作将删除分类及其子分类【${data.desc}】, 是否继续?`,
        "提示",
        {
          confirmButtonText: "删除",
          cancelButtonText: "取消",
          type: "warning"
        }
      )
        .then(() => {
          wtuCrud
            .post("delCategory", {
              where: { id: { op: "=", va: data.id, ex: "cp" }}
            })
            .then(() => {
              this.$refs.tree.remove(data.id);
              this.$message({
                type: "success",
                message: "删除成功!"
              });
            });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },

    // 创建根分类
    submitcategoryForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.dialogStatus === "root") {
            this.submitCategory();
          } else if (this.dialogStatus === "son") {
            this.submitSon();
          } else if (this.dialogStatus === "edit") {
            this.submitEdit();
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    // 添加根分类
    submitCategory() {
      wtuCrud.post("addCategory", this.categoryForm).then(res => {
        if (res.status === 200) {
          const tmp = res.data.data;
          this.treeData.push(tmp);
          this.$notify({
            title: "成功",
            message: "根分类创建成功",
            type: "success"
          });
          this.rootVisible = false;
        } else {
          this.$notify.error({
            title: "错误",
            message: "根节点添加失败！"
          });
        }
      });
    },
    // 添加子分类
    submitSon() {
      wtuCrud.post("addCategory", this.categoryForm).then(res => {
        if (res.status === 200) {
          const tmp = res.data.data;
          // 添加节点
          this.$refs.tree.append(tmp, this.categoryForm.pid);

          this.$notify({
            title: "成功",
            message: "子分类创建成功",
            type: "success"
          });
          this.rootVisible = false;
        } else {
          this.$notify.error({
            title: "错误",
            message: "子节点添加失败！"
          });
        }
      });
    },

    // 编辑分类
    submitEdit() {
      wtuCrud
        .post("updateCategory", {
          data: { desc: this.categoryForm.desc },
          where: { id: { op: "=", va: this.categoryForm.id, ex: "cp" }}
        })
        .then(res => {
          if (res.status === 200) {
            console.log(this.categoryForm);
            // 用nodeData 保存整个树的属性  this.categoryForm 保存了树数据
            this.nodeData.data = this.categoryForm;
            this.rootVisible = false;
            this.$notify({
              title: "Success",
              message: "分类更新成功！",
              type: "success",
              duration: 2000
            });
          } else {
            this.$notify.error({
              title: "错误",
              message: "分类更新失败！"
            });
          }
        });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },

    // 拖拽
    // 拖拽成功完成时触发的事件
    handleDrop(draggingNode, dropNode, dropType, ev) {
      const id = draggingNode.data.id;
      const pid =
        dropType === "inner"
          ? dropNode.data.id
          : dropNode.parent.data.length > 1
            ? 0
            : dropNode.parent.data.id;

      // console.log('dropType', dropType);
      // console.log('draggingNode', draggingNode);
      // console.log('dropNode', dropNode);
      // console.log(id, pid);
      if (id !== undefined && pid !== undefined) {
        wtuCrud
          .post("updateCategory", {
            data: { pid: pid },
            where: { id: { op: "=", va: id, ex: "cp" }}
          })
          .then(res => {
            if (res.status === 200) {
              this.$notify({
                title: "Success",
                message: "分类更新成功！",
                type: "success",
                duration: 2000
              });
            } else {
              this.$notify.error({
                title: "错误",
                message: "分类更新失败！"
              });
            }
            console.log(res);
          });
      } else {
        this.$notify.error({
          title: "错误",
          message: "请重新放置！"
        });
      }
    },
    // 拖拽时判定目标节点能否被放置
    allowDrop(draggingNode, dropNode, type) {
      return true;
      // if (dropNode.data.desc === '二级 3-1') {
      //   return type !== 'inner';
      // } else {
      //   return true;
      // }
    },
    // 判断节点能否被拖拽
    allowDrag(draggingNode) {
      return true;
      // return draggingNode.data.desc.indexOf('三级 3-2-2') === -1;
    },
    /**
     * data 当前选中的节点
     * check 包含 checkedNodes、checkedKeys 勾选状态的 key 和 node
     * halfCheckedNodes、halfCheckedKeys 半勾选状态的 key 和 node
     */
    handChecked(data, check) {
      this.$emit('handchecked', { current: data, check: check });
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
  /* background-color:navy */
}
.node-font{
  font-style: italic;
  font-size: 14px;
}
.background {
  /* background-color:rgb(92, 168, 73) */
}
</style>
