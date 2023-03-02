<template>
  <div>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <div class="card-title">
                  <i class="fa fa-tasks" />
                  Задача:
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div>
                  <div><b>Протокол:</b> {{ task.protokol.nomer }}</div>
                  <div><b>От:</b> {{ task.protokol.descriptions.date }}</div>
                  <div><b>Доклад:</b> {{ task.partition.speaker }}</div>
                  <divs><b>Тема:</b> {{ task.partition.text }}</divs>
                </div>
                <h6 v-if="task.arxiv" class="text-danger" v-html="task.arxiv" />
                <div class="q-mb-md">
                  <q-input v-model="task.number" outlined label="№" />
                </div>
                <div class="q-mb-md">
                  <div class="q-pa-sm text-weight-bolder">Задача:</div>
                  <q-editor v-model="task.text" min-height="5rem" label="dsada" />
                </div>
                <div class="q-mb-md">
                  <q-input v-model="task.executor" outlined label="Исполнители" />
                </div>
                <div class="q-mb-md">
                  <InputDate v-model="task.data_ispoln" placeholder="Укажите дату" label="Выполнить до" outlined clearable />
                </div>
                <div class="q-mb-md">
                  <QSelectExecutor
                    v-model="executors"
                    multiple
                    outlined
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <q-btn flat color="negative" label="Отмена" :to="'/task/show/' + task.id" />
        <q-btn color="primary" label="Сохранить" @click="save" />
      </div>
    </section>
  </div>
</template>

<script>
import { fetchTask, updateTask } from 'src/Modules/Task/api/task.js'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import InputDate from 'components/InputDate/index.vue'

export default {
  name: 'TaskEdit',
  components: {
    QSelectExecutor,
    InputDate
  },
  directives: {},
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
      listQuery: {
        showdeleted: false,
        id: this.$route.params.id
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
      executors: [],
      test: 3,
      files: ''
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
  watch: {},
  mounted() {
    this.getTask()
  },
  methods: {
    getTask() {
      this.listLoading = true
      this.listQuery.id = this.$route.params.id
      fetchTask(this.listQuery)
        .then(response => {
          this.task = response.data
          this.executors = this.task.executors.map(item => item.user_id)
          this.listLoading = false
        })
    },
    save() {
      console.log('save')
      this.task.users = this.executors
      updateTask(this.task)
        .then(response => {
          this.$router.push('/task/show/' + this.task.id)
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
    }
  }
}
</script>

<style>

</style>
