<template>
  <div>
    <div v-if="task" class="container-fluid">
      <div class="row items-center q-col-gutter-sm">
        <div class="text-weight-bolder">
          Задача № {{ task.id }}
        </div>
        <q-space />
        <MoveTaskToArchiveBtn v-if="!task.arxiv" :task-id="task.id" @reload="getTask" />
        <div v-if="!task.arxiv">
          <q-btn icon="edit" color="primary" flat :to="'/task/edit/' + task.id" />
        </div>
      </div>

      <div class="card-body">
        <div>
          <div><b>Протокол:</b> {{ task.protokol.nomer }}</div>
          <div><b>От:</b>{{ task.protokol.descriptions.date }}</div>
          <div><b>Доклад:</b> {{ task.partition.speaker }}</div>
          <div><b>Тема:</b> {{ task.partition.text }}</div>
        </div>
        <div v-if="task.arxiv" class="text-red text-weight-bolder" v-html="task.arxiv" />
        <div class="text-primary bg-grey-2 q-pa-md" v-html="textTask" />
        <div>
          Исполнители: {{ task.executor }}
        </div>
        <div v-if="task.data_ispoln" class="row items-center q-col-gutter-sm">
          <div>
            Выполнить до:
            <b>
              <ShowTime :time="task.data_ispoln" />
            </b>
          </div>
          <div v-if="roles.includes('admin') && task.data_ispoln && !task.arxiv">
            <MoveDateTaskBtn :task-id="task.id" @reload="getTask" />
          </div>
        </div>
        <div v-else class="text-weight-bolder text-secondary">
          Тезис
        </div>
        <div v-if="task.data_perenosa" class="text-red">
          Перенесено на:
          <ShowTime :time="task.data_perenosa" />
        </div>
      </div>
      <div class="bg-grey-1 q-pa-md">
        <div class="row items-center q-col-gutter-sm">
          <div>
            Выполнено:
          </div>
          <div v-for="item in task.executors" :key="item.id">
            <q-btn
              :color="item.done ? 'secondary' : 'negative'"
              @click="changeStatus(item)"
              :disable="!accessFilter(item.user)"
              :loading="item.loading"
            >
              <q-icon v-if="item.done" name="task_alt" class="q-pr-sm" />
              {{ item.user.name }}
            </q-btn>
          </div>
        </div>
      </div>

      <DropDownBlock hide-label="История" show-label="История">
        <div class="q-pa-sm">
          <div v-html="task.history" />
        </div>
      </DropDownBlock>
      <q-card class="">
        <q-card-section class="q-pb-none">
          <div class="text-weight-bold">
            Отчет
            <el-checkbox v-model="listQuery.showdeleted" class="filter-item" style="margin-left:15px;" @change="getTask">Показать удаленные</el-checkbox>
          </div>
        </q-card-section>
        <q-card-section>
          <div>
            <div class="bg-blue-1 q-pa-md">
              <div v-for="item in task.report" :key="item.id">
                <ItemReportMessage :item="item" />
              </div>
            </div>
            <SendReportBlock :task-id="$route.params.id" :disable="task.arxiv" class="bg-blue-2" @reload="getTask" />
          </div>
        </q-card-section>
      </q-card>
    </div>
    <div v-else-if="!listLoading" class="text-center text-red q-pa-lg text-weight-bolder">
      <div>
        Задачи не существует или у вас нет доступа
      </div>
      <div class="q-pa-lg">
        <q-btn label="Задачи" color="primary" to="/task/list" />
      </div>
    </div>
    <div v-else class="text-center">
      <q-spinner-facebook
        color="primary"
        size="5em"
      />
    </div>
  </div>
</template>

<script>
import { fetchTask, setTaskDone } from 'src/Modules/Task/api/task.js'
import DropDownBlock from 'components/DropDownBlock/index.vue'
import ItemReportMessage from 'src/Modules/Task/components/ItemReportMessage/index.vue'
import SendReportBlock from 'src/Modules/Task/components/SendReportBlock/index.vue'
import MoveTaskToArchiveBtn from 'src/Modules/Task/components/MoveTaskToArchiveBtn/index.vue'
import MoveDateTaskBtn from 'src/Modules/Task/components/MoveDateTaskBtn/index.vue'
import ShowTime from 'src/components/ShowTime/index.vue'

export default {
  components: {
    ShowTime,
    DropDownBlock,
    MoveTaskToArchiveBtn,
    ItemReportMessage,
    SendReportBlock,
    MoveDateTaskBtn
  },
  data() {
    return {
      access: false,
      showdeleted: false,
      time: '',
      task: null,
      test: 3,
      files: '',
      listLoading: false,
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
    },
    textTask() {
      return this.task.number + '. ' + this.task.text
    }
  },
  mounted() {
    this.getTask()
  },
  methods: {
    accessFilter(user) {
      if (this.task.arxiv) {
        return false
      }
      var access = false
      if (user.id === this.userId) {
        access = true
      }
      if (this.roles.includes('admin')) {
        access = true
      }
      return access
    },
    changeStatus(executor) {
      if (executor.user_id === this.userId || this.roles.includes('admin')) {
        executor.loading = true
        if (executor.done) {
          executor.done = null
        } else {
          executor.done = 'ok'
        }
        setTaskDone(executor)
          .finally(() => {
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
        })
        .finally(() => {
          this.listLoading = false
        })
    },
    getUrl(link) {
      return process.env.API + '/..' + link
    }
  }
}
</script>

<style scoped>

</style>
