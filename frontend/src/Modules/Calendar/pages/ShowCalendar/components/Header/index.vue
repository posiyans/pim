<template>
  <div class="bg-grey-3">
    <q-toolbar>
      <q-toolbar-title>
        <div class="row items-center q-col-gutter-x-sm">
          <div v-if="calendar.loading" class="text-orange" style="width: 300px;">
            Загрузка...
          </div>
          <div v-else class="ellipsis" style="width: 300px;">
            <ShowTime :time="calendar.selectedDate" format="MMMM YYYY" />
          </div>
          <NavigationBar />
        </div>
      </q-toolbar-title>
      <q-space />
      <TypeCalendar v-if="false" />
    </q-toolbar>
  </div>
</template>

<script>
import TypeCalendar from './components/TypeCalendar'
import ShowTime from 'src/components/ShowTime/index.vue'
import { calendar, storageKey } from 'src/Modules/Calendar/pages/ShowCalendar/components/useSpecialistCalendar.js'
import NavigationBar from 'src/Modules/Calendar/pages/ShowCalendar/components/NavigationBar/index.vue'
import { onMounted } from 'vue'
import { SessionStorage } from 'quasar'
import { useStore } from 'vuex'
import { today } from '@quasar/quasar-ui-qcalendar/src/index'

export default {
  components: {
    NavigationBar,
    TypeCalendar,
    ShowTime
  },
  setup() {
    const store = useStore()
    onMounted(() => {
      calendar.opt = SessionStorage.has(storageKey) ? SessionStorage.getItem(storageKey).opt : calendar.opt
      calendar.selectedDate = SessionStorage.has(storageKey) ? SessionStorage.getItem(storageKey).selectedDate : today()
      calendar.view = SessionStorage.has(storageKey) ? SessionStorage.getItem(storageKey).view : calendar.view
      calendar.key++
    })

    return {
      calendar,
      store
    }
  }
}
</script>

<style scoped>

</style>
