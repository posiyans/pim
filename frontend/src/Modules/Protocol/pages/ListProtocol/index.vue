<template>
  <div class="app-container">
    <div class="row items-center q-col-gutter-sm q-pb-xs">
      <div style="width: 220px;">
        <QSelectTypeProtocol v-model="listQuery.type" outlined dense clearable @update:model-value="handleFilter" />
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
        <q-btn icon="add" color="primary" flat to="/protocol/add" />
      </div>
    </div>
    <q-table
      :rows="list"
      :columns="columns"
      separator="cell"
      bordered
      hide-bottom
      :pagination="{rowsPerPage: 0}"
      flat
      row-key="name"
    >
      <template v-slot:body-cell-id="scope">
        <q-td :props="scope" auto-width>
          <span>{{ scope.row.id }}</span>
        </q-td>
      </template>
      <template v-slot:body-cell-protocol="scope">
        <q-td :props="scope">
          <router-link class="link-type" :to="'/protocol/show/' + scope.row.id">
            {{ scope.row.title }}
          </router-link>
        </q-td>
      </template>
      <template v-slot:body-cell-date="scope">
        <q-td :props="scope">
          <span>{{ scope.row.descriptions.region }} {{ scope.row.descriptions.date }}</span>
        </q-td>
      </template>
      <template v-slot:body-cell-status="scope">
        <q-td :props="scope" auto-width>
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
        </q-td>
      </template>
    </q-table>
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />
  </div>
</template>

<script>
import { fetchList } from 'src/Modules/Protocol/api/protocol.js'
import QSelectTypeProtocol from 'src/Modules/Protocol/components/QSelectTypeProtocol/index.vue'
import LoadMore from 'src/components/LoadMore/index.vue'

export default {
  components: { LoadMore, QSelectTypeProtocol },
  data() {
    return {
      key: 1,
      func: fetchList,
      list: [],
      columns: [
        {
          name: 'id',
          label: '№',
          align: 'center'
        },
        {
          name: 'protocol',
          label: 'Протокол',
          align: 'left'
        },
        {
          name: 'date',
          label: 'Дата',
          align: 'center'
        },
        {
          name: 'status',
          label: '%',
          align: 'center'
        },
      ],
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        find: '',
        type: undefined,
        archiv: false,
        sort: '+id'
      },


    }
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
