<template>
  <div>
    <div class="row items-center q-col-gutter-sm q-pb-xs">
      <div>
        <el-input v-model="listQuery.title" placeholder="Найти..." style="width: 200px;" clearable class="filter-item" @update:model-value="handleFilter" />
      </div>
      <SelectExecutor v-model="listQuery.executor" clearable @update:model-value="handleFilter" />
      <div>
        <el-button class="filter-item" type="primary" @click="handleFilter">Найти</el-button>
      </div>
      <div>
        <el-checkbox v-if="roles.includes('admin')" v-model="listQuery.archiv" class="filter-item" style="margin-left:15px;" @change="handleFilter">Архив</el-checkbox>
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
      <el-table-column label="id" prop="id" align="center" width="65">
        <template #default="scope">
          <div>{{ scope.row.id }}</div>
          <div v-if="scope.row.arxiv" class="text-teal text-small-60">В архиве</div>
        </template>
      </el-table-column>

      <el-table-column label="Протокол" width="120px">
        <template #default="scope">
          <div class="text-no-wrap cursor-pointer" @click="getProtokolInfo(scope.row.protokol_id)"> {{ scope.row.protokol.nomer }}</div>
        </template>
      </el-table-column>

      <el-table-column label="Дата исполнения" width="100px">
        <template #default="scope">
          <div>{{ scope.row.data_ispoln }}</div>
          <div v-if="scope.row.data_perenosa" class="text-red">{{ scope.row.data_perenosa }}</div>
        </template>
      </el-table-column>

      <el-table-column label="Задача" min-width="320px">
        <template #default="scope">
          <div class="link-type ellipsis no-wrap cursor-pointer" @click="getTaskInfo(scope.row)">
            {{ scope.row.number }} {{ scope.row.text }}
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

      <el-table-column label="Ход исполнения" width="150px">
        <template #default="scope">
          <div class="ellipsis" style="font-size: .8em;">
            <q-tooltip>
              {{ scope.row.last_report }}
            </q-tooltip>
            {{ scope.row.last_report }}
          </div>
        </template>
      </el-table-column>

      <el-table-column label="" align="center" width="80px" class-name="bg-white">

        <template #default="scope">
          <div>
            <q-knob
              disable
              v-model="scope.row.execution"
              show-value
              size="40px"
              :thickness="0.5"
              color="teal-2"
              track-color="red-1"
              :class="scope.row.execution === 100 ? 'text-primary' : 'text-negative'"
            />
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
import SelectExecutor from 'src/Modules/User/components/SelectExecutor/index.vue'


export default {
  components: { LoadMore, SelectExecutor },
  data() {
    return {
      key: 1,
      func: fetchList,
      list: [],
      listQuery: {
        page: 1,
        limit: 20,
        arxiv: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
        archiv: false,
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
        return 'bg-deep-orange-1'
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
