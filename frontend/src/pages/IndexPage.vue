<template>
  <q-page class="flex flex-center">
    <img
      alt="Quasar logo"
      src="~assets/quasar-logo-vertical.svg"
      style="width: 200px; height: 200px"
    >
    <ChangeAvatar :id="1" size="120px" />
  </q-page>
</template>

<script>
import { defineComponent } from 'vue'
import ChangeAvatar from 'src/Modules/User/components/ChangeAvatar/index.vue'

export default defineComponent({
  name: 'IndexPage',
  components: {
    ChangeAvatar
  }
})
</script>
