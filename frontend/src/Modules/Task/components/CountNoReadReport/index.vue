<template>
  <div v-if="count > 0">
    <q-btn icon="message" flat>
      <q-badge color="red" floating class="text-white">{{ showCount }}</q-badge>
    </q-btn>
  </div>
</template>

<script>

export default {
  computed: {
    count() {
      return this.$store.state.header.countNoReadReport
    },
    showCount() {
      if (this.count > 100) {
        return '>99'
      }
      return this.count
    }
  }
}
</script>

<style scoped>

</style>
