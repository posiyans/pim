<template>
  <div class="app-container">
    <div class="q-mb-sm">
      Пользователи
      <q-btn flat icon="add" color="primary" @click="addUser" />
    </div>

    <el-table
      v-loading="listLoading"
      :key="tableKey"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;">
      <el-table-column type="index" width="50" label="№" align="center" />
      <el-table-column label="Login">
        <template #default="scope">
          <div class="text-no-wrap row items-center no-wrap q-col-gutter-md">
            <div class="">
              <AvatarById :id="scope.row.id" size="40px" />
            </div>
            <div>
              {{ scope.row.login }}
            </div>
            <q-space />

          </div>
          <div class="row items-center q-pr-sm no-wrap q-col-gutter-sm absolute-bottom-right">
            <div v-if="scope.row.hide">
              <q-icon name="visibility_off" color="negative" />
            </div>
            <div v-if="scope.row.moderator">
              <q-icon name="perm_identity" color="secondary" />
            </div>
            <div v-if="scope.row.two_factor">
              <q-icon name="filter_2" color="secondary" />
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Имя" width="120px" align="center">
        <template #default="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Полное имя">
        <template #default="scope">
          <div class="ellipsis">{{ scope.row.full_name }}</div>
        </template>
      </el-table-column>
      <el-table-column label="Последний вход" align="center">
        <template #default="scope">
          <span>{{ scope.row.last_connect }}</span>
        </template>
      </el-table-column>
      <el-table-column label="" align="center" class-name="small-padding">
        <template #default="scope">
          <div class="row items-center q-col-gutter-sm">
            <div>
              <q-btn color="primary" outline label="Edit" @click="handleUpdate(scope.row)" />
            </div>
            <ChangeUserPasswordBtn :user-id="scope.row.id" :user-name="scope.row.full_name" />

          </div>
        </template>
      </el-table-column>
    </el-table>
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />

    <q-dialog v-model="dialogFormVisible" maximized>
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ selectUser.full_name }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <EditUserProfile
            :user-id="selectUser.id"
            @success="reload"
            @cancel="dialogFormVisible = false"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { createUser, fetchExecutors, getUsersList, updateUser } from 'src/Modules/User/api/user.js'
import LoadMore from 'src/components/LoadMore/index.vue'
import AvatarById from 'src/Modules/User/components/AvatarById/index.vue'
import ChangeUserPasswordBtn from 'src/Modules/User/components/ChangeUserPasswordBtn/index.vue'
import EditUserProfile from 'src/Modules/User/components/EditUserProfile/index.vue'

export default {
  components: {
    ChangeUserPasswordBtn,
    LoadMore,
    AvatarById,
    EditUserProfile
  },
  data() {
    return {
      key: 1,
      func: getUsersList,
      imagecropperShow: false,
      imagecropperKey: 0,
      tableKey: 0,
      list: [],
      total: 0,
      listLoading: false,
      listQuery: {
        page: 1,
        limit: 20
      },
      selectUser: {},
      yearAll: [],
      allExecutor: [],
      test1: false,
      showReviewer: false,
      dialogFormVisible: false,
      dialogStatus: '',
      dialogPvVisible: false,
      downloadLoading: false,
      expandAll: false,
      data: {},
      temp: {},
      reset: {},
      rules: {
        name: [{ required: true, message: 'Обязательное поле', trigger: 'change' }],
        full_name: [{ required: true, message: 'Обязательное поле', trigger: 'change' }],
        login: [{ required: true, min: 6, message: 'Обязательное поле, мин. 6 символов', trigger: 'change' }]
      }
    }
  },
  computed: {
    user() {
      return this.$store.state.user.info
    },
    roles() {
      return this.user.roles
    }
  },
  mounted() {
    // this.getList()
    // this.getExecutors()
  },
  methods: {
    setList(val) {
      this.list = val
    },
    getUrl(id) {
      return process.env.BASE_API + '/user/avatar/' + id
    },
    cropSuccess(resData) {
      this.imagecropperShow = false
      this.imagecropperKey = this.imagecropperKey + 1
      this.temp.image = resData.files.avatar
      this.list = {}
      this.nextTick(this.getList())
    },
    close() {
      this.imagecropperShow = false
    },
    reload() {
      this.key++
      this.dialogFormVisible = false
    },
    addUser() {
      this.selectUser = {
        full_name: 'Новый пользователь',
        id: ''
      }
      this.dialogFormVisible = true
    },
    handleUpdate(row) {
      this.selectUser = row
      // this.temp = Object.assign({}, row) // copy obj
      // if (this.temp.login_by_sms === 1) {
      //   this.temp.login_by_sms = true
      // } else {
      //   this.temp.login_by_sms = false
      // }
      // const i = []
      // this.temp.aliases.forEach(function (item) {
      //   i.push(parseInt(item, 10))
      // })
      // this.temp.aliases = i
      // this.dialogStatus = 'update'
      this.dialogFormVisible = true
    },
    updateUser() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          updateUser(this.temp).then(response => {
            this.getList()
          })
          this.dialogFormVisible = false
        }
      })
    },
    createUser() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          createUser(this.temp).then(response => {
            this.getList()
          })
          this.dialogFormVisible = false
        }
      })
    },
    getExecutors() {
      fetchExecutors().then(response => {
        var users = []
        for (const user of response.data.user) {
          var item = {}
          item.value = user.key
          item.label = user.display_name
          users.push(item)
        }
        this.allExecutor = Object.assign({}, this.allExecutor, users)
      })
    },
    randomString(len) {
      const charSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
      let randomString = ''
      for (var i = 0; i < len; i++) {
        var randomPoz = Math.floor(Math.random() * charSet.length)
        randomString += charSet.substring(randomPoz, randomPoz + 1)
      }
      return randomString
    }
  }
}
</script>
<style scoped>
.danger {
  background-color: rgba(245, 108, 108, .1);
}

.user-avatar {
  cursor: pointer;
  width: 40px;
  height: 40px;
  border-radius: 20px;
}

.user-edit-avatar {
  width: 200px;
  height: 200px;
  border-radius: 50%;
}

.pan-thumb {
  width: 200%;
  height: 100%;
  background-size: 100%;
  border-radius: 50%;
  overflow: hidden;
  position: absolute;
  transform-origin: 95% 40%;
  transition: all 0.3s ease-in-out;
}
</style>
