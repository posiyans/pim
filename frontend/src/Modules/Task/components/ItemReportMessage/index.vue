<template>
  <q-chat-message
    :name="item.user.name"
    avatar="~/assets/user.png"
    :stamp="item.created_at"
    :sent="send"
    text-color="black"
    bg-color="grey-4"
  >

    <div>
      {{ item.text }}
      <div v-if="item.file.length > 0" class="link">
        <div @click="getUrlFile(item.file[0])">Фаил: {{ item.file[0].name }}</div>
      </div>
      <span v-if="item.deleted_at" class="text-red">Удалено</span>
    </div>
  </q-chat-message>
</template>

<script>
import { downloadReport } from 'src/Modules/Task/api/task'
import { exportFile } from 'quasar'

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
    send() {
      return this.user.id === this.item.user_id
    }
  },
  methods: {
    getUrlFile(file) {
      downloadReport(file.hash).then(response => {
        exportFile(file.name, response.data)
      })
    },
  }
}
</script>

<style scoped>
.link {
  color: #000089;
}
</style>
