<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="text-teal text-weight-bold q-pa-lg">
        Добавить протокол
      </div>
      <span>Тип протокола </span>
      <SelectTypeProtocol v-model="protokol.type" clearable />
      <div class="form-group">
        <label for="InputFile">Добавить фаил</label>
        <div class="input-group">
          <div class="custom-file">
            <input id="InputFile" ref="file" type="file" name="file" class="custom-file-input" @change="parseFile">
            <label class="custom-file-label" for="InputFile" />
          </div>
        </div>
      </div>
      <el-button v-if="parserButton" type="primary" class="filter-item" @click="setExecutor">Потвердить</el-button>
    </div>
    <div v-if="loading">
      <q-spinner
        color="primary"
        size="3em"
      />
    </div>
    <div v-if="parserOk">
      <div class="bg-red-2 q-pa-lg text-weight-bold">{{ infoMessage }}</div>
      <div>
        Протокол номер: <i>{{ protokol.nomer }}</i>
      </div>
      <div>
        <b>{{ protokol.title }}</b> от {{ protokol.descriptions.date }}
      </div>
      <div>
        Место проведения: {{ protokol.descriptions.region }}
      </div>
      <div>
        Председатель: {{ protokol.descriptions.president }}
      </div>
      <div :class="protokol.arxiv">
        Секретарь: {{ protokol.descriptions.secretary }}
      </div>
      <div style="padding-bottom: 20px">
        Присутствовали: <b>{{ protokol.descriptions.composition }}</b>
      </div>
      <div v-for="partition in protokol.partition" :key="partition.id" style="padding-bottom: 20px">
        <p><b>{{ partition.number }}. {{ partition.text }}</b></p>
        <p v-if="partition.speaker">
          <i>Докладчик: {{ partition.speaker }}</i>
        </p>
        <div v-if="partition.task">
          <b>ПОСТАВЛЕННЫЕ ЗАДАЧИ:</b>
          <div v-for="task in partition.task" :key="task.number" class="task-text">
            <div class="task-row" @click="editTask(task)">
              <div :class="task.users.length === 0 ? 'bg-reg' : ''" class="task">
                <span>{{ task.number }}.</span> {{ task.executor }}
                <b>–{{ task.data_ispoln }}–</b> {{ task.text }}
              </div>
              <SelectExecutor v-model="task.users" multiple />
            </div>
          </div>
        </div>
      </div>
    </div>
    <el-button v-if="setUser" type="danger" @click="publishProtokol">Опубликовать</el-button>
    <br>
    <el-dialog v-model="dialogEditTask" title="Правка">
      <el-form ref="dataForm" label-position="left" label-width="140px" style="width: 400px; margin-left:50px;">
        <el-form-item label="Задача:">
          <el-input v-model="temp.executor" />
          <el-input v-model="temp.data_ispoln" />
          <el-input v-model="temp.text" type="textarea" rows="6" style="width: 600px;" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="cancelTask">Отмена</el-button>
        <el-button type="danger" @click="updateTask">Обновить</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import SelectTypeProtocol from 'src/Modules/Protocol/components/SelectTypeProtocol/index.vue'
import SelectExecutor from 'src/Modules/User/components/SelectExecutor/index.vue'
import { publishProtokol, uploadProtocol } from 'src/Modules/Protocol/api/protocol.js'


