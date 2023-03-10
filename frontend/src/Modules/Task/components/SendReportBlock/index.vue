<template>
  <div v-if="!disable" class="row items-end q-pb-xs relative-position">
    <div class="hidden">
      <input
        ref="file"
        type="file"
        class="hidden"
        @change="change"
      >
    </div>
    <div class="text-grey-8 q-pb-sm" style="min-width: 20px;">
      <q-btn flat icon="attach_file" @click.stop="attacheFile" />
    </div>
    <div class="col-grow">
      <q-input
        v-model="message"
        dense
        autogrow
        bottom-slots
        :placeholder="placeholderInput"
        @keyup.enter.prevent="enterUp"
      >
        <template v-slot:hint>
          <div v-if="file" class="row">
            <div class="q-mr-md text-grey-10">
              {{ inputHint }}
            </div>
            <div class="delete-btn" @click="deleteFile">
              <q-icon name="highlight_off" />
            </div>
          </div>
        </template>
      </q-input>
    </div>
    <div class="q-pb-sm">
      <q-btn flat icon="send" :loading="showSendBlock" :color="message.length > 0 ? 'teal-8' : 'grey-8'" @click="sendMessage" />
    </div>
  </div>
</template>

<script>
import { addReport } from 'src/Modules/Task/api/task'

export default {
  props: {
    taskId: {
      type: [Number, String],
      required: true
    },
    disable: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      showSendBlock: false,
      file: null,
      message: '',
      listLoading: false,
      newMessage: {
        text: '',
        file: ''
      },
      fileName: 'Выберите файл',
    }
  },
  computed: {
    sendEnter() {
      return this.$store.state.link.sendByEnter
    },
    inputHint() {
      if (this.file) {
        return this.file.name
      }
      return ''
    },
    placeholderInput() {
      if (this.file) {
        return 'Добавить описание к файлу'
      }
      return 'Добавить отчет'
    },
    sendByEnter() {
      return this.$store.state.link.sendByEnter
    }
  },
  methods: {
    change() {
      if (this.$refs.file.files[0]) {
        this.file = this.$refs.file.files[0]
      }
    },
    enterUp(evn) {
      if (!evn.shiftKey && this.sendEnter) {
        this.send()
      }
    },
    deleteFile() {
      this.file = null
      console.log(this.$refs.file)
    },
    attacheFile() {
      this.$refs.file.click()
    },
    send() {
      this.message = this.message.trim()
      this.sendMessage()
    },
    sendMessage() {
      if (this.message === '') {
        if (this.file) {
          this.$q.notify({
            message: 'Добавьте описание к файлу',
            color: 'negative',
            icon: 'info',
            position: 'center',
            timeout: 500
          })
        }
        return true
      }
      this.showSendBlock = true

      const formData = new FormData()
      formData.append('task', this.taskId)
      formData.append('message', this.message)
      if (this.file) {
        formData.append('file', this.file)
      }
      addReport(formData)
        .then(response => {
          this.message = ''
          this.file = null
          this.$message({
            message: 'Ok',
            type: 'success',
            showClose: true,
            duration: 1000
          })
          this.$emit('reload')
        })
        .finally(() => {
          this.showSendBlock = false
        })
    },
    selectFile(event) {
      this.$message({
        message: 'Файл прикреплен',
        type: 'success',
        showClose: true,
        duration: 1000
      })
      this.fileName = event.target.files[0].name
      this.newMessage.file = event.target.files[0]
    },
    AddReport() {
      if (this.newMessage.text !== '' || this.$refs.file.files[0].name) {
        const formData = new FormData()
        formData.append('file', this.$refs.file.files[0])
        formData.append('message', this.newMessage.text)
        formData.append('task', this.taskId)
        addReport(formData)
          .then(response => {
            this.listLoading = false
            this.$message({
              message: 'Ok',
              type: 'success',
              showClose: true,
              duration: 1000
            })
            this.newMessage.text = ''
            this.newMessage.file = ''
            this.fileName = 'Выберите файл'
            this.$refs.file.value = ''
            this.$emit('reload')
          })


      }
    },
  }
}
</script>

<style scoped lang="scss">
.delete-btn {
  cursor: pointer;
  color: teal;
}

.delete-btn:hover {
  color: $negative;
}
</style>
