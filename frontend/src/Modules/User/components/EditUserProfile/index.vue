<template>
  <div class="q-gutter-md q-pa-md">
    <div class="row">
      <div class="col-grow q-gutter-md">
        <div>
          <q-input v-model="user.name" label="Имя" outlined />
        </div>
        <div>
          <q-input v-model="user.login" label="Login" outlined />
        </div>
        <div>
          <q-input v-model="user.full_name" label="Полное имя" outlined />
        </div>
      </div>
      <div class="q-pa-md">
        <ChangeAvatar v-if="user.id" :id="user.id" size="120px" />
      </div>
    </div>
    <div>
      <q-input v-model="user.options.telegram" label="Telegram Id" outlined>
        <template v-slot:append>
          <q-btn round dense flat icon="add" @click="getTelegramId" />
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
          v-model="user.two_factor"
          label="Двухэтапная аутентификация "
          color="negative"
          @update:model-value="setTwoFactor"
        />
      </div>
      <div>
        <q-checkbox
          v-model="user.moderator"
          label="Модератор"
          color="teal"
        />
      </div>
    </div>
    <div v-if="user.two_factor">
      <div class="text-grey q-pa-sm">Двухэтапная аутентификация через</div>
      <div class="q-mb-sm row items-center">
        <div>
          <q-checkbox v-model="user.options.two_factor_enable" val="telegram" />
        </div>
        <div style="flex-grow: 1;">
          Telegram
        </div>
      </div>
      <div class="row items-center">
        <div>
          <q-checkbox v-model="user.options.two_factor_enable" val="mail" />
        </div>
        <div>
          E-mail
        </div>
      </div>
      <div class="row items-center">
        <div>
          <q-checkbox v-model="user.options.two_factor_enable" val="google2fa" />
        </div>
        <div class="q-py-md">
          Google Authenticator
        </div>
      </div>
    </div>
    <div class="text-right q-gutter-md">
      <q-btn color="negative" flat label="Отмена" @click="editCancel" />
      <q-btn color="primary" :label="user.id ? 'Сохранить' : 'Добавить'" @click="saveData" />
    </div>
  </div>
</template>

<script>
import { getLastUserFromTelegram, getUserInfo, updateUser } from 'src/Modules/User/api/user'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ChangeAvatar from 'src/Modules/User/components/ChangeAvatar/index.vue'

export default {
  components: {
    QSelectExecutor,
    ChangeAvatar
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
        options: {},
        hide: true,
        moderator: false
      }
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    setTwoFactor(val) {
      if (val === true) {
        this.user.options.two_factor_enable = []
      }
    },
    getTelegramId() {
      getLastUserFromTelegram()
        .then(res => {
          this.$q.dialog({
            title: 'Последний id: ' + res.data.id,
            message: 'Проверьте у пользователя: ' + res.data.first_name
          }).onOk(() => {
            this.user.options.telegram = res.data.id
          })
        })
    },
    editCancel() {
      this.$emit('cancel')
    },
    saveData() {
      updateUser(this.user)
        .then(() => {
          this.$emit('success')
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
          })
      }
    }

  }
}
</script>

<style scoped>

</style>
