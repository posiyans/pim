<template>
  <div class="">
    <div class="row items-center q-col-gutter-sm q-pb-md">
      <div class="text-teal text-weight-bold">
        Добавить протокол
      </div>
      <div style="min-width: 220px;">
        <SelectTypeProtocol
          v-model="protokol.type"
          outlined
          dense
          add
        />
      </div>

      <div>
        <div class="hidden">
          <input
            ref="file"
            type="file"
            class="hidden"
            @change="parseFile"
          >
        </div>
        <q-btn outline color="primary" label="Загрузить из файла" @click="$refs.file.click()" />
      </div>
    </div>
    <div v-if="loading">
      <q-spinner
        color="primary"
        size="3em"
      />
    </div>
    <div>
      <q-card class="q-mb-sm">
        <q-card-section>

          <div class="row items-center">
            <div class="field-label">
              Протокол номер:
            </div>
            <div class="field">
              {{ protokol.number }}
              <q-popup-edit v-model="protokol.number" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
            </div>
            <div class="field">
              {{ protokol.title }}
              <q-popup-edit v-model="protokol.title" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
            <div class="q-mx-sm">
              от
            </div>
            <div class="field">
              {{ protokol.date }}
              <q-popup-edit v-model="protokol.date" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Место проведения:
            </div>
            <div class="field">
              {{ protokol.region }}
              <q-popup-edit v-model="protokol.region" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Председатель:
            </div>
            <div class="field">
              {{ protokol.president }}
              <q-popup-edit v-model="protokol.president" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Секретарь:
            </div>
            <div class="field">
              {{ protokol.secretary }}
              <q-popup-edit v-model="protokol.secretary" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Присутствовали:
            </div>
            <div class="field">
              {{ protokol.composition }}
              <q-popup-edit v-model="protokol.composition" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
        </q-card-section>
      </q-card>
      <q-btn icon="add" flat label="Добавить доклад" color="primary" @click="addPartition" />
      <div v-for="partition in protokol.partition" :key="partition.id" class="q-pb-sm">
        <q-card>
          <q-card-section>
            <div class="row items-center text-weight-bold">
              <div>
                {{ partition.number }}
                <q-popup-edit
                  v-model="partition.number"
                  auto-save
                  v-slot="scope"
                  @before-hide="changeSortTask"
                >
                  <q-input v-model="scope.value" dense autofocus counter type="number" @keyup.enter="scope.set" />
                </q-popup-edit>
              </div>
              <div class="q-pr-xs">.</div>
              <div class="">
                {{ partition.text }}
                <q-popup-edit v-model="partition.text" auto-save v-slot="scope">
                  <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
                </q-popup-edit>
              </div>
            </div>
            <div class="row items-center q-pl-sm q-pb-sm">
              <div class="q-pr-sm">
                Докладчик:
              </div>
              <div class="">
                {{ partition.speaker }}
                <q-popup-edit v-model="partition.speaker" auto-save v-slot="scope">
                  <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
                </q-popup-edit>
              </div>
            </div>
            <div>
              <div class="row items-center q-col-gutter-sm">
                <div>
                  ПОСТАВЛЕННЫЕ ЗАДАЧИ:
                </div>
                <div>
                  <q-btn icon="add" flat color="primary" @click="addTask(partition)" />
                </div>
              </div>
              <div v-for="task in partition.tasks" :key="task.number" class="row no-wrap task-line q-col-gutter-sm">
                <div class="text-no-wrap">
                  {{ partition.number }}.{{ task.number }}.
                  <q-popup-edit
                    v-model="task.number"
                    v-slot="scope"
                    @before-hide="changeSortTask"
                  >
                    <q-input v-model="scope.value" type="number" dense autofocus @keyup.enter="scope.set">
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
                <div class="text-no-wrap">
                  {{ task.executor }}
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
                <div>
                  –
                </div>
                <div>
                  <div v-if="!task.data_ispoln" class="text-teal">
                    Тезис
                  </div>
                  <ShowTime v-else :time="task.data_ispoln" format="DD.MM.YYYY" />
                  <q-popup-edit v-model="task.data_ispoln" auto-save v-slot="scope">
                    <InputDate v-model="scope.value" clearable />
                  </q-popup-edit>
                </div>
                <div>
                  –
                </div>
                <div class="" style="flex-grow: 1;">
                  <ShowTaskText :text="task.text" />
                  <q-popup-edit
                    v-model="task.text"
                    v-slot="scope">

                    <q-input v-model="scope.value" dense autofocus autogrow>
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
                <div class="row items-center no-wrap">
                  <SelectExecutor v-model="task.users" multiple dense outlined style="min-width:150px;" options-dense :class="task.users.length === 0 ? 'bg-red-2' : ''" />
                  <div>
                    <q-btn icon="delete" flat color="negative" @click="deleteTask(partition, task)" />
                  </div>
                </div>
              </div>

            </div>
          </q-card-section>
        </q-card>
      </div>

      <q-btn color="primary" :disabled="!setUser" @click="publishProtokol">Опубликовать</q-btn>
    </div>
  </div>
