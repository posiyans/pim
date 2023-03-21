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

    <el-table
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      :row-class-name="tableRowClassName"
    >
      <el-table-column label="Протокол" prop="id" align="center" width="100">
        <template #default="scope">
          <div class="row justify-between">

            <div class="text-no-wrap cursor-pointer text-small-70 "> id: {{ scope.row.id }}</div>
            <div class="text-small-70" :class="scope.row.execution === 100 ? 'text-teal' : 'text-negative'">
              {{ scope.row.execution }}%
            </div>
          </div>
          <div v-if="scope.row.arxiv" class="text-teal text-small-60">В архиве</div>
          <div class="text-no-wrap cursor-pointer text-small-70 " @click="getProtokolInfo(scope.row.protokol_id)"> {{ scope.row.protokol.nomer }}</div>

        </template>
      </el-table-column>

      <el-table-column label="Дата" width="100px">
        <template #default="scope">
          <div v-if="scope.row.data_ispoln">
            <ShowTime :time="scope.row.data_ispoln" format="DD.MM.YYYY" />
          </div>
          <div v-else>
            Тезис
          </div>
          <div v-if="scope.row.data_perenosa" class="text-red">
            <ShowTime :time="scope.row.data_perenosa" format="DD.MM.YYYY" />
          </div>
        </template>
      </el-table-column>

      <el-table-column label="Задача" max-width="50vw">
        <template #default="scope">
          <div class="link-type ellipsis no-wrap cursor-pointer text-small-80 row" @click="getTaskInfo(scope.row)">
            <div class="q-mr-xs">
              {{ scope.row.partition.number }}.{{ scope.row.number }}.
            </div>
            <div v-html="scope.row.text" />
            <q-tooltip v-if="scope.row.text.length > 100">
              {{ scope.row.text }}
            </q-tooltip>
          </div>
        </template>
      </el-table-column>

      <el-table-column label="Исполнитель" width="130px">
        <template #default="scope">
          <span>{{ scope.row.executor }}</span>
        </template>
      </el-table-column>

      <el-table-column label="Ход исполнения" class-name="relative-position">
        <template #default="scope">
          <NoReadReport :item="scope.row" class="absolute-top-right text-teal" style="font-size: .8em;" />
          <div v-if="scope.row.last_report" class="" style="font-size: .8em; max-height: 48px;">
            <q-tooltip>
              {{ scope.row.last_report.text }}
            </q-tooltip>
            {{ scope.row.last_report?.text }}
            <div v-if="scope.row.last_report.files.length > 0" class="text-primary">
              <q-icon name="text_snippet" />
              {{ scope.row.last_report.files[0].name }}
            </div>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />

  </div>
</template>

<script>
import { fetchList } from 'src/Modules/Task/api/task.js'
import LoadMore from 'src/components/LoadMore/index.vue'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import NoReadReport from 'src/Modules/Task/pages/TasksList/components/NoReadReport/index.vue'
import ShowTime from 'components/ShowTime/index.vue'

export default {
  components: { LoadMore, QSelectExecutor, NoReadReport, ShowTime },
  data() {
    return {
      key: 1,
      func: fetchList,
      list: [],
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
    tableRowClassName({ row }) {
      if (row.execution < 100) {
        return 'task-red-1'
      }
      return ''
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

