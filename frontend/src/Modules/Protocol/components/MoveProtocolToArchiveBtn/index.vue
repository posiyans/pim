<template>
  <div>
    <el-button v-if="roles.includes('admin')" :loading="loading" type="danger" @click="showFormStatus = true">В архив</el-button>

    <el-dialog v-model="showFormStatus" title="Отправить протокол и ВСЕ его задачи в Архив?" class="dialog">
      <template #footer>
        <el-button @click="showFormStatus = false">Отмена</el-button>
        <el-button type="danger" @click="sendToArchiv">В архив</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { protokolToArchiv } from 'src/Modules/Protocol/api/protocol.js'

export default {
  props: {
    protocolId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      loading: false,
      showFormStatus: false,
    }
  },
  computed: {
    user() {
      return this.$store.state.user.info
    },
    roles() {
      return this.user.roles
    }
  },

  methods: {
    sendToArchiv() {
      this.loading = true
      const data = {
        id: this.protocolId
      }
      protokolToArchiv(data)
        .then(response => {
          this.loading = false
          this.$emit('reload')
        })
      this.showFormStatus = false
    }

  }
}
</script>

<style scoped>
.task-text {
  padding: 1px;
  font-style: italic;
}

.link {
  color: #000061;
  padding: 0 15px;
}

.link:hover {
  color: #0000ff;
  text-decoration: underline;
  cursor: pointer;
}

.edit-button {
  padding-left: 200px;
}

.dialog {
  background-color: #ffeaea;
  color: green;
}

</style>