</template>

<script>
import SelectTypeProtocol from 'src/Modules/Protocol/components/QSelectTypeProtocol/index.vue'
import SelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import { parseProtocol, publishProtokol } from 'src/Modules/Protocol/api/protocol.js'
import { date } from 'quasar'
import ShowTime from 'components/ShowTime/index.vue'
import ShowTaskText from './componets/ShowTaskText/index.vue'
import InputDate from 'components/InputDate/index.vue'

export default {
  components: {
    SelectTypeProtocol,
    SelectExecutor,
    ShowTime,
    InputDate,
    ShowTaskText
  },
  directives: {},

  data() {
    return {
      l: [],
      loading: false,
      formExecutor: false,
      file: false,
      parserOk: false,
      parserButton: false,
      text: '',
      infoMessage: 'Проверить протокол и нажать Потвердить',
      temp: {},
      oldProtokol: {},
      protokol: {
        title: 'Протокол',
        type: 1,
        number: '00/00/00',
        date: date.formatDate(new Date(), 'DD MMMM YYYY'),
        region: 'Место проведения',
        president: 'Председатель',
        secretary: 'Секретарь',
        composition: 'Присутствовали',
        partition: [
          {
            number: 1,
            text: 'Тема доклада',
            speaker: 'Докдачик',
            tasks: []
          }
        ]
      }
    }
  },
  computed: {
    setUser: function () {
      let user = false
      if (this.protokol.partition[0]) {
        user = true
        this.protokol.partition.forEach(part => {
          part.tasks.forEach(task => {
            if (task && task.users.length === 0) {
              user = false
            }
          })
        })
      }
      return user
    }
  },
  methods: {
    deleteTask(partition, task) {
      const t_number = partition.number + '.' + task.number + '.'
      this.$q.dialog({
        title: 'Подтверждение',
        message: 'Удалить задачу № ' + t_number + ' ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        const index = partition.tasks.findIndex(item => item === task)
        partition.tasks.splice(index, 1)
        this.changeSortTask()
      })

    },
    addPartition() {
      this.protokol.partition.push({
        number: this.protokol.partition.length + 1,
        text: 'Тема доклада',
        speaker: 'Докдачик',
        task: []
      })
      this.changeSortTask()
    },
    changeSortTask() {
      this.protokol.partition = this.protokol.partition.sort((a, b) => {
        return a.number - b.number
      })
      this.protokol.partition.forEach(part => {
        part.tasks = part.tasks.sort((a, b) => {
          return a.number - b.number
        })
        let i = 1
        part.tasks = part.tasks.map(item => {
          item.number = i++
          return item
        })
      })
    },
    addTask(partition) {
      const n = partition.tasks.length + 1
      partition.tasks.push({
        number: n,
        executor: 'Исполнитель',
        data_ispoln: date.formatDate(new Date(), 'YYYY-MM-DD'),
        text: 'Текст задачи',
        users: []
      })
      this.changeSortTask()
    },
    parseFile() {
      const file = this.$refs.file.files[0]
      if (file) {
        this.loading = true
        const formData = new FormData()
        formData.append('uid', 'this.uid')
        formData.append('file', file)
        parseProtocol(formData)
          .then(res => {
            const type = this.protokol.type
            this.protokol = Object.assign({}, res.data)
            this.protokol.type = type
          })
          .finally(() => {
            this.loading = false
          })
      }
    },
    publishProtokol() {
      const data = new FormData()
      data.append('file', this.$refs.file.files[0])
      data.append('protocol', JSON.stringify(this.protokol))
      publishProtokol(data)
        .then(response => {
          this.$router.push('/protocol/show/' + response.data.id)
        }, error => {
          this.$notify({
            title: 'Ошибка',
            message: error.response.data.message,
            type: 'error',
            duration: 15000
          })
        })
    }
  }
}
</script>
<style scoped lang="scss">
.field-label {
  min-width: 150px;
}

.field {
  color: $teal;
}

.task-line {
  padding: 0 10px;

  &:hover {
    background-color: #f1f1f1;
  }
}
</style>
