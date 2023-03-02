<template>
  <div>
    <q-btn flat color="primary" label="Перенести" @click="showDialog" />
    <q-dialog v-model="showPerenos">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="">Перенести срок выполнения</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <div class="q-pa-lg row items-center q-col-gutter-sm">
            <div>
              <InputDate
                v-model="date"
                label="Новая дата"
                outlined
                dense
              />
            </div>
            <div>
              <q-btn
                color="primary"
                label="Перенести"
                @click="moveDate"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import InputDate from 'src/components/InputDate/index.vue'
import { transferDate } from 'src/Modules/Task/api/task'

export default {
  components: {
    InputDate
  },
  props: {
    taskId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      showPerenos: false,
      date: ''
    }
  },
  methods: {
    showDialog() {
      this.date = ''
      this.showPerenos = true
    },
    moveDate() {
      const data = {
        id: this.taskId,
        date: this.date
      }
      transferDate(data)
        .then(res => {
          this.$q.notify({
            message: 'Перенесенно',
            color: 'secondary'
          })
          this.showPerenos = false
          this.$emit('reload')
        })
    }
  }
}
</script>

<style scoped>

</style>
