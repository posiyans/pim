<template>
  <q-form
    @submit="sendTestMail"
    class="q-gutter-md"
    ref="mailRef"
  >
    <div style="min-width: 220px;">
      <q-input
        v-model="testMail"
        outlined
        dense
        label="Почта"
        type="mail"
        hint="Отправить тестовое письмо"
        :rules="[EmailValidate]"
      />
    </div>
    <div>
      <q-btn label="Отправить" color="secondary" type="submit" />
    </div>
  </q-form>

</template>

<script>
import { EmailValidate } from 'src/utils/helpers/validate'
import { sendTestEmail } from 'src/Modules/Setting/api/setting'

export default {
  data() {
    return {
      EmailValidate,
      mail: {},
      testMail: ''
    }
  },
  mounted() {
    // this.getData()
  },
  methods: {
    sendTestMail() {
      if (this.testMail) {
        this.$refs.mailRef.validate()
          .then(() => {
            console.log('ok')
            const data = {
              mail: this.testMail
            }
            sendTestEmail(data)
              .then(res => {
                this.$q.notify({
                  type: 'primary',
                  message: 'Тестовое письмо отправленно'
                })
              })
              .catch(er => {
                this.$q.notify({
                  type: 'negative',
                  message: 'Ошибка отправки, проверьте настройки'
                })
                this.$q.notify({
                  type: 'info',
                  message: er.response.data.message,
                  timeout: 0,
                  closeBtn: true
                })
              })
          })
          .catch(() => {
            console.log('error')
          })
      } else {
        this.$q.notify({
          type: 'negative',
          message: 'Пожалуйста, введите действующий email'
        })
      }

    }
  }
}
</script>

<style scoped>

</style>
