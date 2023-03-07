<template>
  <div class="app-container">
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
        <label for="InputFile">Загрузить из файла</label>
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
              {{ protokol.descriptions.date }}
              <q-popup-edit v-model="protokol.descriptions.date" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Место проведения:
            </div>
            <div class="field">
              {{ protokol.descriptions.region }}
              <q-popup-edit v-model="protokol.descriptions.region" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Председатель:
            </div>
            <div class="field">
              {{ protokol.descriptions.president }}
              <q-popup-edit v-model="protokol.descriptions.president" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Секретарь:
            </div>
            <div class="field">
              {{ protokol.descriptions.secretary }}
              <q-popup-edit v-model="protokol.descriptions.secretary" auto-save v-slot="scope">
                <q-input v-model="scope.value" dense autofocus counter @keyup.enter="scope.set" />
              </q-popup-edit>
            </div>
          </div>
          <div class="row items-center">
            <div class="field-label">
              Присутствовали:
            </div>
            <div class="field">
              {{ protokol.descriptions.composition }}
              <q-popup-edit v-model="protokol.descriptions.composition" auto-save v-slot="scope">
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
              <div v-for="task in partition.task" :key="task.number" class="row items-center justify-between no-wrap">
                <div :class="task.users.length === 0 ? 'text-red-10' : ''" class="row q-col-gutter-xs no-wrap">
                  <div>
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
                  <div>
                    {{ task.executor }}
                    <q-popup-edit v-model="task.executor" auto-save v-slot="scope">
                      <q-input v-model="scope.value" dense autofocus counter autogrow @keyup.enter="scope.set" />
                    </q-popup-edit>
                  </div>
                  <div>
                    –
                  </div>
                  <div>
                    <ShowTime :time="task.data_ispoln" format="DD.MM.YYYY" />
                    <q-popup-edit v-model="task.data_ispoln" auto-save v-slot="scope">
                      <InputDate v-model="scope.value" />
                    </q-popup-edit>
                  </div>
                  <div>
                    –
                  </div>
                  <div>
                    <ShowTaskText :text="task.text" />
                    <q-popup-edit v-model="task.text" auto-save v-slot="scope">
                      <q-input v-model="scope.value" dense autofocus counter autogrow @keyup.enter="scope.set" />
                    </q-popup-edit>
                  </div>
                </div>
                <div style="min-width:150px;">
                  <SelectExecutor v-model="task.users" multiple dense outlined />
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
            task: []
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
        const p = this.protokol.partition
        Object.keys(p).forEach(key => {
          const partition = p[key].task
          Object.keys(partition).forEach(taskkey => {
            const task = partition[taskkey]
            if (task.users.length === 0) {
              user = false
            }
          })
        })
      }
      return user
    }
  },
  methods: {
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
        part.taks = part.task.sort((a, b) => {
          return a.number - b.number
        })
      })
    },
    addTask(partition) {
      const n = partition.task.length + 1
      partition.task.push({
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
        if (temptask[1].length > 11) {
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
            this.text = res.data
            this.parserOk = true
            this.parseHeader()
            this.getPartitions()
            this.parserButton = true
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
      data.append('protokol', JSON.stringify(this.protokol))
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
</style>
