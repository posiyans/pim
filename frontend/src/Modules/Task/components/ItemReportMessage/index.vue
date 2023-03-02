<template>
  <q-chat-message
    :name="item.user.name"
    avatar="~/assets/user.png"
    :sent="send"
    :text-color="item.deleted_at ? 'grey' : 'black'"
    bg-color="grey-2"
  >
    <div class="relative-position q-pr-md">
      <div v-if="moderator && !item.deleted_at" class="absolute-top-right">
        <q-btn
          round
          flat
          color="negative"
          size="xs"
          icon="delete"
          @click="deleteMessage"
        />
      </div>
      <div class="q-pa-sm">
        {{ item.text }}
      </div>
      <div v-if="item.file.length > 0">
        Вложение:
        <FileBlock v-for="f in item.file" :file="f" :key="f.id" />
      </div>

      <div class="text-grey text-small-80 q-pt-sm" :class="{ 'text-right': send }">
        <ShowTime :time="item.created_at" />
        <span v-if="item.deleted_at" class="text-red q-px-md">Удалено</span>
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
    moderator() {
      return !!this.user.moderator
    },
    send() {
      return this.user.id === this.item.user_id
    }
  },
  methods: {
    deleteMessage() {
      this.$q.dialog({
        title: 'Подтвердить',
        message: 'Удалить данный отчет?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        const data = {
          id: this.item.id
        }
        delReport(data)
          .finally(() => {
            this.$emit('reload')
          })
      })
    },
    getUrlFile(file) {
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
