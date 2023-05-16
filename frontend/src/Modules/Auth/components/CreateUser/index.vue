<template>
  <div class="fullscreen" style="background-color: #192b42;">
    <div class="fixed-center">
      <div class="text-h4 text-weight-bold q-pb-lg text-white">
        Создать пользователя
      </div>
      <div>
        <q-form
          @submit="handleLogin"
          ref="inputForm"
          class="q-gutter-md"
        >
          <q-input
            outlined
            v-model="userForm.username"
            label="Логин"
            lazy-rules
            dense
            bg-color="white"
            class="text-blue-10"
            :rules="[ val => val && val.length > 0 || 'Поле не может быть пустым']"
          />

          <q-input
            outlined
            v-model="userForm.password"
            label="Пароль"
            type="password"
            class="text-blue-10"
            dense
            bg-color="white"
            lazy-rules
            :rules="[val => val && val.length > 0 || 'Поле не может быть пустым']"
          />
          <q-input
            outlined
            v-model="userForm.confirm_password"
            label="Повторить пароль"
            type="password"
            class="text-blue-10"
            dense
            bg-color="white"
            lazy-rules
            :rules="[
              val => val && val.length > 0 || 'Поле не может быть пустым',
              val => val && val === userForm.password || 'Пароли не совпадают'
              ]"
          />


          <div>
            <q-btn label="Создать" :loading="loading" class="full-width" type="submit" full color="primary" />
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
      userForm: {
        username: 'graywolf',
        password: '12345678',
        confirm_password: '12345678'
      },
      loading: false
    }
  },
  methods: {
    handleLogin() {
      this.$refs.inputForm.validate()
        .then(() => {
          this.loading = true
          this.$store.dispatch('user/createUser', this.userForm)
            .then(res => {
              this.$q.notify({
                message: 'Пользователь успешно создан',
                color: 'secondary'
              })
              this.$store.commit('user/newUser', false)
            })
            .catch((error) => {
              if (error) {
                this.$q.notify({
                  message: 'Ошибка, обратититеь к системному администратору',
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
