<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.title" placeholder="Найти..." style="width: 200px;" clearable class="filter-item" @keyup.enter.native="handleFilter"/>
      <el-select v-model="listQuery.executor" placeholder="Исполнитель" clearable style="width: 90px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in allExecutor" :key="item.key" :label="item.display_name" :value="item.key"/>
      </el-select>
      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key"/>
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">{{ $t('table.search') }}</el-button>
      <el-button v-waves :loading="downloadLoading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">{{ $t('table.export') }}</el-button>
      <el-checkbox v-if="roles.includes('admin')" v-model="listQuery.archiv" class="filter-item" style="margin-left:15px;" @change="handleFilter">Архив</el-checkbox>
    </div>

    <el-table
      v-loading="listLoading"
      :key="tableKey"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange">
      <el-table-column :label="$t('table.id')" prop="id" sortable="custom" align="center" width="65">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Протокол" width="120px">
        <template slot-scope="scope" >
          <span @click="getProtokolInfo(scope.row.protokol.id)">{{ scope.row.protokol.nomer }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Дата исполнения" width="100px">
        <template slot-scope="scope">
          <span>{{ scope.row.data_ispoln | formatDate }}</span>
          <span v-if="scope.row.data_perenosa" class="alert">{{ scope.row.data_perenosa | formatDate }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Задача" min-width="100px">
        <template slot-scope="scope">
          <div :class="scope.row.execution | statusFilter">
            <span :title="scope.row.text" class="link-type" @click="getTaskInfo(scope.row)">{{ scope.row.number }} {{ scope.row.text | cutString(120) }}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Исполнитель" width="130px">
        <template slot-scope="scope">
          <span>{{ scope.row.executor }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Ход исполнения" width="150px">
        <template slot-scope="scope">
          <span :title="scope.row.last_report">{{ scope.row.last_report | cutString(50) }}</span>
        </template>
      </el-table-column>
      <el-table-column label="" min-width="10px">
        <template slot-scope="scope">
          <el-tag :type="scope.row.execution | statusFilter">{{ scope.row.execution }}%</el-tag>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

  </div>
</template>

<script>
import { fetchList } from '@/api/task'
import { fetchExecutors } from '@/api/user'
import waves from '@/directive/waves' // Waves directive
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
import { mapGetters } from 'vuex'
import moment from 'moment'

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
  name: 'ComplexTable',
  components: { Pagination },
  directives: { waves },
  filters: {
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
        return moment(String(value)).format('DD-MM-YYYY')
      }
    }
  },
  data() {
    return {
      tableKey: 0,
      list: null,
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
        descriptions: {

        }
      },
      args: [null, null, 'timeLine']
    }
  },
  computed: {
    ...mapGetters([
      'roles'
    ])
  },
  created() {
    this.getList()
    this.getExecutors()
  },
  methods: {
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
      fetchExecutors().then(response => {
        var users = []
        for (const user of response.data.user) {
          var item = {}
          item.key = user.key
          item.display_name = user.display_name
          users.push(item)
        }
        this.allExecutor = Object.assign({}, this.allExecutor, users)
      })
    },
    getProtokolInfo(id) {
      this.$router.push({ name: 'ProtokolInfo', params: { protokolId: id }})
    },
    handleFilter() {
      this.listQuery.page = 1
      this.getList()
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
      this.$router.push({ name: 'TaskInfo', params: { taskId: task.id }})
    },
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['Протокол', 'Задача', 'Дата', 'Исполнитель', 'Статус']
        const filterVal = ['protokol', 'text', 'data_ispoln', 'executor', 'execution']
        const data = this.formatJson(filterVal, this.list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list'
        })
        this.downloadLoading = false
      })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j])
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
  .danger{
    background-color: rgba(245,108,108,.1);
  }
  .alert{
    color: red;
  }
</style>
