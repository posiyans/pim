<template>
  <q-btn
    color="white"
    text-color="black"
    no-caps
    :label="item.label" style="width: 80px;"
    class="q-mr-sm"
    @click="changeCalendar"
  />
</template>

<script>
import { calendar } from 'src/Modules/Calendar/pages/ShowCalendar/components/useSpecialistCalendar.js'
import { computed } from 'vue'

export default {
  setup() {
    const viewOptions = [
      { label: 'День', val: 'day' },
      { label: 'Месяц', val: 'month' }
    ]
    const item = computed(() => {
      return viewOptions.find(i => i.val !== calendar.view)
    })
    const changeCalendar = () => {
      calendar.view = item.value.val
      calendar.opt.type = calendar.view
    }
    return {
      changeCalendar,
      item
    }
  }
}
</script>

<style scoped>

</style>
