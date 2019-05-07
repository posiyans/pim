<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-download" @click="exportPorotokol()">Скачать</el-button>
      <span class="edit-button">
        <el-button v-if="roles.includes('admin') && !protokol.arxiv" class="filter-item" type="danger" @click="showFormStatus = true" >В архив </el-button>
      </span>
    </div>
    <h4 v-if="protokol.arxiv" class="alert" v-html="protokol.arxiv"/>
    <el-row :gutter="32">
      <el-col :xs="24" :sm="24" :lg="12">
        <div class="chart-wrapper">
          <table>
            <tr><td colspan="2"><input v-model="protokol.title" style="width:500px"></td></tr>
            <tr><td>_</td><td><input v-model="protokol.descriptions.date"></td></tr>
            <tr><td>Место проведения:</td><td><input v-model="protokol.descriptions.region"></td></tr>
            <tr><td>Председатель:</td><td><input v-model="protokol.descriptions.president"></td></tr>
            <tr><td>Секретарь:</td><td><input v-model="protokol.descriptions.secretary"></td></tr>
            <tr><td>Присутствовали:</td><td><input v-model="protokol.descriptions.composition"></td></tr>
            <tr><td>Тип протокола:</td><td>
              <el-select v-model="protokol.type" placeholder="Тип протокола" clearable class="filter-item" style="width: 130px">
                <el-option v-for="item in protokolTypeOptions" :key="item.key" :label="item.display_name" :value="item.key"/>
              </el-select>
            </td></tr>
          </table>
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="12">
        <div class="chart-wrapper">
          <div>
            <b>{{ protokol.title }}</b> {{ protokol.descriptions.date }}
          </div>
          <div>
            Место проведения: {{ protokol.descriptions.region }}
          </div>
          <div>
            Председатель: {{ protokol.descriptions.president }}
          </div>
          <div>
            Секретарь: {{ protokol.descriptions.secretary }}
          </div>
          <div :class="protokol.arxiv | ArxivFilter" style="padding-bottom: 20px">
            Присутствовали: <b>{{ protokol.descriptions.composition }}</b>
          </div>
        </div>
      </el-col>
    </el-row>
    <div v-for="partition in protokol.partition" :key="partition.id" style="padding-bottom: 5px">
      <p><input v-model="partition.number" style="width:40px">. <input v-model="partition.text" style="width:800px">
        <span v-if="partition.file_name" class="link" @click="getLink(partition)">
          <svg-icon icon-class="link"/> скачать
        </span>
        <span class="link" @click="showUploadFileForm(partition)">
          <svg-icon icon-class="link" /> Добавить отчет
        </span>
      </p>
      <p v-if="partition.speaker">
        <i>Докладчик:</i><input v-model="partition.speaker" style="width:240px">
      </p>
    </div>

    <el-button class="btn btn-primary" @click="$router.go(-1)">назад</el-button>
    <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-message" @click="saveProtokol()">Сохранить</el-button>

    <el-dialog :visible.sync="showFormUpload" title="Загрузить отчет">
      <div class="dialog-body">
        <input id="InputFile" ref="file" type="file" name="file" class="custom-file-input" @change="selectFile">
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="showFormUpload = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="primary" @click="UploadFile">Загрузить</el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="showEditForm" title="Редактировать протокол" class="dialog">
      <div slot="footer" class="dialog-footer">
        <el-button @click="showFormStatus = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="danger" @click="$router.push({ name: 'ProtokolEdit', ProtokolId: $route.params.protokolId })">{{ $t('table.ok') }}</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { fetchProtokol, downloadProtokol, protokolToArchiv, updateProtokol, uploadPartitionFile } from '@/api/protokol'
import { saveAs } from 'file-saver'
import { mapGetters } from 'vuex'
import ElDragSelect from '@/components/DragSelect'

const protokolTypeOptions = [
  { key: 'psd', display_name: 'Протоколы СД' },
  { key: 'skype', display_name: 'Скайп Протоколы' },
  { key: 'year', display_name: 'Готодовые протоколы' }
]

export default {
  name: 'ComplexTable',
  components: { ElDragSelect },
  filters: {
    ArxivFilter(arxiv) {
      if (arxiv) {
        return 'arxiv'
      }
      return ''
    },
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      data: {},
      protokolTypeOptions,
      showFormUpload: false,
      showEditForm: false,
      fileUploadTo: false,
      protokol: {
        title: '',
        descriptions: {}
      }
    }
  },
  computed: {
    ...mapGetters([
      'userId',
      'name',
      'avatar',
      'device',
      'roles'
    ])
  },
  created() {
    this.getProtokolInfo()
  },
  methods: {
    getLink(partition) {
      downloadProtokol(partition.file_hash).then(response => {
        saveAs(new Blob([response.data], {
          type: response.data.type
        }), partition.file_name)
      })
    },
    statusArchiv() {
      this.listLoading = true
      protokolToArchiv(this.$route.params.protokolId).then(response => {
        console.log(response.data)
        this.listLoading = false
        this.getProtokolInfo()
      })
      this.showFormStatus = false
    },
    done(task) {
      // console.log(task.view_report)
      const report = task.view_report
      let done = 0
      let not_performed = 0
      for (const item in report) {
        if (report[item].executor !== 0) {
          if (report[item].done == null) {
            not_performed++
          } else {
            done++
          }
        }
      }
      const total = done / (done + not_performed)
      return total === 1 ? 'success' : 'alert'
    },
    getProtokolInfo() {
      fetchProtokol(this.$route.params.protokolId).then(response => {
        this.protokol = response.data.protokol
      })
    },
    saveProtokol() {
      console.log('save')
      updateProtokol(this.protokol).then(response => {
        this.$message({
          message: 'Ok',
          type: 'success',
          showClose: true,
          duration: 1000
        })
        this.getProtokolInfo()
      })
    },
    showUploadFileForm(partition) {
      console.log('upload')
      this.showFormUpload = true
      this.fileUploadTo = partition.id
      console.log(partition.id)
    },
    UploadFile() {
      if (this.fileUploadTo) {
        console.log('file')
        if (this.$refs.file.files[0].name) {
          const formData = new FormData()
          formData.append('file', this.$refs.file.files[0])
          formData.append('partition', this.fileUploadTo)
          formData.append('type', 'uploadPartitionFile')
          uploadPartitionFile(this.fileUploadTo, formData).then(response => {
            this.showFormUpload = false
            this.$message({
              message: 'Ok',
              type: 'success',
              showClose: true,
              duration: 3000
            })
            this.getProtokolInfo()
          })
          this.$refs.file.value = ''
        }
        this.fileUploadTo = false
      }
    },
    selectFile(event) {
      this.$message({
        message: event.target.files[0].name,
        type: 'success',
        showClose: true,
        duration: 1000
      })
      // this.fileName = event.target.files[0].name
      // this.newMessage.file = event.target.files[0]
    },
    exportPorotokol() {
      downloadProtokol(this.$route.params.protokolId).then(response => {
        saveAs(new Blob([response.data], {
          type: response.data.type
        }), this.protokol.file_name)
      })
    }
  }
}
</script>

<style scoped>
  .task-text {
    padding: 1px;
    font-style: italic;
  }

  .alert {
    color: red;
  }
  .arxiv {
    color: #6e0000;
  }
  .success {
    color: darkgreen;
  }
  .link{
    color: #000061;
    padding: 0 15px;
  }
  .link:hover{
    color: #0000ff;
    text-decoration: underline;
    cursor: pointer;
  }
  .edit-button{
    padding-left: 200px;
  }
  .dialog{
    background-color: #ffeaea;
    color: green;
  }
</style>
