<template>
  <div>
    <q-btn v-if="roles.includes('moderator')" :loading="loading" color="negative" label="В архив" @click="showFormStatus = true" />

    <q-dialog v-model="showFormStatus">
      <q-card>
        <q-card-section class="row items-center q-pb-none" style="min-width: 400px;">
          <div class="text-h6">Отправить протокол и ВСЕ его задачи в Архив?</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <div class="text-right q-gutter-md">

            <q-btn color="negative" flat label="Отмена" v-close-popup />
            <q-btn color="negative" label="В архив" @click="sendToArchiv" />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
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

</style>
