<template>
  <div class="q-pb-lg">
    <section class="content">
      <div v-if="access" class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                  <i class="fa fa-tasks" />
                  Задача:
                  <el-button v-if="roles.includes('admin') && !task.arxiv" type="primary" @click="showFormStatus = true">В архив</el-button>
                  <el-button v-if="roles.includes('admin') && !task.arxiv" type="warning" @click="editTask">Edit</el-button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div>
                  <p><b>Протокол:</b> {{ task.protokol.nomer }}</p>
                  <p><b>От:</b>{{ task.protokol.descriptions.date }}</p>
                  <p><b>Доклад:</b> {{ task.partition.speaker }}</p>
                  <p><b>Тема:</b> {{ task.partition.text }}</p>
                </div>
                <div v-if="task.arxiv" class="text-danger" v-html="task.arxiv" />
                <p class="text-primary">
                  {{ task.number }}. {{ task.text }}
                </p>
                <div>
                  Исполнители: {{ task.executor }}
                </div>
                <div v-if="task.data_ispoln" class="li">
                  Выполнить до: {{ task.data_ispoln }}
                </div>
                <div v-else class="li">
                  <el-tag type="success">Тезис</el-tag>
                </div>
                <div v-if="roles.includes('admin') && task.data_ispoln" class="li">
                  <el-button class="tag-item" type="primary" @click="showPerenos = !showPerenos">Перенести</el-button>
                </div>
                <div v-if="showPerenos" class="time-container li">
                  <el-date-picker v-model="time" type="datetime" format="dd-MM-yyyy" placeholder="Укажите дату" />
                  <el-button class="tag-item small" @click="savePerenos">Сохранить</el-button>
                </div>
                <div v-if="task.data_perenosa" class="perenos">
                  Перенесено на : {{ task.data_perenosa }}
                </div>
              </div>
              <div class="card-body">
                Выполнено:
                <span v-for="item in task.executors" :key="item.id">
                  <el-button v-if="accessFilter(item.user)" :type="item.done  ? 'success' : 'danger'" class="tag-item" @click="changeStatus(item)">
                    {{ item.user.name }}
                  </el-button>
                  <el-tag v-else :type="item.done ? 'success' : 'danger'" class="tag-item">
                    {{ item.user.name }}
                  </el-tag>
                </span>
              </div>
            </div>
          </div>
        </div>
        <DropDownBlock hide-label="История" show-label="История">
          <div v-html="task.history" />
        </DropDownBlock>
        <div class="row">
          <section class="col-md-8">
            <!-- DIRECT CHAT -->
            <div class="card direct-chat direct-chat-primary">
              <div class="card-header">
                <div class="card-title">
                  Отчет
                  <el-checkbox v-model="listQuery.showdeleted" class="filter-item" style="margin-left:15px;" @change="getTask">Показать удаленные</el-checkbox>
                </div>
              </div>
              <div class="bg-blue-1 q-pa-md">
                <div v-for="item in task.report" :key="item.id">
                  <ItemReportMessage :item="item" />
                </div>
              </div>
              <SendReportBlock :task-id="$route.params.id" class="bg-blue-2" @reload="getTask" />
            </div>
            <!--/.direct-chat -->
          </section>
        </div>
      </div>
      <div v-else>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title" style="color: #890000">
              <i class="fa fa-tasks" />
              Задачи не существует или у вас нет доступа
            </h3>
          </div>
        </div>
      </div>
    </section>
    <el-dialog v-model="showFormStatus" title="Отправить задачу в Архив???">
      <template #footer>
        <el-button @click="showFormStatus = false">Отмена</el-button>
        <el-button type="danger" @click="statusArchiv">В архив</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { delReport, downloadReport, fetchTask, taskDone, taskToArchiv, transferDate } from 'src/Modules/Task/api/task.js'
import { exportFile } from 'quasar'
import DropDownBlock from 'components/DropDownBlock/index.vue'
import ItemReportMessage from 'src/Modules/Task/components/ItemReportMessage/index.vue'
import SendReportBlock from 'src/Modules/Task/components/SendReportBlock/index.vue'

export default {
  components: {
    DropDownBlock,
    ItemReportMessage,
    SendReportBlock
  },
  data() {
    return {
      access: false,
      showdeleted: false,
      showPerenos: false,
      time: '',
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
        showdeleted: false,
        id: this.$route.params.id
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
    this.getTask()
  },
  methods: {
    chandeShowDeleted() {

    },
    getUrlFile(file) {
      downloadReport(file.hash).then(response => {
        exportFile(file.name, response.data)
      })
    },
    statusArchiv() {
      this.listLoading = true
      taskToArchiv(this.$route.params.id).then(response => {
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
      this.listQuery.id = this.$route.params.id
      fetchTask(this.listQuery)
        .then(res => {

          this.access = true

          this.task = res.data
          if (this.task.data_perenosa) {
            this.time = this.task.data_perenosa
          }
          console.log(this.task)
          console.log(this.access)
          this.listLoading = false
        })
    },
    editTask() {
      this.$router.push('/task/edit/' + this.$route.params.id)
    },
    getUrl(link) {
      return process.env.API + '/..' + link
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
<!--<style scoped src="@/../../node_modules/admin-lte/dist/css/adminlte.css"></style>-->
<!--<style scoped src="@/../../node_modules/font-awesome/css/font-awesome.min.css"></style>-->

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

.history {
  padding: 20px;
  border: rgba(148, 142, 161, 0.47) solid 1px;
}

.perenos {
  color: red;
  padding-top: 20px;
}

.link {
  color: #000089;
}

.link:hover {
  color: #4d4489;
  cursor: pointer;
}

.ispolnp {
  padding-right: 40px;
  padding-top: 9px;
  float: left;
  height: 36px;
}

.deleted {
  color: #9d9d9d !important;
  background: #f2e9ff !important;
  border-color: #f2e9ff !important;
}

.li {
  padding: 10px 0 0 0;
}
</style>
