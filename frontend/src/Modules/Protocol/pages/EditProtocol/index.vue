<template>
  <div>
    <div class="row items-center q-col-gutter-sm">
      <div class="q-mr-lg">
        <el-button class="filter-item" style="margin-left: 10px;" type="primary" @click="exportPorotokol()">Скачать</el-button>
      </div>
      <MoveProtocolToArchiveBtn v-if="!protokol.arxiv" :protocol-id="protokol.id" @reload="getProtokolInfo" />
    </div>
    <h4 v-if="protokol.arxiv" class="alert" v-html="protokol.arxiv" />
    <el-row :gutter="32">
      <el-col :xs="24" :sm="24" :lg="12">
        <div class="chart-wrapper">
          <table>
            <tr>
              <td colspan="2"><input v-model="protokol.title" style="width:500px"></td>
              <td>от <input v-model="protokol.descriptions.date"></td>
            </tr>
            <tr>
              <td>Место проведения:</td>
              <td><input v-model="protokol.descriptions.region"></td>
            </tr>
            <tr>
              <td>Председатель:</td>
              <td><input v-model="protokol.descriptions.president"></td>
            </tr>
            <tr>
              <td>Секретарь:</td>
              <td><input v-model="protokol.descriptions.secretary"></td>
            </tr>
            <tr>
              <td>Присутствовали:</td>
              <td><input v-model="protokol.descriptions.composition"></td>
            </tr>
            <tr>
              <td>Тип протокола:</td>
              <td>
                <SelectTypeProtocol v-model="protokol.type" clearable />
              </td>
            </tr>
          </table>
        </div>
      </el-col>
    </el-row>
    <div v-for="partition in protokol.partition" :key="partition.id" style="padding-bottom: 5px">
      <p><input v-model="partition.number" style="width:40px">. <input v-model="partition.text" style="width:800px">
        <span v-if="partition.file_name" class="link" @click="getLink(partition)">
          <svg-icon icon-class="link" /> скачать
        </span>
        <span v-if="false" @click="showUploadFileForm(partition)">
          <svg-icon icon-class="link" /> Добавить отчет
        </span>
      </p>
      <p v-if="partition.speaker">
        <i>Докладчик:</i><input v-model="partition.speaker" style="width:240px">
      </p>
    </div>

    <el-button class="btn btn-primary" @click="$router.go(-1)">назад</el-button>
    <el-button class="filter-item" style="margin-left: 10px;" type="primary" @click="saveProtokol">Сохранить</el-button>
    <el-dialog v-model="showFormUpload" title="Загрузить отчет">
      // отключено так как недоделано
      <div class="dialog-body">
        <input id="InputFile" ref="file" type="file" name="file" class="custom-file-input" @change="selectFile">
      </div>
      <template #footer>
        <el-button @click="showFormUpload = false">Отмена</el-button>
        <el-button type="primary" @click="UploadFile">Загрузить</el-button>
      </template>
    </el-dialog>


  </div>
</template>

<script>
import { downloadProtocol, fetchProtokol, protokolToArchiv, updateProtokol, uploadPartitionFile } from 'src/Modules/Protocol/api/protocol.js'
import SelectTypeProtocol from 'src/Modules/Protocol/components/SelectTypeProtocol/index.vue'
import MoveProtocolToArchiveBtn from 'src/Modules/Protocol/components/MoveProtocolToArchiveBtn/index.vue'
import { exportFile } from 'quasar'

export default {
  components: {
    SelectTypeProtocol,
    MoveProtocolToArchiveBtn
  },
  data() {
    return {
      data: {},
      showFormUpload: false,
      fileUploadTo: false,
      protokol: {
        id: '',
        title: '',
        descriptions: {}
      }
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
  mounted() {
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
      protokolToArchiv(this.$route.params.id).then(response => {
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
      const data = {
        id: this.$route.params.id
      }
      fetchProtokol(data)
        .then(response => {
          this.protokol = response.data
        })
    },
    saveProtokol() {
      updateProtokol(this.protokol)
        .then(response => {
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
      downloadProtocol(this.$route.params.id)
        .then(response => {
          exportFile(this.protokol.nomer + '.docx', response.data)
        })
        .catch(er => {
          this.$message({
            message: 'Файл не найден',
            type: 'error',
            showClose: true,
            duration: 3000
          })
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
