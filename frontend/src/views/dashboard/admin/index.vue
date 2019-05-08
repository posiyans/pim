<template>
  <div class="dashboard-editor-container">

    <el-row :gutter="32">
      <el-col :xs="24" :sm="24" :lg="16">
        <div class="chart-wrapper">
          <p>Задачи на сегодня</p>
          <el-table
            v-loading="listLoading"
            :key="tableKey"
            :data="list"
            border
            fit
            highlight-current-row
            style="width: 100%;">
            <el-table-column :label="$t('table.id')" prop="id" sortable="custom" align="center" width="65">
              <template slot-scope="scope">
                <span>{{ scope.row.id }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Номер" align="center" width="120px">
              <template slot-scope="scope">
                <div>{{ scope.row.protokol.nomer }}</div>
                <span v-if="scope.row.data_perenosa" class="alert">перенесено с {{ scope.row.data_ispoln }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Протокол" min-width="200px">
              <template slot-scope="scope">
                <div :class="scope.row.execution | statusFilter">
                  <span :title="scope.row.text" class="link-type" @click="getTaskInfo(scope.row)">{{ scope.row.text | cutString(120) }}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="Исполнитель" align="center" width="130px">
              <template slot-scope="scope">
                <span>{{ scope.row.executor }}</span>
              </template>
            </el-table-column>
            <el-table-column label="%" align="center" min-width="60px">
              <template slot-scope="scope">
                <el-tag :type="scope.row.execution | statusFilter">{{ scope.row.execution }}%</el-tag>
              </template>
            </el-table-column>
          </el-table>
          <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <pie-chart/>
        </div>
      </el-col>
    </el-row>

  </div>
</template>

<script>
import PieChart from './components/PieChart'
import { fetchList } from '@/api/task'
import Pagination from '@/components/Pagination'

export default {
  name: 'DashboardAdmin',
  components: {
    PieChart,
    Pagination
  },
  filters: {
    statusFilter(status) {
      if (status === 100) {
        return 'success'
      } else {
        return 'danger'
      }
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
    }
  },
  data() {
    return {
      list: null,
      tableKey: 0,
      listLoading: true,
      total: 0,
      listQuery: {
        page: 1,
        limit: 20,
        arxiv: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        executor: undefined,
        today: true
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(response => {
        this.list = response.data.items.data
        this.total = response.data.total
        this.listLoading = false
      })
    },
    getTaskInfo(row) {
      const task = Object.assign({}, row)
      this.$router.push({ name: 'TaskInfo', params: { taskId: task.id }})
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
  .alert{
    color: red;
  }
}
</style>
