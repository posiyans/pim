<template>
  <div v-html="showText" />
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: ''
    }
  },
  computed: {
    showText() {
      return this.text.replace(/\n/g, "<br />");
    }
  }
}
</script>

<style scoped>

</style>
