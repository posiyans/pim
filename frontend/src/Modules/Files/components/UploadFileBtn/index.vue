<template>
  <div>
    <div class="hidden">
      <input
        ref="file"
        type="file"
        class="hidden"
        @change="change"
      >
    </div>
    <q-btn icon="add" flat color="primary" @click="attacheFile" />
  </div>
</template>

<script>
import { uploadFile } from 'src/Modules/Files/api/file'

export default {
  props: {
    type: {
      type: String,
      required: true
    },
    id: {
      type: [String, Number],
      required: true
    }
  },
  methods: {
    change() {
      const file = this.$refs.file.files[0] || null
      if (file) {
        const formData = new FormData()
        formData.append('type', this.type)
        formData.append('id', this.id)
        formData.append('file', file)
        uploadFile(formData)
          .then(res => {
            this.$emit('reload')
          })
          .catch(() => {
            this.$q.notify({
              type: 'negative',
              message: 'Ошибка загрузки файла'
            })
          })
      }
    },
    attacheFile() {
      this.$refs.file.click()
    },
  }
}
</script>

<style scoped>

</style>
