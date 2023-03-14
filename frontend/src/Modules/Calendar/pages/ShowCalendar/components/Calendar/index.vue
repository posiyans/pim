<template>
  <div v-if="show" :key="calendar.view" style="width: 100%;">
    <div
    >
      <QCalendarMonth
        :ref="(e) => calendar.refCalendar = e"
        v-model="calendar.selectedDate"
        locale="ru-Ru"
        animated
        bordered
        focusable
        hoverable
        :day-min-height="80"
        :weekdays="[1,2,3,4,5,6,0]"
        @change="changeCalendar"
      >
        <template #day="{ scope: { timestamp } }">
          <div
            v-for="item in getEventsGroup(timestamp.date)"
            :key="item.id"
            class="text-black cursor-pointer"
            :class="item.execution < 100 ? 'bg-red-3' : 'bg-teal-3'"
            style="margin-bottom: 1px;"
            @click="showTask(item)"
          >
            <div class="row items-center">
              <div class="q-mr-sm">
                {{ item.executor }}
              </div>
              <ExecutorsAvatar :executors="item.view_report" />
            </div>
          </div>
        </template>
      </QCalendarMonth>
    </div>
    <q-dialog v-model="showForm" title="Задача">
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">Задача</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-separator />
        <q-card-section>
          <ShowTaskInfo :task-id="task.id" />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<style src="@quasar/quasar-ui-qcalendar/dist/index.css"></style>

<script>
/* eslint-disable */

import { QCalendarDay, QCalendarMonth } from '@quasar/quasar-ui-qcalendar/src/index'
import { defineComponent, onMounted, ref } from 'vue'
import { calendar } from 'src/Modules/Calendar/pages/ShowCalendar/components/useSpecialistCalendar.js'
import ShowTime from 'src/components/ShowTime/index.vue'
import ShowTaskInfo from 'src/Modules/Task/components/ShowTaskInfo/index.vue'
import ExecutorsAvatar from 'src/Modules/Calendar/pages/ShowCalendar/components/Calendar/components/ExecutorsAvatar/index.vue'

const moment = require('moment')
// const momentTz = require('moment-timezone')
require('moment/locale/ru')
require('moment/locale/en-au')
moment.locale('ru')
// momentTz.suppressDeprecationWarnings = true
// momentTz.tz.setDefault('UTC')


export default defineComponent({
  components: {
    ShowTime,
    ShowTaskInfo,
    QCalendarMonth,
    QCalendarDay,
    ExecutorsAvatar

  },
  setup() {
    const show = ref(false)
    onMounted(() => {
      // calendar.selectedDate = today()
      show.value = true
    })
    const getEventsGroup = (dt) => {
      if (calendar.events[dt]) {
        return calendar.events[dt]
      }
      return {}
    }
    const showForm = ref(false)
    const changeCalendar = (val) => {
      calendar.start = val.start
      calendar.end = val.end
    }

    const task = ref({})
    const showTask = (item) => {
      task.value = item
      showForm.value = true
    }

    return {
      task,
      show,
      showForm,
      calendar,
      showTask,
      changeCalendar,
      getEventsGroup
    }
  }

})

</script>

<style lang="sass">

</style>
