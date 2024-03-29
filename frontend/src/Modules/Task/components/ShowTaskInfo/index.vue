<template>
  <div>
    <div v-if="task">
      <div class="row items-center q-col-gutter-sm">
        <div class="text-weight-bolder">
          Задача № {{ task.id }}
        </div>
        <q-space />
        <MoveTaskToArchiveBtn v-if="!task.arxiv" :task-id="task.id" @reload="getTask" />
        <div v-if="!task.arxiv && isModerator">
          <q-btn icon="edit" color="primary" flat :to="'/task/edit/' + task.id" />
        </div>
      </div>

      <div class="card-body">
        <div>
          <router-link class="link-type text-black row items-center q-col-gutter-sm hover-opacity-80" :to="'/protocol/show/' + task.protokol.id">
            <div class="text-weight-bold">
              Протокол:
            </div>
            <div>
              {{ task.protokol.number }}
            </div>
            <div>
              от
            </div>
            <div>
              {{ task.protokol.descriptions.date }}
            </div>
            <div>
              {{ task.protokol.descriptions.region }}
            </div>
          </router-link>
          <div><b>Доклад:</b> {{ task.partition.speaker }}</div>
          <div><b>Тема:</b> {{ task.partition.number }}.{{ task.partition.text }}</div>
        </div>
        <div v-if="task.arxiv" class="text-red text-weight-bolder" v-html="task.arxiv" />
        <div class="row text-primary bg-grey-1 q-pa-md q-col-gutter-xs">
          <div>
            {{ task.partition.number }}.{{ task.number }}.
          </div>
          <div v-html="task.text" />
        </div>
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
          <div v-if="roles.includes('moderator') && task.data_ispoln && !task.arxiv">
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
              :disable="!accessFilter(item.user)"
              @click="changeStatus(item)"
            >
              <q-icon v-if="item.done" name="task_alt" class="q-pr-sm" />
              {{ item.user.name }}
            </q-btn>
          </div>
        </div>
      </div>

      <DropDownBlock hide-label="История" show-label="История">
        <div class="q-pa-sm">
          <ShowTaskHistory :task-id="task.id" />
        </div>
      </DropDownBlock>
      <q-card class="">
        <q-toolbar class="">
          <q-toolbar-title>
            Отчет
          </q-toolbar-title>
          <q-btn v-if="isModerator" flat round dense icon="more_vert">
            <q-menu>
              <q-list style="min-width: 100px">
                <q-item clickable v-close-popup>
                  <q-checkbox v-model="listQuery.showdeleted" @update:model-value="getTask">Показать удаленные</q-checkbox>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </q-toolbar>
        <q-card-section>
          <div>
            <div class="bg-blue-1 q-pa-md">
              <div v-for="item in task.report" :key="item.id">
                <ItemReportMessage :item="item" @reload="getTask" />
              </div>
              <div v-if="task.report.length === 0" class="q-pa-md text-primary">
                Нет отчетов
              </div>
            </div>
            <SendReportBlock :task-id="taskId" :disable="task.arxiv" class="bg-blue-2" @reload="getTask" />
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
import ShowTaskHistory from 'src/Modules/Log/components/ShowTaskHistory/index.vue'

export default {
  components: {
    ShowTaskHistory,
    ShowTime,
    DropDownBlock,
    MoveTaskToArchiveBtn,
    ItemReportMessage,
    SendReportBlock,
    MoveDateTaskBtn
  },
  props: {
    taskId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      timer: null,
      access: false,
      task: null,
      listLoading: false,
      listQuery: {
        showdeleted: false,
        id: this.taskId
      }
    }
  },
  computed: {
    user() {
      return this.$store.state.user.info
    },
    userId() {
      return this.user.id
    },
    roles() {
      return this.user.roles
    },
    isModerator() {
      return this.roles.includes('moderator')
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
      if (this.roles.includes('moderator')) {
        access = true
      }
      return access
    },
    changeStatus(executor) {
      if (executor.user_id === this.userId || this.roles.includes('moderator')) {
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
      this.listQuery.id = this.taskId
      fetchTask(this.listQuery)
        .then(res => {
          this.access = true
          this.task = res.data
        })
        .finally(() => {
          this.listLoading = false
        })
    }
  }
}
</script>

<style scoped>

</style>
