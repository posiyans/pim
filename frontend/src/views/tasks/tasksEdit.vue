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
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div>
                  <p><b>Протокол:</b> {{ task.protokol.nomer }}</p>
                  <p><b>От:</b> {{ task.protokol.descriptions.date }}</p>
                  <p><b>Доклад:</b> {{ task.partition.speaker }}</p>
                  <p><b>Тема:</b> {{ task.partition.text }}</p>
                </div>
                <h6 v-if="task.arxiv" class="text-danger" v-html="task.arxiv"/>
                <p class="text-primary bg-yellow" >
                  <span>№: </span>
                  <el-input v-model="task.number"/>
                </p>
                <p class="text-primary bg-yellow" >
                  <span>Задача: </span>
                  <el-input v-model="task.text"/>
                </p>
                <p class="text-primary bg-yellow" >
                  <span>Исполнители: </span>
                  <el-input v-model="task.executor"/>
                </p>
                <div class="time-container">
                  <p class="text-primary bg-yellow" >
                    <span>Выполнить до: </span>
                    <el-date-picker v-model="time" type="datetime" format="dd-MM-yyyy" placeholder="Укажите дату"/>
                  </p>
                </div>
                <div class="task-left">
                  <el-drag-select v-model="task.users" class="select" multiple placeholder="Пожалуйста, выберите">
                    <el-option v-for="item in allExecutor" :label="item.label" :value="item.value" :key="item.value" />
                  </el-drag-select>
                </div>
              <!-- /.card-body -->
              </div>
            <!-- /.card -->
            </div>
          <!-- ./col -->
          </div>
        </div>
        <el-button class="btn btn-primary" @click="$router.go(-1)">назад</el-button>
        <el-button v-if="roles.includes('admin') && !task.arxiv" class="btn btn-primary" @click="save">Сохранить </el-button>
      </div>
    </section>
  </div>
</template>

<script>
import { fetchTask, updateTask } from '@/api/task'
import { mapGetters } from 'vuex'
import { fetchExecutors } from '@/api/user'
import ElDragSelect from '@/components/DragSelect'

export default {
  name: 'TaskEdit',
  components: { ElDragSelect },
  directives: { },
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
      time: '',
      allExecutor: [],
      newMessage: {
        text: '',
        file: ''
      },
      fileName: 'Выберите файл',
      task: {
        protokol: {
          descriptions: {}
        },
        partition: {},
        users: [],
        data_ispoln: ''
      },
      test: 3,
      files: ''
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
    this.getExecutors()
  },
  methods: {
    getTask() {
      this.listLoading = true
      fetchTask(this.$route.params.taskId).then(response => {
        this.task = response.data
        this.task = Object.assign({}, this.allExecutor, response.data)
        const users = []
        this.task.executors.forEach((executor) => {
          if (executor.executor === 1) {
            users.push(executor.user_id)
          }
        })
        this.task = Object.assign({}, this.task, { users: users })
        this.time = new Date(this.task.data_ispoln)
        this.listLoading = false
      })
    },
    save() {
      console.log('save')
      this.task.time_ispoln = this.time
      updateTask(this.task).then(response => {
        this.$router.push({ name: 'TaskInfo', params: { taskId: this.$route.params.taskId }})
      })
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
    }
  }
}
</script>
<style scoped src="@/../../node_modules/admin-lte/dist/css/adminlte.css"></style>
<style scoped src="@/../../node_modules/font-awesome/css/font-awesome.min.css"></style>
<style>
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
  .select {
    width: 100%;
  }

</style>