export default {
  components: { SelectTypeProtocol, SelectExecutor },
  directives: {},

  data() {
    return {
      l: [],
      loading: false,
      dialogEditTask: false,
      formExecutor: false,
      file: false,
      parserOk: false,
      parserButton: false,
      text: '',
      infoMessage: 'Проверить протокол и нажать Потвердить',
      temp: {},
      oldProtokol: {},
      protokol: {
        title: '',
        type: 'psd',
        number: '',
        descriptions: {},
        partition: {}
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
    strToarray(text) {
      text = text.replace('Председатель Совета Директоров', '')
      const pr = text.split('\n')
      console.log('pr')
      console.log(pr)

      pr.forEach((p) => {
        const field = p.replace(/<\/?[^>]+>/g, '')
        if (field.length > 0) {
          if (~field.toLowerCase().indexOf('протокол')) {
            this.protokol.title = field.trim()
            this.protokol.nomer = field.split('№')[1].trim()
          } else if (~field.toLowerCase().indexOf('место')) {
            this.protokol.descriptions.region = field.split(':')[1].trim()
          } else if (~field.toLowerCase().indexOf('председатель')) {
            this.protokol.descriptions.president = field.split(':')[1].trim()
          } else if (~field.toLowerCase().indexOf('секретарь')) {
            this.protokol.descriptions.secretary = field.split(':')[1].trim()
          } else if (~field.toLowerCase().indexOf('присутствовали')) {
            this.protokol.descriptions.composition = field.split(':')[1].trim()
          } else {
            this.protokol.descriptions.date = field.trim()
          }
        }
      })
      this.getPartitions(text)
    },
    getPartitions(text) {

      const temp = this.text.split('\n')
      let part_i = 0
      const partitions = []
      let tmp = null
      let findNull = false
      temp.forEach((p, index) => {
        // console.log(p)
        // console.log(index)
        if (~p.toLowerCase().indexOf('докладчик')) {
          console.log('докладчик')
          console.log(p)
          console.log(index)
          // if (tmp) {
          //   partitions.push(tmp)
          // }
          tmp = {
            number: ++part_i,
            speaker: p.split(':')[1].trim(),
            text: temp[index - 1],
            task: []
          }
          console.log(tmp)
          // partitions.push({
          //   number: part_i,
          //   speaker: p.split(':')[1],
          //   text: temp[index - 1],
          //   // task: this.getTask(temp[part_i], part_i)
          // })

        } else if (tmp) {
          const field = p.replace(/<\/?[^>]+>/g, '')
          if (p.replace(/ /g, '').length > 0) {
            if (field.toLowerCase().indexOf('поставленные задачи')) {
              const task = this.parseTask(field)
              const i = tmp.task.length + 1
              task.number = part_i + '.' + i
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
      console.log(partitions)
      this.protokol.partition = Object.assign({}, this.protokol.partition, partitions)
    },
    parseTask(line) {
      let title = ''
      let date = ''
      let executor = ''
      const temptask = line.replace(' - ', ' – ').split('–')
      let successfull = false
      if (temptask.length > 2 && temptask[1].length < 11) {
        executor = temptask[0].replace(/<\/?[^>]+>/g, '').trim()
        date = temptask[1].replace(/<\/?[^>]+>/g, '').trim()
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
          date = ''
        }
      }
      return { text: title, executor: executor, data_ispoln: date, users: [] }
    },
    getTask(text, part) {
      // console.log(text)
      const temp = text.split('<li>')
      let part_i = 0
      const task = []
      temp.forEach((p) => {
        if (p.replace(/<\/?[^>]+>/g, '').trim().length > 10) {
          if (!~p.toLowerCase().indexOf('докладчик')) {
            part_i++
            let title = ''
            let date = ''
            let executor = ''
            const temptask = p.replace(' - ', ' – ').split('–')
            let successfull = false
            if (temptask.length > 2 && temptask[1].length < 11) {
              executor = temptask[0].replace(/<\/?[^>]+>/g, '').trim()
              date = temptask[1].replace(/<\/?[^>]+>/g, '').trim()
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
                date = ''
              }
            }
            task.push({ text: title, executor: executor, data_ispoln: date, users: [], number: part + '.' + part_i })
          }
        }
      })
      return Object.assign({}, task)
    },
    parseFile() {
      const file = this.$refs.file.files[0]
      console.log(file)
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
            this.protokol.nomer = field.split('№')[1].trim()
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
    setExecutor() {
      this.infoMessage = 'Укажите исполнителей для задач'
      this.parserButton = false
      this.formExecutor = true
    },
    editTask(task) {
      if (!this.formExecutor) {
        this.temp = task
        this.oldProtokol = this.protokol
        this.dialogEditTask = true
      }
    },
    updateTask() {
      // this.protokol = Object.assign({}, this.protokol, this.temp)
      this.dialogEditTask = false
    },
    cancelTask() {
      this.$nextTick(() => {
        this.protokol = Object.assign({}, this.oldProtokol)
      })
      this.dialogEditTask = false
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
<style scoped>
.task {
  width: 70%;
  float: left;
  display: block;
}

.task-left {
  float: left;
  display: block;
  width: 30%;
}

el-drag-select .task-left {
  width: 100%;
}

.task-row {
  width: 100%;
  display: inline-block;
  border-top: #c0c0c0 solid 1px;
  border-bottom: #c0c0c0 solid 1px;
}

.alert {
  color: red;
  background-color: rgba(255, 0, 0, 0.05);
}

.message {
  color: red;
  background-color: rgba(255, 0, 51, 0.1);
  padding: 20px;
  font-size: 16px;
  display: inline-block;
}

.select {
  width: 100%;
}

.no-select {
  border: #ff7b9f solid 1px;
}
</style>
