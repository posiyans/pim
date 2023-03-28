<template>
  <q-form
    @submit="saveData"
    ref="userForm"
    greedy
    class="q-gutter-md q-pa-md"
  >
    <div class="row">
      <div class="col-grow q-gutter-md">
        <div>
          <q-input
            v-model="user.name"
            label="Имя"
            outlined
            :rules="[ val => val && val.length > 0 || 'Поле не может быть пустым']"
          />
        </div>
        <div>
          <q-input
            v-model="user.login"
            label="Login"
            outlined
            :rules="[ val => val && val.length > 0 || 'Поле не может быть пустым']"
          />
        </div>
        <div>
          <q-input
            v-model="user.full_name"
            label="Полное имя"
            outlined
            :rules="[ val => val && val.length > 0 || 'Поле не может быть пустым']"
          />
        </div>
      </div>
      <div class="q-pa-md" style="min-width: 150px;">
        <ChangeAvatar v-if="user.id" :id="user.id" size="120px" />
      </div>
    </div>
    <div>
      <q-input v-model="user.options.telegram" label="Telegram Id" outlined>
        <template v-slot:append>
          <GetLastUserIdBtn @setId="setTelegramId" />
        </template>
      </q-input>
    </div>
    <div>
      <q-input v-model="user.email" label="E-mail" outlined />
    </div>
    <div>
      <QSelectExecutor v-model="user.aliases" outlined multiple label="Доступ к " />
    </div>
    <div class="row items-center q-col-gutter-md q-pa-md">
      <div>
        <q-checkbox
          v-model="user.hide"
          label="Скрыть в списке исполнителей"
          color="negative"
        />
      </div>
      <div>
        <q-checkbox
          v-model="user.moderator"
          label="Модератор"
          color="teal"
        />
      </div>
      <div v-if="isAdmin">
        <q-checkbox
          v-model="user.admin"
          label="Админ"
          color="teal"
        />
      </div>
    </div>
    <div class="text-right q-gutter-x-md">
      <q-btn color="negative" flat label="Отмена" @click="editCancel" />
      <q-btn color="primary" :label="user.id ? 'Сохранить' : 'Добавить'" type="submit" />
    </div>
  </q-form>
</template>

<script>
import { getUserInfo, updateUser } from 'src/Modules/User/api/user'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ChangeAvatar from 'src/Modules/User/components/ChangeAvatar/index.vue'
import GetLastUserIdBtn from 'src/Modules/Telegram/components/GetLastUserIdBtn/index.vue'

export default {
  components: {
    QSelectExecutor,
    ChangeAvatar,
    GetLastUserIdBtn
  },
  props: {
    userId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      user: {
        login: '',
        full_name: '',
        name: '',
        phone: '',
        aliases: [],
        options: {
          telegram: '',

        },
        hide: true,
        moderator: false,
        admin: false
      }
    }
  },
  computed: {
    myUser() {
      return this.$store.state.user.info
    },
    isAdmin() {
      return this.myUser.roles.includes('admin')
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    setTelegramId(val) {
      this.user.options.telegram = val
    },
    editCancel() {
      this.$emit('cancel')
    },
    saveData() {
      this.$refs.userForm.validate()
        .then(() => {
          updateUser(this.user)
            .then(() => {
              this.$emit('cancel')
            })
        })
    },
    getData() {
      if (this.userId) {
        const data = {
          id: this.userId
        }
        getUserInfo(data)
          .then(res => {
            this.user = res.data
            this.user.aliases = this.user.aliases.map(item => +item)
            this.user.moderator = this.user.roles.includes('moderator')
            this.user.admin = this.user.roles.includes('admin')
          })
      }
    }

  }
}
</script>

<style scoped>

</style>
