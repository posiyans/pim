<template>
  <div v-if="show">
    (+{{ report.show }})
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  computed: {
    user() {
      return this.$store.state.user.info
    },
    report() {
      return this.item.view_report.find(item => item.user_id === this.user.id)
    },
    show() {
      if (this.report) {
        return this.report.show > 0
      }
      return false
    }

  }
}
</script>

<style scoped>

</style>
