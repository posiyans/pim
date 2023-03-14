<template>
  <div v-if="task">
    <q-card class="q-mb-sm">
      <q-card-section>
        <div>
          <div class="">
            <b>Задача id: </b>{{ task.id }}
          </div>
          <div class="row items-center q-col-gutter-sm">
            <div class="text-weight-bold">Протокол:</div>
            <div>
              {{ task.protokol.title }}
            </div>
            <div>от</div>
            <div>
              {{ task.protokol.descriptions.date }}
            </div>
          </div>
          <div><b>Доклад:</b> {{ task.partition.speaker }}</div>
          <div><b>Тема:</b> {{ task.partition.number }}. {{ task.partition.text }}</div>
        </div>
      </q-card-section>
    </q-card>
    <q-card class="">
      <q-card-section>
        <div class="q-gutter-sm">
          <div v-if="task.arxiv" class="text-negative" v-html="task.arxiv" />
          <div class="q-pa-sm row items-center q-col-gutter-sm">
            <div class="text-weight-bolder">
              Задача №
            </div>
            <div class="text-secondary">
              {{ task.partition.number }}.{{ task.number }}.
              <q-popup-edit v-model="task.number" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus type="number" @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>

          <div class="text-secondary">
            {{ task.text }}
            <q-popup-edit v-model="task.text" auto-save v-slot="scope">
              <q-editor
                v-model="scope.value"
                dense
                autofocus
                :toolbar="[
                  ['bold', 'italic', 'strike', 'underline'],
                  ['cancel', 'save']
                ]"
                :definitions="{
                  cancel: {
                    tip: 'Отмена',
                    icon: 'cancel',
                    color: 'negative',
                    handler: scope.cancel
                  },
                  save: {
                    tip: 'Сохранить',
                    icon: 'done',
                    color: 'secondary',
                    handler: scope.set,
                    disable: scope.validate(scope.value) === false || scope.initialValue === scope.value
                  }
                }"
              >
              </q-editor>
            </q-popup-edit>
          </div>
          <div class="text-no-wrap">
            Исполнители:
            <span class="text-secondary">
              {{ task.executor }}
          </span>
            <q-popup-edit v-model="task.executor" v-slot="scope">
              <q-input v-model="scope.value" dense autofocus autogrow @keyup.enter="scope.set">
                <template v-slot:after>
                  <q-btn
                    flat dense color="negative" icon="cancel"
                    @click.stop.prevent="scope.cancel"
                  />

                  <q-btn
                    flat dense color="positive" icon="save"
                    @click.stop.prevent="scope.set"
                    :disable="scope.validate(scope.value) === false || scope.initialValue === scope.value"
                  />
                </template>
              </q-input>
            </q-popup-edit>
          </div>
          <div class="q-mb-md">
            <InputDate v-model="task.data_ispoln" placeholder="Укажите дату" label="Выполнить до" outlined clearable />
          </div>
          <div class="q-mb-md">
            <QSelectExecutor
              v-model="executors"
              multiple
              outlined
            />
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <div>
          <q-btn flat color="negative" label="Отмена" :to="'/task/show/' + task.id" />
          <q-btn color="primary" label="Сохранить" @click="save" />
        </div>
      </q-card-actions>
    </q-card>
  </div>
</template>

<script>
import { fetchTask, updateTask } from 'src/Modules/Task/api/task.js'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import InputDate from 'components/InputDate/index.vue'

export default {
  name: 'TaskEdit',
  components: {
    QSelectExecutor,
    InputDate
  },
  directives: {},
  filters: {
    selectExecutorFilter(users) {
      return users.length === 0 ? 'no-select' : ''
    },
    executorFilter(users) {
      return users.length === 0 ? 'alert' : ''
    },
    statusFilter(status) {
      if (status) {
        return 'success'
      } else {
        return 'danger'
      }
    }
  },
  data() {
    return {
      listQuery: {
        id: this.$route.params.id
      },
      task: null,
      executors: []
    }
  },
  mounted() {
    this.getTask()
  },
  methods: {
    getTask() {
      this.listLoading = true
      this.listQuery.id = this.$route.params.id
      fetchTask(this.listQuery)
        .then(response => {
          this.task = response.data
          this.executors = this.task.executors.map(item => item.user_id)
        })
        .finally(() => {
          this.listLoading = false
        })
    },
    save() {
      this.task.users = this.executors
      updateTask(this.task)
        .then(response => {
          this.$router.push('/task/show/' + this.task.id)
        })
    }
  }
}
</script>

<style>

</style>
