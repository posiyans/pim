<template>
  <q-img v-if="url" :src="url" spinner-color="white">
    <template v-slot:loading>
      <q-spinner-gears color="secondary" size="0.5em" />
    </template>
  </q-img>
  <q-spinner-gears v-else color="secondary" size="1em" />
</template>

<script>
import { getUserAvatar } from 'src/Modules/User/api/user'

export default {
  props: {
    id: {
      type: [String, Number],
      required: true
    },
  },
  data() {
    return {
      url: null
    }
  },
  computed: {
    key() {
      return this.$store.state.avatar.key
    }
  },
  mounted() {
    this.getData()
  },
  watch: {
    key() {
      this.getData()
    }
  },
  methods: {
    getData() {
      const data = {
        id: this.id,
        sail: this.sail
      }
      getUserAvatar(data)
        .then(res => {
          this.url = URL.createObjectURL(res.data)
        })
        .catch(() => {
          this.getData()
        })
    }
  }
}
</script>

<style scoped>

</style>
