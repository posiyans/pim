<template>
  <div>
    <div class="text-primary text-weight-bold q-pa-sm">
      Задачи на сегодня
    </div>
    <ShowTaskTable :list="list" />
    <div class="q-px-sm text-grey-8">
      Всего {{ total }}
    </div>
    <LoadMore v-show="total > listQuery.limit" :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" @setTotal="setTotal" />
  </div>
</template>

<script>
import { fetchList } from 'src/Modules/Task/api/task.js'
import LoadMore from 'src/components/LoadMore/index.vue'
import ShowTaskTable from 'src/Modules/Task/components/ShowTaskTable/index.vue'

export default {
  components: { LoadMore, ShowTaskTable },
  data() {
    return {
      key: 1,
      func: fetchList,
      list: [],
      total: 0,
      listQuery: {
        page: 1,
        limit: 100,
        today: true,
        archiv: '',
        title: '',
        type: undefined,
        sort: '+id',
        executor: undefined
      }
    }
  },
  computed: {
    user() {
      return this.$store.state.user.info
    },
    roles() {
      return this.user.roles
    }
  },
  mounted() {
  },
  methods: {
    tableRowClassName({ row }) {
      if (row.execution < 100) {
        return 'task-red-1'
      }
      return ''
    },
    setTotal(val) {
      this.total = val
    },
    setList(val) {
      this.list = val
    },
    getProtokolInfo(id) {
      this.$router.push('/protocol/show/' + id)
    },
    handleFilter() {
      this.listQuery.page = 1
      this.key++
    },
    getTaskInfo(row) {
      this.$router.push('/task/show/' + row.id)
    }
  }
}
</script>
<style scoped>

</style>
<style>
.task-red-1 {
  background-color: #fff8f8 !important;
}
</style>

