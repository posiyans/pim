<template>
  <ShowLogList :list="list" />
</template>

<script>
import { getTaskHistory } from 'src/Modules/Log/api/logApi'
import ShowLogList from 'src/Modules/Log/components/ShowLogList/index.vue'

export default {
  components: {
    ShowLogList
  },
  props: {
    taskId: {
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
        id: this.taskId
      }
      getTaskHistory(data)
        .then(res => {
          this.list = res.data
        })
    }
  }
}
</script>

<style scoped>

</style>
