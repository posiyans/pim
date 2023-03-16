<template>
  <div class="">
    <q-card>
      <q-card-section class="">
        <div class="row">
          <div class="col-grow q-gutter-md">
            <div class="row">
              <div class="col-6 q-pr-md">
                <q-input v-model="user.name" readonly label="Имя" outlined />
              </div>
              <div class="col-6">
                <QSelectExecutor v-model="user.aliases" readonly outlined multiple label="Доступ к " />
              </div>
            </div>
            <div>
              <q-input v-model="user.full_name" readonly label="Полное имя" outlined />
            </div>
            <div>
              <q-input v-model="user.login" readonly label="Login" outlined />
            </div>

          </div>
          <div class="q-pa-md">
            <ChangeAvatar v-if="user.id" :id="user.id" size="120px" />
          </div>
        </div>
        <div class="q-mt-md q-gutter-md">
          <div>
            <q-input v-model="user.email" label="E-mail" outlined />
          </div>
          <div>
            <q-input v-model="user.options.telegram" label="Telegram Id" outlined>
              <template v-slot:append>
                <q-btn round dense flat icon="add" @click="getTelegramId" />
              </template>
            </q-input>
          </div>
        </div>
        <User2FaSetting :user-id="user.id" />

        <div class="text-right q-gutter-md">
          <q-btn color="negative" flat label="Отмена" @click="editCancel" />
          <q-btn color="primary" :label="user.id ? 'Сохранить' : 'Добавить'" @click="saveData" />
        </div>
      </q-card-section>

    </q-card>
  </div>
</template>

<script>
import { getLastUserFromTelegram, updateUser } from 'src/Modules/User/api/user'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ChangeAvatar from 'src/Modules/User/components/ChangeAvatar/index.vue'
import User2FaSetting from 'src/Modules/User/components/User2FaSetting/index.vue'

export default {
  components: {
    QSelectExecutor,
    ChangeAvatar,
    User2FaSetting
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
    // this.getData()
    this.user = Object.assign({}, this.$store.state.user.info)
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
    }

  }
}
</script>

<style scoped>

</style>
