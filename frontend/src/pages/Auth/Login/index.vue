<template>
  <div class="fullscreen" style="background-color: #2d3a4b;">
    <div class="fixed-center">
      <div class="text-h4 text-weight-bold q-pb-lg text-white">
        Добро Пожаловать
      </div>
      <div>
        <q-form
          @submit="handleLogin"
          ref="inputForm"
          class="q-gutter-md"
        >
          <q-input
            outlined
            v-model="loginForm.username"
            label="Логин"
            lazy-rules
            dense
            bg-color="white"
            class="text-blue-10"
            :rules="[ val => val && val.length > 0 || 'Поле не может быть пустым']"
          />

          <q-input
            outlined
            v-model="loginForm.password"
            label="Пароль"
            type="password"
            class="text-blue-10"
            dense
            bg-color="white"
            lazy-rules
            :rules="[val => val && val.length > 0 || 'Поле не может быть пустым']"
          />

          <q-input
            v-if="showCodeForm"
            outlined
            v-model="loginForm.code"
            label="Код подтверждения"
            class="text-blue-10"
            dense
            bg-color="white"
            lazy-rules
            maxlength="6"
            @update:model-value="setSms"
          />


          <div>
            <q-btn label="Войти" :loading="loading" class="full-width" type="submit" full color="primary" />

          </div>
        </q-form>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  components: {},
  data() {
    return {
      showCodeForm: false,
      loginForm: {
        username: '',
        password: '',
        code: ''
      },
      loading: false,
      showDialog: false,
      redirect: undefined,
      titleSmsDialod: ''
    }
  },
  computed: {},
  watch: {
    $route: {
      handler: function (route) {
        this.redirect = route.query && route.query.redirect
      },
      immediate: true
    },
    'loginForm.sms': function (sms) {
      if (sms.length === 4) {
        this.handleLogin()
      }
    }
  },
  methods: {
    setSms(val) {
      if (val.length === 6) {
        this.handleLogin()
      }
    },
    showPwd() {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
    },
    handleLogin() {
      this.$refs.inputForm.validate()
        .then(() => {
          this.loading = true
          this.$store.dispatch('user/loginUser', this.loginForm)
            .then(res => {
              if (res.status === 'done') {
                this.$router.push('/')
              } else if (res.status === 'sendCode') {
                this.showCodeForm = true
              } else if (res.status === 'errorCode') {
                this.$q.notify({
                  message: res.error,
                  color: 'negative'
                })
              }
            })
            .catch((error) => {
              if (error) {
                this.$q.notify({
                  message: error.response.data.error,
                  color: 'negative'
                })
              }
            })
            .finally(() => {
              this.loading = false
            })
        })
    }
  }
}
</script>

<style scoped lang="scss">

</style>
