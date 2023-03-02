<template>
  <span>{{ time_show }}</span>
</template>s
<script>
export default {
  props: {
    time: [String, Number, Date],
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
    timeZone() {
      if (this.tz !== '') {
        return this.tz
      }
      return this.$moment.tz.guess(true)
    },
    int_time() {
      return parseInt(+this.time, 10)
    },
    sql_time() {
      if (!this.int_time && typeof this.time === 'string' && this.time.length > 16 && this.time[4] === '-' && this.time[7] === '-' && (this.time[10] === ' ' || this.time[10] === 'T') && this.time[13] === ':' && this.time[16] === ':') {
        return true
      }
      return false
    },
    date_format() {
      if (this.time[4] === '-' && this.time[7] === '-' && this.time.length === 10) {
        return true
      }
      return false
    },
    time_show() {
      if (this.time) {
        if (this.time === '0000-00-00') {
          return ''
        } else if (this.time instanceof Date) {
          return this.$moment(this.time).format(this.format)
        } else if (this.sql_time || this.date_format) {
          return this.$moment(this.time).format(this.format)
          // } else if (this.int_time > 0) {
        } else if (this.$moment.unix(this.int_time)) {
          return this.$moment.unix(this.int_time).format(this.format)
        }
      }
      return ''
    }
  }
}
</script>

<style scoped>

</style>
