<template>
  <div class="app-container">
    <div class="q-mb-sm">
      Пользователи
      <q-btn flat icon="add" color="primary" @click="addUser" />
    </div>
    <q-table
      :rows="list"
      :columns="columns"
      separator="cell"
      bordered
      hide-bottom
      :pagination="{rowsPerPage: 0}"
      flat
      row-key="name"
    >
      <template v-slot:body-cell-id="scope">
        <q-td :props="scope" auto-width>
          {{ ++scope.pageIndex }}
        </q-td>
      </template>
      <template v-slot:body-cell-login="scope">
        <q-td :props="scope" auto-width class="relative-position">
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
            <div v-if="scope.row.roles.includes('admin')">
              <q-icon name="people_alt" color="secondary" />
            </div>
            <div v-else-if="scope.row.roles.includes('moderator')">
              <q-icon name="perm_identity" color="secondary" />
            </div>
            <div v-if="scope.row.two_factor">
              <q-icon name="filter_2" color="secondary" />
            </div>
          </div>
        </q-td>
      </template>
      <template v-slot:body-cell-name="scope">
        <q-td :props="scope" auto-width>
          <span>{{ scope.row.name }}</span>
        </q-td>
      </template>
      <template v-slot:body-cell-fullname="scope">
        <q-td :props="scope">
          <div class="ellipsis">{{ scope.row.full_name }}</div>

        </q-td>
      </template>
      <template v-slot:body-cell-date="scope">
        <q-td :props="scope">
          <span>{{ scope.row.last_connect }}</span>
        </q-td>
      </template>
      <template v-slot:body-cell-action="scope">
        <q-td :props="scope">
          <div class="row items-center q-col-gutter-sm justify-center">
            <div>
              <q-btn color="primary" outline label="Edit" @click="EditUser(scope.row)" />
            </div>
            <ChangeUserPasswordBtn :user-id="scope.row.id" :user-name="scope.row.full_name" />
          </div>
        </q-td>
      </template>
    </q-table>

    <LoadMore :key="key" v-model:list-query="listQuery" :auto-scroll="false" :func="func" @setList="setList" />

    <q-dialog v-model="dialogFormVisible" maximized @hide="reload">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ selectUser.full_name }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <EditUserProfile
            :user-id="selectUser.id"
            @cancel="dialogFormVisible = false"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { getUsersList } from 'src/Modules/User/api/user.js'
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
      tableKey: 0,
      list: [],
      columns: [
        {
          name: 'id',
          label: '№',
          align: 'center'
        },
        {
          name: 'login',
          label: 'Login',
          align: 'left'
        },
        {
          name: 'name',
          label: 'Имя',
          align: 'center'
        },
        {
          name: 'fullname',
          label: 'Полное имя',
          align: 'center'
        },
        {
          name: 'date',
          label: 'Последний вход',
          align: 'center'
        },
        {
          name: 'action',
          label: '',
          align: 'center'
        },
      ],
      listLoading: false,
      listQuery: {
        page: 1,
        limit: 20
      },
      selectUser: {},
      dialogFormVisible: false
    }
  },
  methods: {
    setList(val) {
      this.list = val
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
    EditUser(row) {
      this.selectUser = row
      this.dialogFormVisible = true
    }
  }
}
</script>
<style scoped>

</style>
