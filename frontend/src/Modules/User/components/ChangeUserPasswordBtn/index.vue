<template>
  <div>
    <q-btn label="Пароль" outline color="negative" @click="showDialog" />
    <q-dialog v-model="dialogVisible">
      <q-card style="width: 700px; max-width: 80vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Сменить пароль для "{{ userName }}"</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <q-input
            v-model="newPassword"
            label="Новый пароль"
            outlined
            :rules="[ val => val && val.length > 7 || 'Минимум 8 символов']"
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn color="negative" flat label="Отмена" v-close-popup></q-btn>
          <q-btn color="primary" label="Сменить" @click="changePassword"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { changeUserPassword } from 'src/Modules/User/api/user'

export default {
  props: {
    userId: {
      type: [String, Number],
      required: true
    },
    userName: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      newPassword: '',
      dialogVisible: false
    }
  },
  methods: {
    changePassword() {
      const data = {
        id: this.userId,
        password: this.newPassword
      }
      changeUserPassword(data)
        .then(res => {
          this.dialogVisible = false
          this.$q.notify({
            message: 'Ok',
            position: 'top',
            color: 'secondary',
            timeout: 500
          })
        })
    },
    showDialog() {
      this.newPassword = this.randomString(8)
      this.dialogVisible = true
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

</style>
