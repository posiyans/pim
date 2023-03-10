<template>
  <q-item
    clickable
    :to="item.link"
    @click="clickLink"
  >
    <q-item-section
      v-if="item.icon"
      avatar
    >
      <q-icon :name="item.icon" />
    </q-item-section>

    <q-item-section>
      <q-item-label>{{ item.title }}</q-item-label>
    </q-item-section>
  </q-item>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  methods: {
    clickLink() {
      if (this.$q.platform.is.mobile) {
        this.$store.commit('header/toggleLeftDrawer')
      }
    }
  }
}
</script>

<style scoped>

</style>
