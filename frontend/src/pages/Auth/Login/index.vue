<template>
  <div class="fullscreen" style="background-color: #2d3a4b;">
    <component :is="componentName" />
  </div>
</template>

<script>
import LoginUser from 'src/Modules/Auth/components/LoginUser/index.vue'
import CreateUser from 'src/Modules/Auth/components/CreateUser/index.vue'

export default {
  computed: {
    componentName() {
      if (this.$store.state.user.newUser) {
        return CreateUser
      } else {
        return LoginUser
      }
    }
  }
}
</script>

<style scoped lang="scss">

</style>
