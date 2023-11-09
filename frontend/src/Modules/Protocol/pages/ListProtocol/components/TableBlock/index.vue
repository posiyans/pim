<template>
  <div>
    <q-table
      :rows="list"
      :columns="columns"
      separator="cell"
      bordered
      hide-bottom
      :pagination="{rowsPerPage: 0}"
      flat
      row-key="id"
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
  </div>
</template>

<script>
import { fetchList } from 'src/Modules/Protocol/api/protocol.js'

export default {
  components: {},
  props: {
    list: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      key: 1,
      func: fetchList,
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
      ]
    }
  },
  methods: {}
}
</script>
