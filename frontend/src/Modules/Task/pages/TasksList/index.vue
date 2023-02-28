<template>
  <div class="">
    <div class="filter-container">
      <el-input v-model="listQuery.title" placeholder="Найти..." style="width: 200px;" clearable class="filter-item" @click="handleFilter" />
      <el-select v-model="listQuery.executor" placeholder="Исполнитель" clearable style="width: 150px" class="filter-item" @update:model-value="handleFilter">
        <el-option v-for="item in allExecutor"
                   :key="item.id" :label="item.name" :value="item.id" />
      </el-select>
      <el-button class="filter-item" type="primary" @click="handleFilter">Найти</el-button>
      <el-button :loading="downloadLoading" class="filter-item" type="primary" @click="handleDownload">Скачать</el-button>
      <el-checkbox v-if="roles.includes('admin')" v-model="listQuery.archiv" class="filter-item" style="margin-left:15px;" @change="handleFilter">Архив</el-checkbox>
    </div>

    <el-table
      :key="tableKey"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange">
      <el-table-column label="id" prop="id" sortable="custom" align="center" width="65">
        <template #default="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Протокол" width="120px">
        <template #default="scope">
          <div class="text-no-wrap" @click="getProtokolInfo(scope.row.protokol_id)"> {{ scope.row.protokol.nomer }}</div>
        </template>
      </el-table-column>
      <el-table-column label="Дата исполнения" width="100px">
        <template #default="scope">
          <span>{{ formatDate(scope.row.data_ispoln) }}</span>
          <span v-if="scope.row.data_perenosa" class="alert">{{ formatDate(scope.row.data_perenosa) }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Задача" min-width="320px">
        <template #default="scope">
          <div :class="statusFilter(scope.row.execution)">
            <span :title="scope.row.text" class="link-type ellipsis no-wrap" @click="getTaskInfo(scope.row)">{{ scope.row.number }} {{ scope.row.text }}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Исполнитель" width="130px">
        <template #default="scope">
          <span>{{ scope.row.executor }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Ход исполнения" width="150px">
        <template #default="scope">
          <div class="ellipsis" style="font-size: .8em;">
            {{ scope.row.last_report }}
          </div>
        </template>
      </el-table-column>
      <el-table-column label="" align="center" min-width="80px">
        <template #default="scope">
          <el-tag :type="scope.row.execution === 100 ? 'success' : 'danger'">{{ scope.row.execution }}%</el-tag>
        </template>
      </el-table-column>
    </el-table>
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />

  </div>
</template>

<script>
import { fetchList } from 'src/Modules/Task/api/task.js'
import { fetchExecutors } from 'src/Modules/User/api/user.js'
import LoadMore from 'src/components/LoadMore/index.vue'

const calendarTypeOptions = [
  { key: 'psd', display_name: 'Протоколы' },
  { key: 'skype', display_name: 'Скайп Протоколы' },
  { key: 'year', display_name: 'Готодовые протоколы' }
]

const typeArxiv = [
  { key: 'arxiv', display_name: 'Протоколы в архиве' },
  { key: 'all', display_name: 'Все протоколы' }
]

// arr to obj ,such as { CN : "China", US : "USA" }
const calendarTypeKeyValue = calendarTypeOptions.reduce((acc, cur) => {
  acc[cur.key] = cur.display_name
  return acc
}, {})

export default {
  components: { LoadMore },
  data() {
    return {
      key: 1,
      func: fetchList,
      tableKey: 0,
      list: [],
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        arxiv: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        archiv: false,
        executor: undefined
      },
      yearAll: [],
      calendarTypeOptions,
      typeArxiv,
      allExecutor: [],
      test1: false,
      sortOptions: [{ label: 'Сначало новые', key: '+id' }, { label: 'Сначало старые', key: '-id' }],
      statusOptions: ['published', 'draft', 'deleted'],
      showReviewer: false,
      temp: {
        id: undefined,
        importance: 1,
        remark: '',
        timestamp: new Date(),
        title: '',
        type: '',
        status: 'published'
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create'
      },
      dialogPvVisible: false,
      pvData: [],
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }]
      },
      downloadLoading: false,
      expandAll: false,
      data: {},
      protokol: {
        title: '',
        descriptions: {}
      },
      args: [null, null, 'timeLine']
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
    // this.getList()
    this.getExecutors()
  },
  methods: {
    statusFilter(status) {
      if (status === 100) {
        return 'success'
      } else {
        return 'danger'
      }
    },
    typeFilter(type) {
      return calendarTypeKeyValue[type]
    },
    cutString(string, len) {
      var sliced = string.slice(0, len)
      if (sliced.length < string.length) {
        var a = sliced.split(' ')
        a.splice(a.length - 1, 1)
        sliced = a.join(' ')
        sliced += '...'
      }
      return sliced
    },
    formatDate(value) {
      if (value) {
        return value
      }
    },
    setList(val) {
      console.log('load list')
      console.log(val)
      this.list = val
    },
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(response => {
        this.list = response.data.items.data
        this.total = response.data.total
        this.yearAll = response.data.years
        this.listLoading = false
      })
    },
    getExecutors() {
      fetchExecutors()
        .then(response => {
          this.allExecutor = response.data
        })
    },
    getProtokolInfo(id) {
      this.$router.push({ name: 'ProtokolInfo', params: { protokolId: id } })
    },
    handleFilter() {
      this.listQuery.page = 1
      this.key++
    },
    handleModifyStatus(row, status) {
      this.$message({
        message: 'Успешная операция',
        type: 'success'
      })
      row.status = status
    },
    sortChange(data) {
      const { prop, order } = data
      if (prop === 'id') {
        this.sortByID(order)
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = '+id'
      } else {
        this.listQuery.sort = '-id'
      }
      this.handleFilter()
    },
    resetTemp() {
      this.temp = {
        id: undefined,
        importance: 1,
        remark: '',
        timestamp: new Date(),
        title: '',
        status: 'published',
        type: ''
      }
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
    },
    getTaskInfo(row) {
      const task = Object.assign({}, row)
      this.$router.push({ name: 'TaskInfo', params: { taskId: task.id } })
    },
    handleDownload() {
      //   this.downloadLoading = true
      //   import('@/vendor/Export2Excel').then(excel => {
      //     const tHeader = ['Протокол', 'Задача', 'Дата', 'Исполнитель', 'Статус']
      //     const filterVal = ['protokol', 'text', 'data_ispoln', 'executor', 'execution']
      //     const data = this.formatJson(filterVal, this.list)
      //     excel.export_json_to_excel({
      //       header: tHeader,
      //       data,
      //       filename: 'table-list'
      //     })
      //     this.downloadLoading = false
      //   })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return v[j]
        } else if (j === 'protokol') {
          return v['protokol']['nomer']
        } else {
          return v[j]
        }
      }))
    }
  }
}
</script>
<style scoped>
.danger {
  background-color: rgba(245, 108, 108, .1);
}

.alert {
  color: red;
}
</style>
