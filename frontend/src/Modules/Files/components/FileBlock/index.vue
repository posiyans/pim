<template>
  <div class="cursor-pointer file row items-center q-col-gutter-sm">
    <div class="text-primary">
      {{ i }}
    </div>
    <div class="cursor-pointer file text-primary" @click="getFile">
      {{ file.name }}
    </div>
    <div class="text-grey">
      {{ size }}
    </div>
    <div v-if="edit">
      <q-btn flat color="negative" :icon="hover ? 'delete' : 'close'" size="10px" @mousemove="hover = true" @mouseout="hover = false" @click="deleteFile" />
    </div>
  </div>
</template>

<script>
import { sizeFilter } from 'src/utils/file'
import { exportFile } from 'quasar'
import { deleteFile, downloadFile } from 'src/Modules/Files/api/file'

export default {
  props: {
    file: {
      type: Object,
      required: true
    },
    index: {
      type: [Number, String],
      default: ''
    },
    edit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      hover: false
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
    deleteFile() {
      const data = {
        uid: this.file.uid
      }
      deleteFile(data)
        .then(res => {
          this.$emit('reload')
        })
    },
    getFile() {
      const data = {
        uid: this.file.uid
      }
      downloadFile(data)
        .then(response => {
          let fileName = response.headers['content-disposition'].split('filename=')[1].split(';')[0]
          try {
            fileName = decodeURIComponent(response.headers['content-disposition'].split("filename*=utf-8''")[1].split(';')[0])
          } catch (e) {
          }
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

<style scoped lang="scss">
.file {
  &:hover {
    opacity: 0.7;
  }
}
</style>
