<template>
  <div class="q-px-md">
    {{ title }}
  </div>
</template>

<script>
import { getBalance } from 'src/Modules/Sms/api/sms'

export default {
  data() {
    return {
      balance: null
    }
  },
  computed: {
    title() {
      if (this.balance) {
        return this.balance + ' руб.'
      }
      return ''
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      getBalance()
        .then(res => {
          this.balance = res.data
        })
    }
  }
}
</script>

<style scoped>

</style>
