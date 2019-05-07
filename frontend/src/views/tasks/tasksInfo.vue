<template>
  <div>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fa fa-tasks"/>
                  Задача:
                  <el-button v-if="roles.includes('admin') && !task.arxiv" class="btn btn-primary" @click="showFormStatus = true">В архив </el-button>
                  <el-button v-if="roles.includes('admin') && !task.arxiv" class="btn btn-warning" @click="editTask">Edit </el-button>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <dl>
                  <dt>Протокол:</dt> <dd>{{ task.protokol.nomer }}</dd>
                  <dt>От:</dt><dd>{{ task.protokol.descriptions.date }}</dd>
                  <dt>Доклад:</dt><dd> {{ task.partition.speaker }}</dd>
                  <dt>Тема:</dt><dd> {{ task.partition.text }}</dd>
                </dl>
                <h6 v-if="task.arxiv" class="text-danger" v-html="task.arxiv"/>
                <p class="text-primary" >
                  {{ task.number }}. {{ task.text }}
                </p>
                <div class="li">
                  <p>
                    Исполнители: {{ task.executor }}
                  </p>
                </div>
                <div v-if="task.data_ispoln" class="ispolnp">
                  Выполнить до: {{ task.data_ispoln | formatDate }}
                </div>
                <div v-else class="ispolnp">
                  <el-tag type="success">Тезис</el-tag>
                </div>
                <div v-if="task.data_ispoln">
                  <el-button class="tag-item" @click="showPerenos = !showPerenos">Перенести</el-button>
                </div>
                <div v-if="showPerenos" class="time-container">
                  <el-date-picker v-model="time" type="datetime" format="dd-MM-yyyy" placeholder="Укажите дату"/>
                  <el-button class="tag-item small" @click="savePerenos">Сохранить</el-button>
                </div>
                <div v-if="task.data_perenosa" class="perenos">
                  Перенесено на : {{ task.data_perenosa | formatDate }}
                </div>
              </div>
              <div class="card-body">
                Выполнено:
                <span v-for="item in task.executors" :key="item.id">
                  <el-button v-if="accessFilter(item.user)" :type="item.done | statusFilter" class="tag-item" @click="changeStatus(item)">
                    {{ item.user.name }}
                  </el-button>
                  <el-tag v-else :type="item.done | statusFilter" class="tag-item">
                    {{ item.user.name }}
                  </el-tag>
                </span>
              </div>
              <!-- /.card-body -->
            <!-- /.card -->
            </div>
          <!-- ./col -->
          </div>
        </div>
        <div class="row">
          <section class="col-md-8">
            <div class="history" @click="historyShow = !historyShow">
              История
              <div v-if="historyShow" v-html="task.history"/>
            </div>
          </section>
        </div>
        <div class="row">
          <section class="col-md-8">
            <!-- DIRECT CHAT -->
            <div class="card direct-chat direct-chat-primary">
              <div class="card-header">
                <h3 class="card-title">
                  Отчет
                  <el-checkbox v-model="listQuery.showdeleted" class="filter-item" style="margin-left:15px;" @change="getTask">Показать удаленные</el-checkbox>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages" style="height: 100%">
                  <!-- Message. Default to the left -->
                  <div v-for="item in task.report" :key="item.id" :class="{ right: item.user.id === userId }" class="direct-chat-msg">
                    <div class="direct-chat-info clearfix">
                      <span :class="[ item.user.id === userId ? 'pull-right' : 'pull-left' ]" class="direct-chat-name">{{ item.user.name }}
                        <span v-if="item.deleted_at" class="perenos">Удалено</span>
                      </span>
                      <span :class="[ item.user.id === userId ? 'pull-left' : 'pull-right' ]" class="direct-chat-timestamp">{{ item.created_at }}</span>
                    </div>
                    <!-- /.direct-chat-info -->
                    <img :src="getUrl('/user/avatar/' + item.user.id)" :alt="item.user.name" :title="accessFilter(item.user) ? 'Удалить Отчет' : '' " class="direct-chat-img" @click="removeReport(item)">
                    <!-- /.direct-chat-img -->
                    <div :class="item.deleted_at | deletedFilter" class="direct-chat-text">
                      {{ item.text }}
                      <div v-if="item.file.length > 0" class="link">
                        <span target="_blank" @click="getUrlFile(item.file[0])">Фаил: {{ item.file[0].name }}</span>
                      </div>
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->
                </div>
                <!--/.direct-chat-messages-->
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <div>
                  <div class="form-group">
                    <label for="textTask">Добавить отчет</label>
                    <div class="input-group">
                      <input id="textTask" v-model="newMessage.text" type="text" name="message" placeholder="Текст отчета..." class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="InputFile">Добавить фаил</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input id="InputFile" ref="file" type="file" name="file" class="custom-file-input" @change="selectFile">
                        <label class="custom-file-label" for="InputFile">{{ fileName }}</label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-append">
                        <button type="button" class="btn btn-primary" @click="AddReport">Отправить</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-footer-->
            </div>
            <!--/.direct-chat -->
          </section>
        </div>
        <el-button class="btn btn-primary" @click="$router.go(-1)">назад</el-button>
      </div>
    </section>
    <el-dialog :visible.sync="showFormStatus" title="Отправить задачу в Архив???">
      <div slot="footer" class="dialog-footer">
        <el-button @click="showFormStatus = false">{{ $t('table.cancel') }}</el-button>
        <el-button type="danger" @click="statusArchiv">В архив</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { fetchTask, addReport, delReport, taskDone, taskToArchiv, downloadReport, transferDate } from '@/api/task'
