<template>
  <div v-if="user" :key="key">
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
          <EmailField
            v-model="user.email"
            :user-id='user.id'
            :func="updateUserField"
            name="email"
            outlined label="E-mail"
            @reload="reload"
          />
        </div>
        <div>
          <TelegramIdFiled
            v-model="user.options.telegram"
            :user-id='user.id'
            :func="updateUserField"
            name="telegram"
            outlined label="Telegram Id"
            @reload="reload"
          />
        </div>
        <User2FaSetting :user-id="user.id" />
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { updateUserField } from 'src/Modules/User/api/user'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ChangeAvatar from 'src/Modules/User/components/ChangeAvatar/index.vue'
import User2FaSetting from 'src/Modules/User/components/User2FaSetting/index.vue'
import EmailField from 'src/components/Fields/EmailField.vue'
import TelegramIdFiled from 'src/components/Fields/TelegramIdFiled.vue'

export default {
  components: {
    EmailField,
    TelegramIdFiled,
    QSelectExecutor,
    ChangeAvatar,
    User2FaSetting
  },
  data() {
    return {
      updateUserField,
      user: null,
      key: 1
    }
  },
  mounted() {
    this.user = Object.assign({}, this.$store.state.user.info)
  },
  methods: {
    reload() {
      this.$store.dispatch('user/getInfo')
        .finally(() => {
          this.user = Object.assign({}, this.$store.state.user.info)
          this.key++
        })
    }
  }
}
</script>

<style scoped>

</style>
