<template>
  <div>
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
        <q-checkbox
          v-model="listQuery.archiv"
          label="Архив"
          true-value="1"
          false-value=""
          @update:model-value="handleFilter"
        />
      </div>
      <div>
        <q-btn icon="add" color="primary" flat to="/protocol/add" />
      </div>
    </div>
    <TableBlock :list="list" />
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />
  </div>
</template>

<script>
/* eslint-disable */
import { defineComponent, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { fetchList } from 'src/Modules/Protocol/api/protocol.js'
import QSelectTypeProtocol from 'src/Modules/Protocol/components/QSelectTypeProtocol/index.vue'
import LoadMore from 'src/components/LoadMore/index.vue'
import TableBlock from './components/TableBlock/index.vue'

export default defineComponent({
  components: { LoadMore, QSelectTypeProtocol, TableBlock },
  props: {},
  setup(props, { emit }) {
    const key = ref(null)
    const func = fetchList
    const list = ref([])
    const router = useRouter()
    const route = useRoute()
    const listQuery = ref({
      page: +route.query.page || 1,
      limit: +route.query.limit || 20,
      find: route.query.find || '',
      type: route.query.type || '',
      archiv: route.query.archiv || ''
    })
    const setList = (val) => {
      router.replace({ name: 'ProtokolList', query: listQuery.value })
      list.value = val
    }
    const handleFilter = () => {
      listQuery.value.page = 1
      key.value++
    }
    return {
      key,
      func,
      list,
      listQuery,
      setList,
      handleFilter
    }
  }
})
</script>

<style scoped>

</style>
