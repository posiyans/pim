<template>
  <div>
    <div class="q-gutter-md">
      <div>
        <q-input v-model="mail.MAIL_HOST" outlined label="Host" />
      </div>
      <div>
        <q-input v-model="mail.MAIL_PORT" outlined label="Port" />
      </div>
      <div>
        <q-input v-model="mail.MAIL_USERNAME" outlined label="UserName" />
      </div>
      <div>
        <q-input v-model="mail.MAIL_PASSWORD" outlined label="Password" :type="passwordShow ? 'string' : 'password'">
          <template v-slot:append>
            <q-icon :name="passwordShow ? 'visibility_off' : 'visibility'" @click="passwordShow = !passwordShow" class="cursor-pointer" />
          </template>
        </q-input>
      </div>
      <div>
        <q-input v-model="mail.MAIL_ENCRYPTION" outlined label="Ssl or Tls" />
      </div>
      <div class="row">
        <div>
          <q-btn label="Сохранить" color="primary" @click="saveData" />
        </div>
        <q-space />
        <SendTestMailBlock class="row q-col-gutter-sm" />
      </div>
    </div>
  </div>
</template>

<script>
import { getMailSetting, updateMailSetting } from 'src/Modules/Setting/api/setting'
import SendTestMailBlock from 'src/Modules/Setting/components/SendTestMailBlock/index.vue'

export default {
  components: {
    SendTestMailBlock
  },
  data() {
    return {
      passwordShow: false,
      mail: {},
      testMail: ''
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      getMailSetting()
        .then(res => {
          this.mail = res.data
        })
    },
    saveData() {
      const data = {
        mail: this.mail
      }
      updateMailSetting(data)
        .then(res => {
          this.getData()
        })
    }
  }
}
</script>

<style scoped>

</style>
