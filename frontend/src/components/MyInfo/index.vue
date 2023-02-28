<template>
  <div>
    {{ name.name }} {{ name.role }}
  </div>
</template>

<script>
export default {
  computed: {
    name() {
      return this.$store.state.user.info
    }
  }
}
</script>

<style scoped>

</style>
