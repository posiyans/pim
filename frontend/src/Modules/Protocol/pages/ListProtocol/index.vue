<template>
  <div class="app-container">
    <div class="row items-center q-col-gutter-sm q-pb-xs">
      <div style="width: 220px;">
        <QSelectTypeProtocol v-model="listQuery.type" outlined dense />
      </div>
      <div>
        <q-input
          v-model="listQuery.find"
          label="Найти"
          dense
          outlined
          clearable
          @update:model-value="handleFilter"
        />
      </div>
      <div>
        <q-checkbox v-model="listQuery.archiv" label="Архив" @update:model-value="handleFilter" />
      </div>
      <div>
        <q-btn icon="add" color="primary" flat @click="addProtokol" />
      </div>
    </div>

    <el-table
      :key="tableKey"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column label="№" prop="id" align="center" width="65">
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
          <q-knob
            disable
            v-model="scope.row.PercentComplete"
            show-value
            size="30px"
            :thickness="0.5"
            color="teal-2"
            track-color="red-1"
            :class="scope.row.PercentComplete >90 ? 'text-primary' : 'text-negative'"
          />
        </template>
      </el-table-column>
    </el-table>

    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />

  </div>
</template>

<script>
import { fetchList, fetchProtokol } from 'src/Modules/Protocol/api/protocol.js'
import QSelectTypeProtocol from 'src/Modules/Protocol/components/QSelectTypeProtocol/index.vue'
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
  components: { LoadMore, QSelectTypeProtocol },
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
        find: '',
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
