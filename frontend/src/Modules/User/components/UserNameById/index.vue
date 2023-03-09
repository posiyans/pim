<template>
  <div>
    {{ userName }}
  </div>
</template>

<script>
export default {
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  computed: {
    users() {
      return this.$store.state.users.executors
    },
    userName() {
      if (this.users) {
        const user = this.users.find(item => item.id === this.id)
        if (user) {
          return user.name
        }
      }
      return ''
    }
  },
  mounted() {
    this.$store.dispatch('users/getExecutors')
  },
}
</script>

<style scoped>

</style>
