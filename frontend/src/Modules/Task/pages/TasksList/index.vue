<template>
  <div>
    <div class="row items-center q-col-gutter-sm q-pb-xs">
      <div style="width: 200px;">
        <q-input v-model="listQuery.title" label="Найти..." dense clearable outlined @update:model-value="handleFilter" />
      </div>
      <div style="width: 200px;">
        <QSelectExecutor v-model="listQuery.executor" clearable outlined dense @update:model-value="handleFilter" />
      </div>
      <div>
        <q-btn color="primary" label="Найти" @click="handleFilter" />
      </div>
      <div>
        <q-checkbox v-if="roles.includes('moderator')" v-model="listQuery.archiv" label="Архив" @update:model-value="handleFilter" />
      </div>
    </div>
    <div>
      <ShowTable :list="list" />
    </div>
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />

  </div>
</template>

<script>
import { fetchList } from 'src/Modules/Task/api/task.js'
import LoadMore from 'src/components/LoadMore/index.vue'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ShowTable from 'src/Modules/Task/components/ShowTaskTable/index.vue'

export default {
  components: { LoadMore, QSelectExecutor, ShowTable },
  data() {
    return {
      key: 1,
      func: fetchList,
      list: [],
      columns: [
        {
          name: 'protocol',
          label: 'Протокол',
          align: 'center'
        },
        {
          name: 'date',
          label: 'Дата',
          align: 'center'
        },
        {
          name: 'task',
          label: 'Задача',
          align: 'left'
        },
        {
          name: 'executor',
          label: 'Исполнитель',
          align: 'left'
        },
        {
          name: 'status',
          label: 'Исполнение',
          align: 'left'
        },
      ],
      listQuery: {
        page: 1,
        limit: 20,
        archiv: false,
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
    setList(val) {
      this.list = val
    },
    handleFilter() {
      this.listQuery.page = 1
      this.key++
    }
  }
}
</script>
<style scoped>

</style>
<style>
.link-type {
  text-decoration: none;
}

.task-red-1 {
  background-color: #fff8f8 !important;
}
</style>

