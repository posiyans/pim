<template>
  <div class="app-container">
    <div class="filter-container">
      <el-select v-model="listQuery.type" placeholder="Тип протокола" clearable class="filter-item" style="width: 130px" @update:model-value="handleFilter">
        <el-option v-for="item in calendarTypeOptions" :key="item.key" :label="item.display_name" :value="item.key" />
      </el-select>
      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @update:model-value="handleFilter">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" />
      </el-select>
      <el-button class="filter-item" type="primary" @click="handleFilter">Найти</el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" @click="addProtokol">Add</el-button>
      <el-button :loading="downloadLoading" class="filter-item" type="primary" @click="handleDownload">Скачать</el-button>
      <el-checkbox v-model="listQuery.archiv" class="filter-item" style="margin-left:15px;" @change="handleFilter">Архив</el-checkbox>
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
      <el-table-column label="Протокол" min-width="200px">
        <template #default="scope">
          <span class="link-type" @click="getProtokolInfo(scope.row)">{{ scope.row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Дата" align="center" min-width="200px">
        <template #default="scope">
          <span>{{ scope.row.descriptions.region }} {{ scope.row.descriptions.date }}</span>
        </template>
      </el-table-column>
      <el-table-column label="%" align="center" width="80px">
        <template #default="scope">
          <el-tag :type="scope.row.PercentComplete > 90 ? 'success' : 'danger'">{{ scope.row.PercentComplete }} %</el-tag>
        </template>
      </el-table-column>
    </el-table>

    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />

  </div>
</template>

<script>
import { fetchList, fetchProtokol } from 'src/Modules/Protocol/api/protocol.js'

import LoadMore from 'src/components/LoadMore/index.vue'

const calendarTypeOptions = [
  { key: 'psd', display_name: 'Протоколы' },
  { key: 'skype', display_name: 'Скайп Протоколы' },
  { key: 'year', display_name: 'Готодовые протоколы' }
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
        year: undefined,
        title: undefined,
        type: undefined,
        archiv: false,
        sort: '+id'
      },
      yearAll: [],
      calendarTypeOptions,
      sortOptions: [{ label: 'Сначало новые', key: '+id' }, { label: 'Сначало старые', key: '-id' }],
      statusOptions: ['published', 'draft', 'deleted'],
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
  methods: {
    setList(val) {
      this.list = val
    },
    getList() {
      this.key++
      this.listLoading = true
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
    handleUpdate(row) {
      this.temp = Object.assign({}, row) // copy obj
      this.temp.timestamp = new Date(this.temp.timestamp)
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
    },
    getProtokolInfo2(row) {
      this.temp = Object.assign({}, row)
      fetchProtokol({ 'id': this.temp.id }).then(response => {
        this.data = response.data.items
        this.protokol = response.data.protokol
        // this.listLoading = false
        // Just to simulate the time of the request
        // setTimeout(() => {
        //   this.listLoading = false
        // }, 0.5 * 1000)
      })
      this.dialogFormVisible = true
    },
    getProtokolInfo(row) {
      this.$router.push('/protocol/show/' + row.id)
    },

    addProtokol() {
      this.$router.push('/protocol/add')
    }
  }
}
</script>
