<template>
  <div @click="downloadProtokol">
    <slot>
      <q-btn label="Скачать" color="primary" />
    </slot>
  </div>
</template>

<script>
import { downloadProtocol } from 'src/Modules/Protocol/api/protocol'
import { exportFile } from 'quasar'

export default {
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  methods: {
    downloadProtokol() {
      const data = {
        id: this.id
      }
      downloadProtocol(data)
        .then(response => {
          const fileName = response.headers['content-disposition'].split('filename=')[1].split(';')[0] || 'Протокол № ' + this.id + '.docx'
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
    }

  }
}
</script>

<style scoped>

</style>
