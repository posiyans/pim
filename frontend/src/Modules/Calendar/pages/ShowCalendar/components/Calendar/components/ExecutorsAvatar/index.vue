<template>
  <div v-for="i in showId" :key="i" class="q-mr-xs">
    <AvatarById :id="i" size="16px" />
  </div>
</template>

<script>
import AvatarById from 'src/Modules/User/components/AvatarById/index.vue'

export default {
  components: {
    AvatarById
  },
  props: {
    executors: {
      type: Array,
      required: true
    }
  },
  computed: {
    showId() {
      return this.executors.filter(item => item.executor).map(item => item.user_id)
    }
  }
}
</script>

<style scoped>

</style>
