<template>
  <div>
    <div @click="changeKey">
      <slot>
        <q-btn label="Обновить SecretKey" flat color="primary" />
      </slot>
    </div>
    <q-dialog v-model="dialogVisible" persistent maximized>
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Обновить SecretKey</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section v-if="secret">
          <div class="text-center">
            Ваш секретный ключ
          </div>
          <div class="text-center">
            <div class="q-pa-md text-weight-bold text-h6" style="font-family: monospace;">
              <div v-for="line in showText" :key="line" class="row items-center q-col-gutter-md justify-center">
                <div v-for="item in line" :key="item">{{ item }}</div>
              </div>
            </div>
            <div class="row justify-center q-pa-md">
              <VueQRCodeComponent :text="qrCodeText" />
            </div>
            <div>
              1.Загрузите приложение Google Authenticator на свое устройство и откройте его.
            </div>
            <div>
              2. Найдите и нажмите знак «+».
            </div>
            <div>
              3. Отсканируйте Qr-code или введите ключ, чтобы добавить свою учетную запись в Google Authenticator.
            </div>
            <div class="q-pa-md">
              <q-btn color="negative" label="Закрыть" v-close-popup />
            </div>
          </div>
        </q-card-section>
        <q-card-section v-else class="row justify-center">
          <div class="text-center" style="max-width: 600px;">
            <div class="q-pa-lg">
              Для смены SecretKey введите текущий пароль от учетной записи
            </div>
            <div>
              <q-input v-model="password" label="Пароль" outlined dense type="password" />
            </div>
            <div class="text-right q-pa-md">
              <q-btn color="primary" :disable="!password" label="Обновить" @click="updateKey" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { changeGoogle2FaSecretKey } from 'src/Modules/Google2Fa/api/google2fa'
import VueQRCodeComponent from 'vue-qrcode-component'

export default {
  components: {
    VueQRCodeComponent
  },
  data() {
    return {
      dialogVisible: false,
      password: '',
      secret: null
    }
  },
  computed: {
    showText() {
      const data = []
      let pos = 0
      for (let i = 0; i < 2; i++) {
        const row = []
        for (let j = 0; j < 4; j++) {
          row.push(this.secret.substr(pos, 4))
          pos = pos + 4
        }
        data.push(row)
      }
      return data
    },
    user() {
      return this.$store.state.user.info
    },
    qrCodeText() {
      return 'otpauth://totp/' + this.user.login + '?issuer=' + window.location.hostname + '&secret=' + this.secret
    }
  },
  mounted() {
    console.log(this.$router)
    console.log(this.$route)
  },
  methods: {
    changeKey() {
      console.log('click')
      this.secret = null
      this.dialogVisible = true
      console.log(this.dialogVisible)
    },
    updateKey() {
      const data = {
        password: this.password
      }
      changeGoogle2FaSecretKey(data)
        .then(res => {
          this.secret = res.data.secret
        })
        .catch(er => {
          this.$q.notify({
            message: er.response.data.error,
            type: 'negative'
          })
        })

    }
  }
}
</script>

<style scoped>

</style>
