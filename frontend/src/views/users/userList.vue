<template>
  <div class="app-container">
    <div class="filter-container">
      Пользователи
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="addUser">{{ $t('table.add') }}</el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :key="tableKey"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;">
      <el-table-column :label="$t('table.id')" prop="id" sortable="custom" align="center" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="" width="60px" align="center">
        <template slot-scope="scope">
          <img :src="scope.row.id | avatarFilter" class="user-avatar">
        </template>
      </el-table-column>
      <el-table-column label="Login" width="100px">
        <template slot-scope="scope">
          <span>{{ scope.row.login }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Имя" width="120px" align="center" >
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Полное имя">
        <template slot-scope="scope">
          <span>{{ scope.row.full_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Последний вход" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.last_connect }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Phone" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.phone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Telegram Id" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.telegram }}</span>
        </template>
      </el-table-column>
      <el-table-column label="" align="center" class-name="small-padding">
        <template slot-scope="scope">
          <el-button type="primary" size="mini" @click="handleUpdate(scope.row)">{{ $t('table.edit') }}</el-button>
          <el-button type="danger" size="mini" title="Сбросить пароль" @click="resetPassword(scope.row)">Reset</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="temp.full_name" :visible.sync="dialogFormVisible">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="120px" style="width: 400px; margin-left:50px;">
        <el-form-item label="Id"> {{ temp.id }}
        </el-form-item>
        <el-form-item label="Имя" prop="name">
          <el-input v-model="temp.name"/>
        </el-form-item>
        <el-form-item label="Полное имя" prop="full_name">
          <el-input v-model="temp.full_name"/>
        </el-form-item>
        <el-form-item label="Login" prop="login">
          <el-input v-model="temp.login"/>
        </el-form-item>
        <el-form-item label="e-mail">
          <el-input v-model="temp.email"/>
        </el-form-item>
        <el-form-item label="Телефон">
          <el-input v-model="temp.phone"/>
        </el-form-item>
        <el-form-item label="SMS">
          <el-checkbox v-model="temp.login_by_sms" true-value="1" false-value="0" class="filter-item" style="margin-left:15px;">Вход по SMS</el-checkbox>
        </el-form-item>
        <el-form-item label="Telegram Id">
          <el-input v-model="temp.telegram"/>
        </el-form-item>
        <el-form-item label="Роли">
          <el-drag-select v-model="temp.roles_json" style="width:500px;" multiple placeholder="Пожалуйста, выберите">
            <el-option v-for="item in roles" :label="item" :value="item" :key="item" />
          </el-drag-select>
        </el-form-item>
        <el-form-item label="Доступ к ">
          <el-drag-select v-model="temp.aliases" style="width:500px;" multiple placeholder="Пожалуйста, выберите">
            <el-option v-for="item in allExecutor" :label="item.label" :value="item.value" :key="item.value" />
          </el-drag-select>
        </el-form-item>
        <el-form-item label="Цвет">
          <el-color-picker
            v-model="temp.color"
            class="user-theme-picker"
            popper-class="theme-picker-dropdown"/>
        </el-form-item>
        <el-form-item v-if ="dialogStatus == 'update'" label="Аватар" >
          <pan-thumb :image="getUrl(temp.id)"/>
          <el-button type="primary" icon="upload" style="position: absolute;bottom: 15px;margin-left: 40px;" @click="imagecropperShow=true">Сменить аватар
          </el-button>
          <image-cropper
            v-show="imagecropperShow"
            :width="300"
            :height="300"
            :key="imagecropperKey"
            :url="getUrl(temp.id)"
            lang-type="en"
            @close="close"
            @crop-upload-success="cropSuccess"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createUser():updateUser()">Сохранить</el-button>
      </div>
    </el-dialog>
    <el-dialog :title="reset.title" :visible.sync="dialogResetPassword">
      <el-form ref="dataForm" label-position="left" label-width="140px" style="width: 400px; margin-left:50px;">
        <el-form-item label="Новый Пароль:">
          <el-input v-model="reset.password"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogResetPassword = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="danger" @click="updatePassword()">Сменить!!!</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, updateUser, fetchExecutors, updatePassword, createUser } from '@/api/user'
import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
import { mapGetters } from 'vuex'
import ElDragSelect from '@/components/DragSelect'
import ThemePicker from '@/components/ThemePicker'
import ImageCropper from '@/components/ImageCropper'
import PanThumb from '@/components/PanThumb'

export default {
  components: { Pagination, ElDragSelect, ThemePicker, ImageCropper, PanThumb },
  filters: {
    avatarFilter(url) {
      if (url) {
        return process.env.BASE_API + '/../user/avatar/' + url
      }
    }
  },
  data() {
    return {
      imagecropperShow: false,
      imagecropperKey: 0,
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        arxiv: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        archiv: false,
        executor: undefined
      },
      yearAll: [],
      allExecutor: [],
      test1: false,
      showReviewer: false,
      dialogFormVisible: false,
      dialogResetPassword: false,
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
    ...mapGetters([
      'roles',
      'userId'
    ])
  },
  created() {
    this.getList()
    this.getExecutors()
  },
  methods: {
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
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(response => {
        this.list = response.data.items.data
        this.total = response.data.total
        this.yearAll = response.data.years
        this.listLoading = false
      })
    },
    addUser() {
      this.temp = {
        id: 'new',
        name: '',
        full_name: '',
        login: '',
        email: '',
        color: '#ff0000',
        telegram: '',
        phone: '',
        login_by_sms: true,
        roles: ['user'],
        aliases: []
      }
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row) // copy obj
      if (this.temp.login_by_sms === 1) {
        this.temp.login_by_sms = true
      } else {
        this.temp.login_by_sms = false
      }
      const i = []
      this.temp.aliases.forEach(function(item) {
        i.push(parseInt(item, 10))
      })
      this.temp.aliases = i
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
    },
    resetPassword(row) {
      this.reset.id = row.id
      this.reset.title = 'Сбросить пароль для ' + row.full_name
      this.reset.password = this.randomString(8)
      this.dialogResetPassword = true
    },
    updatePassword() {
      updatePassword(this.reset).then(response => {
        console.log(response.data)
        this.getList()
      })
      this.dialogResetPassword = false
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
  .danger{
    background-color: rgba(245,108,108,.1);
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
