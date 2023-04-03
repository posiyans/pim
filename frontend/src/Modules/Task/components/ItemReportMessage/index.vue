<template>
  <q-chat-message
    :sent="send"
    :text-color="item.deleted_at ? 'grey' : 'black'"
    bg-color="grey-2"
  >
    <template v-slot:name>
      <UserNameById :id="item.user_id" />
    </template>
    <template v-slot:avatar>
      <AvatarById :id="item.user_id" class="q-message-avatar" :class="send ? 'q-message-avatar--sent' : 'q-message-avatar--received'" />
    </template>
    <div class="relative-position q-pr-md">
      <div v-if="removed" class="absolute-top-right">
        <q-btn
          round
          flat
          color="negative"
          size="xs"
          icon="delete"
          @click="deleteMessage"
        />
      </div>
      <div class="q-pa-sm" v-html="showText">
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
import AvatarById from 'src/Modules/User/components/AvatarById/index.vue'
import UserNameById from 'src/Modules/User/components/UserNameById/index.vue'

export default {
  components: {
    ShowTime,
    AvatarById,
    UserNameById,
    FileBlock
  },
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  computed: {
    removed() {
      if (this.item.deleted_at) {
        return false
      }
      if (this.moderator) {
        return true
      }
      if (this.item.user_id === this.user.id) {
        return true
      }
      return false
    },
    showText() {
      return this.item.text.replace(/\n/g, "<br />");
    },
    user() {
      return this.$store.state.user.info
    },
    moderator() {
      // return !!this.user.moderator
      return !!this.user.roles.includes('moderator') || !!this.user.roles.includes('admin')
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
      if (report.user_id === this.userId || this.roles.includes('moderator')) {
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
