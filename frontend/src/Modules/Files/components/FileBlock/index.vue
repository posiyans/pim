<template>
  <div class="cursor-pointer file text-primary" @click="getFile">
    {{ i }} {{ file.name }} {{ size }}
  </div>
</template>

<script>
import { sizeFilter } from 'src/utils/file'
import { downloadReport } from 'src/Modules/Task/api/task'
import { exportFile } from 'quasar'
import { downloadFile } from 'src/Modules/Files/api/file'

export default {
  props: {
    file: {
      type: Object,
      required: true
    },
    index: {
      type: [Number, String],
      default: ''
    }
  },
  computed: {
    i() {
      if (this.index) {
        return this.index + '. '
      }
      return ''
    },
    size() {
      if (this.file.size > 0) {
        return sizeFilter(this.file.size)
      }
      return ''
    }
  },
  methods: {
    getFile() {
      const data = {
        id: this.file.id,
        hash: this.file.hash
      }
      downloadFile(data)
        .then(response => {
          const fileName = response.headers['content-disposition'].split('filename=')[1].split(';')[0]
          exportFile(fileName, response.data)
        })
        .catch(er => {
          this.$message({
            message: 'Файл не найден',
            type: 'error',
            showClose: true,
            duration: 3000
          })
        })
    },
    getUrlFile(file) {
      console.log(file)

      downloadReport(file.hash).then(response => {
        exportFile(file.name, response.data)
      })
    },
  }
}
</script>

<style scoped lang="scss">
.file {
  &:hover {
    opacity: 0.7;
  }
}
</style>
