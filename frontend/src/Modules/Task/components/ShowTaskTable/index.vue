<template>
  <table class="p-table-task full-width q-table q-table--cell-separator q-table--bordered q-table__card q-table--flat">
    <TableHeader :columns="columns" />
    <tbody>
    <template v-for="row in list" :key="row.id">
      <tr :class="{ 'task-red-1': row.execution < 100 }">
        <td style="padding: 2px 8px; width: 70px;">
          <div class="row justify-between">
            <div class="text-no-wrap cursor-pointer text-small-70 "> id: {{ row.id }}</div>
            <div class="text-small-70" :class="row.execution === 100 ? 'text-teal' : 'text-negative'">
              {{ row.execution }}%
            </div>
          </div>
          <div v-if="row.arxiv" class="text-teal text-small-60">В архиве</div>
          <router-link
            class="text-no-wrap link-type text-small-80 "
            :to="'/protocol/show/' + row.protokol.id"
          >
            {{ row.protokol.number }}
          </router-link>
        </td>
        <td style="padding: 8px 4px; width: 65px;">
          <div v-if="row.data_ispoln">
            <ShowTime :time="row.data_ispoln" format="DD.MM.YYYY" />
          </div>
          <div v-else>
            Тезис
          </div>
          <div v-if="row.data_perenosa" class="text-red">
            <ShowTime :time="row.data_perenosa" format="DD.MM.YYYY" />
          </div>
        </td>
        <td>
          <router-link class="link-type cursor-pointer no-wrap text-primary text-small-80 row q-px-sm" :to="'/task/show/' + row.id">
            <div class="q-mr-xs">
              {{ row.partition.number }}.{{ row.number }}.
            </div>
            <div v-html="row.text" class="" style="max-width: 40vw;" />
            <q-tooltip v-if="row.text.length > 100">
              {{ row.text }}
            </q-tooltip>
          </router-link>
        </td>
        <td>
          <div class="text-small-90 text-center">{{ row.executor }}</div>
        </td>
        <td class="relative-position">
          <NoReadReport :item="row" class="absolute-top-right text-teal text-small-80" />
          <div v-if="row.last_report" class="ellipsis-3-lines q-px-sm text-small-80" style="max-width: 40vw;">
            <q-tooltip>
              {{ row.last_report.text }}
            </q-tooltip>
            {{ row.last_report?.text }}
            <div v-if="row.last_report.files.length > 0" class="text-primary">
              <q-icon name="text_snippet" />
              {{ row.last_report.files[0].name }}
            </div>
          </div>
        </td>
      </tr>
    </template>
    </tbody>
  </table>
</template>

<script>
/* eslint-disable */
import { fetchList } from 'src/Modules/Task/api/task.js'
import NoReadReport from 'src/Modules/Task/pages/TasksList/components/NoReadReport/index.vue'
import ShowTime from 'components/ShowTime/index.vue'
import TableHeader from './TableHeader.vue'

export default {
  components: { NoReadReport, ShowTime, TableHeader },
  props: {
    list: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      key: 1,
      func: fetchList,
      columns: [
        {
          name: 'protocol',
          label: 'Протокол',
          align: 'center',
          width: '70px'
        },
        {
          name: 'date',
          label: 'Дата',
          align: 'center',
          width: '70px'
        },
        {
          name: 'task',
          label: 'Задача',
          align: 'left',
          width: '35vw'
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
    tableRowClassName({ row }) {
      if (row.execution < 100) {
        return 'task-red-1'
      }
      return ''
    },
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
<style scoped lang="scss">
.q-table th, .q-table td {
  padding: 0;
  background-color: inherit;
}

//table.p-table-task, td {
//  border: 1px solid #b4b4b4;
//  border-collapse: separate;
//  border-spacing: 0;
//}

tr:hover {
  //background-color: #f5f7fa !important;
}

.task-red-1 {
  background-color: #fff8f8;
}
</style>

