<template>
  <q-chat-message
    :name="item.user.name"
    avatar="~/assets/user.png"
    :sent="send"
    text-color="black"
    bg-color="grey-2"
  >
    <div>
      <div class="q-pa-sm">
        {{ item.text }}
      </div>
      <div v-if="item.file.length > 0">
        Вложение:
        <FileBlock v-for="f in item.file" :file="f" :key="f.id" />
      </div>
      <span v-if="item.deleted_at" class="text-red">Удалено</span>
      <div class="text-grey text-small-80 q-pt-sm" :class="{ 'text-right': send }">
        <ShowTime :time="item.created_at" />
      </div>
    </div>
  </q-chat-message>
</template>

<script>
import { delReport, downloadReport } from 'src/Modules/Task/api/task'
import { exportFile } from 'quasar'
import ShowTime from 'src/components/ShowTime/index.vue'
import FileBlock from 'src/Modules/Files/components/FileBlock/index.vue'

export default {
  components: {
    ShowTime,
    FileBlock
  },
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
      console.log(file)

      downloadReport(file.hash).then(response => {
        exportFile(file.name, response.data)
      })
    },
    removeReport(report) {
      if (report.user_id === this.userId || this.roles.includes('admin')) {
        let isRemote = confirm('Удалить отчет????')
        if (isRemote) {
          delReport(report.id).then(response => {
            this.listLoading = false
            this.$message({
              message: 'Ok',
              type: 'success',
              showClose: true,
              duration: 5000
            })
            this.getTask()
          })
        }
        isRemote = false
      }
    },
  }
}
</script>

<style scoped>
.link {
  color: #000089;
}
</style>
