<template>
  <ShowLogList :list="list" />
</template>

<script>
import { getProtocolHistory } from 'src/Modules/Log/api/logApi'
import ShowLogList from 'src/Modules/Log/components/ShowLogList/index.vue'

export default {
  components: {
    ShowLogList
  },
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      list: []
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      const data = {
        id: this.id
      }
      getProtocolHistory(data)
        .then(res => {
          this.list = res.data
        })
    }
  }
}
</script>

<style scoped>

</style>
