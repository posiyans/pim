<template>
  <div class="">
    <div class="row items-center q-col-gutter-sm q-pb-md">
      <div class="text-teal text-weight-bold">
        Добавить протокол
      </div>
      <div style="min-width: 150px;">
        <SelectTypeProtocol
          v-model="protokol.type"
          outlined
          dense
        />
      </div>

      <div class="form-group">
        <label>Загрузить из файла</label>
        <div class="input-group">
          <div class="custom-file">
            <input ref="file" type="file" name="file"
                   @change="parseFile">
          </div>
        </div>
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
                    auto-save
                    v-slot="scope"
                    @before-hide="changeSortTask"
                  >
                    <q-input v-model="scope.value" type="number" dense autofocus counter @keyup.enter="scope.set" />
                  </q-popup-edit>
                </div>
                <div class="text-no-wrap">
                  {{ task.executor }}
                  <q-popup-edit v-model="task.executor" auto-save v-slot="scope">
                    <q-input v-model="scope.value" dense autofocus counter autogrow @keyup.enter="scope.set" />
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
                  <q-popup-edit v-model="task.text" auto-save v-slot="scope">

                    <q-input v-model="scope.value" dense autofocus counter autogrow>
                      <template v-slot:after>
                        <q-btn round dense color="primary" flat icon="save" @click="scope.set" />
                      </template>
                    </q-input>
                  </q-popup-edit>
                </div>
                <div class="row items-center no-wrap">
                  <SelectExecutor v-model="task.users" multiple dense outlined style="min-width:150px;" :class="task.users.length === 0 ? 'bg-red-2' : ''" />
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
import { publishProtokol, uploadProtocol } from 'src/Modules/Protocol/api/protocol.js'
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
        title: 'Протокол планового заседания',
        type: 'psd',
        number: '00/00/00',
        descriptions: {
          date: date.formatDate(new Date(), 'DD MMMM YYYY'),
          region: 'Место проведения',
          president: 'Председатель',
          secretary: 'Секретарь',
          composition: 'Присутствовали'
        },
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
      console.log('changesrot')
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
    getPartitions() {
      const temp = this.text.split('\n')
      let part_i = 0
      const partitions = []
      let tmp = null
      let findNull = false
      temp.forEach((p, index) => {
        // console.log(p)
        // console.log(index)
        if (~p.toLowerCase().indexOf('докладчик')) {
          tmp = {
            number: ++part_i,
            speaker: p.split(':')[1].trim(),
            text: temp[index - 1],
            task: []
          }
        } else if (tmp) {
          const field = p.replace(/<\/?[^>]+>/g, '')
          if (p.replace(/ /g, '').length > 0) {
            if (field.toLowerCase().indexOf('поставленные задачи')) {
              const task = this.parseTask(field)
              const i = tmp.task.length + 1
              task.number = i
              tmp.task.push(task)
            } else {
              findNull = true
            }
          } else if (findNull) {
            partitions.push(tmp)
            tmp = null
            findNull = false
          }
        }
      })
      if (tmp) {
        partitions.push(tmp)
      }
      this.protokol.partition = Object.assign({}, this.protokol.partition, partitions)
    },
    parseTask(line) {
      let title = ''
      let date_ispoln = ''
      let executor = ''
      const temptask = line.replace(' - ', ' – ').split('–')
      let successfull = false
      if (temptask.length > 2 && temptask[1].length < 11) {
        executor = temptask[0].replace(/<\/?[^>]+>/g, '').trim()
        const temp_d = temptask[1].replace(/<\/?[^>]+>/g, '').trim().split('.');
        if (temp_d.length === 2) {
          date_ispoln = temptask[1].replace(/<\/?[^>]+>/g, '').trim()
          const nowMonth = date.formatDate(new Date(), 'M')
          let year = date.formatDate(new Date(), 'YYYY')
          if (nowMonth > +temp_d[1]) {
            year++
          }
          date_ispoln = date.formatDate(new Date(year, temp_d[1] - 1, temp_d[0]), 'YYYY-MM-DD')
        }
        temptask.shift()
        temptask.shift()
        title = temptask.join('–').replace(/<\/?[^>]+>/g, '').trim()
        successfull = true
      }
      if (!successfull) {
        if (temptask[1] && temptask[1].length > 11) {
          executor = temptask[0].replace(/<\/?[^>]+>/g, '').trim()
          temptask.shift()
          title = temptask.join('–').replace(/<\/?[^>]+>/g, '').trim()
          date_ispoln = ''
        }
      }
      return { text: title, executor: executor, data_ispoln: date_ispoln, users: [] }
    },
    parseFile() {
      const file = this.$refs.file.files[0]
      if (file) {
        this.loading = true
        const formData = new FormData()
        formData.append('uid', 'this.uid')
        formData.append('file', file)
        uploadProtocol(formData)
          .then(res => {
            const type = this.protokol.type
            this.protokol = Object.assign({}, res.data)
            this.protokol.type = type
            // this.text = res.data
            // this.parserOk = true
            // this.parseHeader()
            // this.getPartitions()
            // this.parserButton = true
          })
          .finally(() => {
            this.loading = false
          })
      }
    },
    parseHeader() {
      const text = this.text.replace('Председатель Совета Директоров', '')
      const line = text.split('\n')
      line.forEach((item, index) => {
        const field = item.replace(/<\/?[^>]+>/g, '')
        if (field.length > 0) {
          if (~field.toLowerCase().indexOf('протокол')) {
            this.protokol.title = field.trim()
            this.protokol.number = field.split('№')[1].trim()
          } else if (~field.toLowerCase().indexOf('место')) {
            this.protokol.descriptions.region = field.split(':')[1].trim()
          } else if (~field.toLowerCase().indexOf('председатель')) {
            this.protokol.descriptions.president = field.split(':')[1].trim()
          } else if (~field.toLowerCase().indexOf('секретарь')) {
            this.protokol.descriptions.secretary = field.split(':')[1].trim()
          } else if (~field.toLowerCase().indexOf('присутствовали')) {
            this.protokol.descriptions.composition = field.split(':')[1].trim()
          } else if (index === 3) {
            this.protokol.descriptions.date = field.trim()
          }
        }
      })
    },
    publishProtokol() {
      const data = new FormData()
      data.append('file', this.$refs.file.files[0])
      data.append('protocol', JSON.stringify(this.protokol))
      publishProtokol(data)
        .then(response => {
          const protokol = response.data[0]
          this.$router.push('/protocol/show/' + protokol.id)
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
