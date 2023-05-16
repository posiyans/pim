<template>
  <div>
    <q-btn flat dense icon="keyboard_arrow_left" @click="calendarPrev"></q-btn>
    <q-btn flat dense icon="keyboard_arrow_right" @click="calendarNext"></q-btn>
    <q-btn flat dense label="Сегодня" no-caps class="q-mx-md" @click="calendarToday"></q-btn>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { calendar } from 'src/Modules/Calendar/pages/ShowCalendar/components/useCalendar.js'

export default defineComponent({
  setup() {
    const calendarNext = () => {
      calendar.refCalendar.next()
    }
    const calendarPrev = () => {
      calendar.refCalendar.prev()
    }
    const calendarToday = () => {
      calendar.refCalendar.moveToToday()
    }
    return {
      calendarNext,
      calendarPrev,
      calendarToday
    }
  }
})
</script>

<style scoped>

</style>
