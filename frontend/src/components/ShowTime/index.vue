<template>
  <span>{{ time_show }}</span>
</template>
<script>
import { date } from 'quasar'

export default {
  props: {
    time: {
      type: [String, Number, Date],
      default: ''
    },
    tz: {
      type: String,
      default: ''
    },
    format: {
      type: String,
      default: 'DD-MM-YYYY' //DD MMMM YYYY в HH:mm
    }
  },
  data() {
    return {}
  },
  computed: {
    int_time() {
      return date.inferDateFormat(this.time) === 'number'
    },
    filterTime() {
      if (this.int_time) {
        return this.time * 1000
      }
      return this.time
    },
    time_show() {
      if (this.time) {
        if (this.time === '0000-00-00') {
          return ''
        } else {
          return date.formatDate(this.filterTime, this.format)
        }
      }
      return ''
    }
  }
}
</script>

<style scoped>

</style>
