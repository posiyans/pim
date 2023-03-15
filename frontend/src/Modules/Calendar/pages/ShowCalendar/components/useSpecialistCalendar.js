import { reactive, ref, watch } from 'vue'
import { date, SessionStorage } from 'quasar'
import { today } from '@quasar/quasar-ui-qcalendar/src/index'
import { getTasksForCalendar } from 'src/Modules/Calendar/api/calendar'

const moment = require('moment')
// const momentTz = require('moment-timezone')
require('moment/locale/ru')
require('moment/locale/en-au')
moment.locale('ru')
// momentTz.locale('ru')
// momentTz.suppressDeprecationWarnings = true
// momentTz.tz.setDefault('UTC')

const storageKey = 'SpecialistCalendarStorageKey'
const timer = ref(null)

const now = new Date()
const calendar = reactive({
  key: 1,
  refCalendar: null,
  start: date.formatDate(date.addToDate(now, { months: -1 }), 'YYYY-MM-DD'),
  end: date.formatDate(date.addToDate(now, { months: 1 }), 'YYYY-MM-DD'),
  loading: false,
  selectedDate: today(),
  view: 'month',
  events: {},
  opt: {
    executor: [1, 2, 3],
  }
})

watch(() => [calendar.opt, calendar.key, calendar.selectedDate, calendar.view],
  () => {
    SessionStorage.set(storageKey, calendar)
    calendar.loading = true
    if (timer.value) clearTimeout(timer.value)
    timer.value = setTimeout(() => {
      getData()
    }, 800)
  },
  { deep: true }
)

const getData = () => {
  const data = {}
  // data.executor = calendar.opt.executor
  data.date_start = date.formatDate(calendar.start, 'YYYY-MM-DD')
  data.date_end = date.formatDate(date.addToDate(calendar.end, { days: 1 }), 'YYYY-MM-DD')
  data.type = calendar.view
  calendar.loading = true
  getTasksForCalendar(data)
    .then(res => {
      const tmp = {}
      res.data.forEach(task => {
        if (task.data_perenosa) {
          if (!tmp[task.data_perenosa]) {
            tmp[task.data_perenosa] = []
          }
          tmp[task.data_perenosa].push(task)
        } else if (task.data_ispoln) {
          if (!tmp[task.data_ispoln]) {
            tmp[task.data_ispoln] = []
          }
          tmp[task.data_ispoln].push(task)
        }
      })
      for (const [key, value] of Object.entries(tmp)) {
        calendar.events[key] = value
      }
      // calendar.events = tmp
    })
    .finally(() => {
      calendar.loading = false

    })
}

export { calendar, storageKey, getData }
