<template>
  <div class="app-container">
    <div class="filter-container">
      <h3 style="padding: 10px 0px">
        Добавить протокол
      </h3>
      <span>Тип протокола </span>
      <el-select v-model="protokol.type" placeholder="Тип протокола" clearable class="filter-item" style="width: 130px">
        <el-option v-for="item in protokolTypeOptions" :key="item.key" :label="item.display_name" :value="item.key"/>
      </el-select>
      <div class="form-group">
        <label for="InputFile">Добавить фаил</label>
        <div class="input-group">
          <div class="custom-file">
            <input id="InputFile" ref="file" type="file" name="file" class="custom-file-input" @change="parseFile">
            <label class="custom-file-label" for="InputFile"/>
          </div>
        </div>
      </div>
      <el-button v-if="parserButton" type="primary" class="filter-item" icon="el-icon-document" @click="setExecutor">Потвердить</el-button>
    </div>
    <div v-if="parserOk">
      <div class="message">{{ infoMessage }}</div>
      <div>
        <i>{{ protokol.nomer }}</i>
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
              <div :class="task.users | executorFilter" class="task">
                <span>{{ task.number }}.</span> {{ task.executor }}
                <b>–{{ task.data_ispoln }}–</b> {{ task.text }}
              </div>
              <div v-if="formExecutor" class="task-left">
                <el-drag-select v-model="task.users" :class="task.users | selectExecutorFilter" class="select" multiple placeholder="Пожалуйста, выберите">
                  <el-option v-for="item in allExecutor" :label="item.label" :value="item.value" :key="item.value" />
                </el-drag-select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <el-button v-if="setUser" type="danger" @click="publishProtokol">Опубликовать</el-button>
    <br><br><br><br><br><br>
    <el-dialog :visible.sync="dialogEditTask" title="Правка">
      <el-form ref="dataForm" label-position="left" label-width="140px" style="width: 400px; margin-left:50px;">
        <el-form-item label="Задача:">
          <el-input v-model="temp.executor" />
          <el-input v-model="temp.data_ispoln" />
          <el-input v-model="temp.text" type="textarea" rows="6" style="width: 600px;"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="cancelTask">{{ $t('table.cancel') }}</el-button>
        <el-button type="danger" @click="updateTask">Обновить</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import readFileInputEventAsArrayBuffer from './readFileDoc'
import { fetchExecutors } from '@/api/user'
import { publishProtokol } from '@/api/protokol'
import ElDragSelect from '@/components/DragSelect'
import ElPager from 'element-ui/packages/pagination/src/pager'
// import { mammoth } from 'mammoth'
const clonedeep = require('lodash.clonedeep')
var mammoth = require('mammoth')

const protokolTypeOptions = [
  { key: 'psd', display_name: 'Протоколы СД' },
  { key: 'skype', display_name: 'Скайп Протоколы' },
  { key: 'year', display_name: 'Готодовые протоколы' }
]

export default {
  name: 'ProtokolAdd',
  components: { ElPager, ElDragSelect },
  directives: {},
  filters: {
    selectExecutorFilter(users) {
      return users.length === 0 ? 'no-select' : ''
    },
    executorFilter(users) {
      return users.length === 0 ? 'alert' : ''
    }
  },
  data() {
    return {
      l: [],
      dialogEditTask: false,
      formExecutor: false,
      allExecutor: [],
      file: false,
      parserOk: false,
      parserButton: false,
      text: '',
      protokolTypeOptions,
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
    setUser: function() {
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
  created() {
    this.getExecutors()
  },
  methods: {
    strToarray(text) {
      text = text.replace('Председатель Совета Директоров', '')
      const temp = text.split('<ol>')
      const pr = temp[0].split('</p>')
      delete temp[0]
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
      const temp = text.split('ПОСТАВЛЕННЫЕ ЗАДАЧИ')
      let part_i = 0
      const partitions = []
      temp.forEach((p) => {
        if (~p.toLowerCase().indexOf('докладчик')) {
          part_i++
          let v = p.toLowerCase().lastIndexOf('докладчик')
          const speaker = p.substr(v + 10).replace(/<\/?[^>]+>/g, '').trim()
          let text = p.substr(0, v)
          v = text.toLowerCase().lastIndexOf('<li>')
          text = text.substr(v + 4).replace(/<\/?[^>]+>/g, '').trim()
          partitions.push({
            number: part_i,
            speaker: speaker,
            text: text,
            task: this.getTask(temp[part_i], part_i)
          })
        }
      })
      this.protokol.partition = Object.assign({}, this.protokol.partition, partitions)
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
      this.file = this.$refs.file.files[0]
      readFileInputEventAsArrayBuffer(this.file, arrayBuffer => {
        mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
          .then(result => {
            this.text = result.value
            this.strToarray(result.value)
            this.parserOk = true
            this.parserButton = true
          })
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
        this.oldProtokol = clonedeep(this.protokol)
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
    getExecutors() {
      fetchExecutors().then(response => {
        var users = []
        for (const user of response.data.user) {
          var item = {}
          item.value = user.key
          item.label = user.display_name
          users.push(item)
        }
        this.allExecutor = Object.assign({}, this.allExecutor, users)
      })
    },
    publishProtokol() {
      const data = new FormData()
      data.append('file', this.$refs.file.files[0])
      data.append('protokol', JSON.stringify(this.protokol))
      publishProtokol(data).then(response => {
        const protokol = response.data[0]
        this.$router.push({ name: 'ProtokolInfo', params: { protokolId: protokol.id }})
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
  .task{
    width: 70%;
    float: left;
    display:block;
  }
  .task-left{
    float: left;
    display:block;
    width: 30%;
  }
  el-drag-select .task-left{
    width: 100%;
  }
  .task-row{
    width: 100%;
    display:inline-block;
    border-top: #c0c0c0 solid 1px;
    border-bottom: #c0c0c0 solid 1px;
  }
  .alert{
    color: red;
    background-color: rgba(255, 0, 0, 0.05);
  }
  .message{
    color: red;
    background-color: rgba(255, 0, 51, 0.1);
    padding: 20px;
    font-size: 16px;
    display: inline-block;
  }
  .select{
    width: 100%;
  }
  .no-select {
    border: #ff7b9f solid 1px;
  }
</style>