import { mapGetters } from 'vuex'
import { saveAs } from 'file-saver'
import moment from 'moment'

export default {
  name: 'TaskInfo',
  components: { },
  directives: { },
  filters: {
    deletedFilter(deleted) {
      if (deleted) {
        return 'deleted'
      } else {
        return ''
      }
    },
    statusFilter(status) {
      if (status) {
        return 'success'
      } else {
        return 'danger'
      }
    },
    formatDate(value) {
      if (value) {
        return moment(String(value)).format('DD-MM-YYYY')
      }
    }
  },
  data() {
    return {
      showdeleted: false,
      historyShow: false,
      showPerenos: false,
      newMessage: {
        text: '',
        file: ''
      },
      time: '',
      fileName: 'Выберите файл',
      task: {
        protokol: {
          descriptions: {}
        },
        partition: {}
      },
      showFormStatus: false,
      test: 3,
      files: '',
      listQuery: {
        showdeleted: false
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
  watch: {
  },
  created() {
    this.getTask()
  },
  methods: {
    chandeShowDeleted() {

    },
    getUrlFile(file) {
      downloadReport(file.hash).then(response => {
        saveAs(new Blob([response.data], {
          type: response.data.type
        }), file.name)
      })
    },
    statusArchiv() {
      this.listLoading = true
      taskToArchiv(this.$route.params.taskId).then(response => {
        // this.task = response.data
        this.listLoading = false
        this.getTask()
      })
      this.showFormStatus = false
    },
    accessFilter(user) {
      var access = false
      if (user.id === this.userId) {
        access = true
      }
      if (this.roles.includes('admin')) {
        access = true
      }
      return access
    },
    removeReport(report) {
      if (report.user_id === this.userId || this.roles.includes('admin')) {
        let isRemote = confirm('Удалить отчет????')
        if (isRemote) {
          delReport(report.id).then(response => {
            this.listLoading = false
            this.$message({
              message: 'Ok',
              type: 'success',
              showClose: true,
              duration: 5000
            })
            this.getTask()
          })
        }
        isRemote = false
      }
    },
    selectFile(event) {
      this.$message({
        message: 'file',
        type: 'success',
        showClose: true,
        duration: 1000
      })
      this.fileName = event.target.files[0].name
      this.newMessage.file = event.target.files[0]
    },
    AddReport() {
      if (this.newMessage.text !== '' || this.$refs.file.files[0].name) {
        const formData = new FormData()
        formData.append('file', this.$refs.file.files[0])
        formData.append('message', this.newMessage.text)
        formData.append('task', this.$route.params.taskId)
        addReport(formData).then(response => {
          this.listLoading = false
          this.getTask()
          this.$message({
            message: 'Ok',
            type: 'success',
            showClose: true,
            duration: 3000
          })
        })
        this.newMessage.text = ''
        this.newMessage.file = ''
        this.fileName = 'Выберите файл'
        this.$refs.file.value = ''
      }
    },
    changeStatus(executor) {
      if (executor.user_id === this.userId || this.roles.includes('admin')) {
        if (executor.done) {
          executor.done = null
        } else {
          executor.done = 'ok'
        }
        taskDone(executor).then(response => {
          this.$message({
            message: 'Ok',
            type: 'success',
            showClose: true,
            duration: 1000
          })
          this.getTask()
        })
      }
    },
    getTask() {
      this.listLoading = true
      fetchTask(this.$route.params.taskId, this.listQuery).then(response => {
        this.task = response.data
        if (this.task.data_perenosa) {
          this.time = this.task.data_perenosa
        }
        this.listLoading = false
      })
    },
    editTask() {
      this.$router.push({ name: 'TaskEdit', params: { taskId: this.$route.params.taskId }})
    },
    getUrl(link) {
      return process.env.BASE_API + '/..' + link
    },
    savePerenos() {
      this.task.time_transfer = this.time
      transferDate(this.task).then(responce => {
        console.log(responce.date)
        this.getTask()
      })
      this.showPerenos = false
    }
  }
}
</script>
<style scoped src="@/../../node_modules/admin-lte/dist/css/adminlte.css"></style>
<style scoped src="@/../../node_modules/font-awesome/css/font-awesome.min.css"></style>

<style scoped>
  #myVueDropzone .dz-filename {
    padding: 10px;
  }
  #myVueDropzone .dz-error {
    color: red;
    padding: 10px;
  }
  #myVueDropzone .dz-remove {
    color: red;
    padding: 10px;
  }
  .history{
    padding: 20px;
    border: rgba(148, 142, 161, 0.47) solid 1px;
  }
  .perenos{
    color: red;
    padding-top: 20px;
  }
  .link{
    color: #00f7ff;
  }
  .link:hover{
    color: #81fff2;
    cursor: pointer;
  }
  .ispolnp{
    padding-right: 40px;
    padding-top: 9px;
    float: left;
    height: 36px;
  }
  .deleted{
    color: #9d9d9d !important;
    background: #f2e9ff !important;
    border-color: #f2e9ff !important;
  }
</style>
