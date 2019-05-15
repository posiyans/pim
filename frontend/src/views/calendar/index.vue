<template>
  <div>
    <div class="app-container">
      <FullCalendar ref="calendar" :event-sources="eventSources" :config="config" :header="header" @event-selected="eventSelected" />
    </div>
    <div>
      <el-dialog :visible.sync="showForm" title="Задача">
        <div class="dialog-body">
          <div>
            <p><b>Протокол:</b> {{ task.protokol.nomer }} <b>От:</b> {{ task.protokol.descriptions.date }}</p>
            <p><b>Доклад:</b> {{ task.partition.speaker }}</p>
            <p><b>Тема:</b> {{ task.partition.text }}</p>
          </div>
          <p>
            <b>{{ task.number }}.</b>
            {{ task.text }}
          </p>
          <p>Исполнитель: {{ task.executor }}</p>
          <div v-if="task.data_perenosa">
            <p style="color: red">Перенесено с : {{ task.data_ispoln | formatDate }} на {{ task.data_perenosa | formatDate }}</p>
          </div>
          <div v-else>
            <p>Дата исполнения: {{ task.data_ispoln | formatDate }}</p>
          </div>
          <p>Выполнено на {{ task.done }}%</p>
          <div class="block">
            <el-tag v-for="tag in task.executors" :type="tag.done | statusTask" :key="tag.id" class="tag-item">
              {{ tag.user.name }}
            </el-tag>
          </div>
          <div class="link" @click="taskShow(task)">Перейти к задаче № {{ task.id }}</div>
        </div>
        <div slot="footer" class="dialog-footer">
          <el-button @click="showForm = false">Ok</el-button>
        </div>
      </el-dialog>
    </div>
  </div>
</template>
<script>
// import FullCalendar from '@fullcalendar/vue'
// import dayGridPlugin from '@fullcalendar/daygrid'
// import ru from '@fullcalendar/core/locales/ru'
// Vue.use(FullCalendar)
import { FullCalendar } from 'vue-full-calendar'
import moment from 'moment'
import request from '@/utils/request'
// Vue.use(FullCalendar)
export default {
  components: {
    FullCalendar // make the <FullCalendar> tag available
  },
  filters: {
    formatDate(value) {
      if (value) {
        return moment(String(value)).format('DD-MM-YYYY')
      }
    },
    statusTask(value) {
      if (value) {
        return 'success'
      } else {
        return 'danger'
      }
    }
  },
  data() {
    return {
      // calendarPlugins: [dayGridPlugin],
      showForm: false,
      task: {
        partition: {},
        protokol: {
          descriptions: {}
        }
      },
      defaultView: 'month',
      header: {
        left: 'title',
        center: 'prevYear, prev, today, next, nextYear',
        right: 'month,agendaWeek,agendaDay'
      },
      // events: {
      //   url: process.env.BASE_API + '/calendar'
      // },
      eventSources: [
        {
          events(start, end, timezone, callback) {
            request.get(`/calendar`, {params: { start: start, end: end }}).then(response => {
              callback(response.data.data)
            })
          }
        }
      ],
      config: {
        defaultView: 'month',
        locale: 'ru',
        firstDay: '1',
        height: 950

        // changeMonth: function(event, element) {
        //   console.log(event)
        // }
      }
    }
  },
  methods: {
    taskShow(task) {
      this.$router.push({ name: 'TaskInfo', params: { taskId: task.id }})
    },
    next() {
      this.$refs.calendar.fireMethod('next')
      // console.log('next')
    },
    eventSelected(event) {
      this.showForm = true
      this.task = Object.assign({}, {}, event)
      // console.log(event)
      // console.log(event.e)
    }
  }
}

</script>
<!--<style src="@/../../node_modules/@fullcalendar/core/main.css"></style>-->
<!--<style src="@/../../node_modules/@fullcalendar/daygrid/main.css"></style>-->
<style src="fullcalendar/dist/fullcalendar.css"></style>
<style>
  #calendar .fc-title {
    color: black;
  }
  .link {
      padding-top: 30px;
      color: blue;
  }
  .link:hover {
      padding-top: 30px;
      color: #000089;
      cursor: pointer;
  }
</style>
