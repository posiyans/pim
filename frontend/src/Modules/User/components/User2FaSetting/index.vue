<template>
  <div>
    <div class="row items-center q-col-gutter-md q-pa-md">
      <div>
        <q-checkbox
          v-model="user.two_factor"
          label="Двухэтапная аутентификация "
          color="negative"
          @update:model-value="saveData"
        />
      </div>
    </div>
    <div v-if="user.two_factor">
      <div class="text-grey q-pa-sm">Двухэтапная аутентификация через</div>
      <div class="q-mb-sm row items-center">
        <div :class="{ 'text-negative': !user.two_factor_valid.mail}">
          <q-checkbox v-model="user.two_factor_enable" val="telegram" @update:model-value="saveData" />
          <div v-if="!user.two_factor_valid.telegram">
            sfsfsdf
          </div>
        </div>
        <div :class="{ 'text-negative': !user.two_factor_valid.telegram}">
          <div>
            Telegram
          </div>
          <div v-if="!user.two_factor_valid.telegram" class="text-small-80">
            Не указан id в Telegram
          </div>
        </div>
      </div>
      <div class="row items-center">
        <div>
          <q-checkbox v-model="user.two_factor_enable" val="mail" @update:model-value="saveData" />
        </div>
        <div :class="{ 'text-negative': !user.two_factor_valid.mail}">
          <div>
            E-mail
          </div>
          <div v-if="!user.two_factor_valid.mail" class="text-small-80">
            Не указан адрес почты
          </div>
        </div>
      </div>
      <div class="row items-center">
        <div>
          <q-checkbox v-model="user.two_factor_enable" val="google2fa" @update:model-value="saveData" />
        </div>
        <div class="q-py-md q-mr-lg" :class="{ 'text-negative': !user.two_factor_valid.google2fa}">
          <div>
            Google Authenticator
          </div>
          <div v-if="!user.two_factor_valid.google2fa" class="text-small-80">
            Не утановлен SecretKey
          </div>
        </div>
        <div>
          <ChangeSecretKey />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getTwoFactorSettingsForUser, updateTwoFactorSettingsForUser } from 'src/Modules/User/api/user'
import ChangeSecretKey from 'src/Modules/Google2Fa/components/ChangeSecretKey/index.vue'

export default {
  components: {
    ChangeSecretKey
  },
  props: {
    userId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      user: {}
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      const data = {
        id: this.userId
      }
      getTwoFactorSettingsForUser(data)
        .then(res => {
          this.user = res.data
        })
    },
    saveData() {
      updateTwoFactorSettingsForUser(this.user)
        .then(res => {
          this.getData()
        })
    }
  }
}
</script>

<style scoped>

</style>
