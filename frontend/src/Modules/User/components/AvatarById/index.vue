<template>
  <q-avatar :rounded="rounded" :square="square" :size="size">
    <UserAvatarImg :id="id">
      <slot></slot>
    </UserAvatarImg>
  </q-avatar>
</template>

<script>
import UserAvatarImg from 'src/Modules/User/components/AvatarImg/index.vue'

export default {
  components: {
    UserAvatarImg
  },
  props: {
    id: {
      type: [String, Number],
      required: true
    },
    size: {
      type: String,
      default: ''
    },
    rounded: {
      type: Boolean,
      default: false
    },
    square: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>

</style>
