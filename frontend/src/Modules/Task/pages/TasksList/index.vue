<template>
  <div>
    <div class="row items-center q-col-gutter-sm q-pb-xs">
      <div style="width: 200px;">
        <q-input v-model="listQuery.title" label="Найти..." dense clearable outlined @update:model-value="handleFilter" />
      </div>
      <div style="min-width: 200px;">
        <QSelectExecutor v-model="listQuery.executor" clearable outlined dense @update:model-value="handleFilter" />
      </div>
      <div style="min-width: 200px;">
        <TaskStatusSelect v-model="listQuery.status" clearable outlined dense @update:model-value="handleFilter" />
      </div>
      <div>
        <q-btn color="primary" label="Найти" @click="handleFilter" />
      </div>
      <div>
        <q-checkbox
          v-if="moderator"
          v-model="listQuery.archiv"
          label="Архив"
          true-value="1"
          false-value=""
          @update:model-value="handleFilter"
        />
      </div>
    </div>
    <div>
      <ShowTable :list="list" />
    </div>
    <LoadMore :key="key" v-model:list-query="listQuery" :func="func" @setList="setList" />
  </div>
</template>

<script>
/* eslint-disable */
import { computed, defineComponent, onMounted, ref } from 'vue'
import { fetchList } from 'src/Modules/Task/api/task.js'
import LoadMore from 'src/components/LoadMore/index.vue'
import QSelectExecutor from 'src/Modules/User/components/QSelectExecutor/index.vue'
import ShowTable from 'src/Modules/Task/components/ShowTaskTable/index.vue'
import TaskStatusSelect from 'src/Modules/Task/components/TaskStatusSelect/index.vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router/dist/vue-router'

export default defineComponent({
  components: {
    LoadMore,
    QSelectExecutor,
    ShowTable,
    TaskStatusSelect
  },
  props: {},
  setup(props, { emit }) {
    const data = ref(null)
    const list = ref([])
    const func = fetchList
    const router = useRouter()
    const route = useRoute()
    const listQuery = ref({
      page: +route.query.page || 1,
      limit: +route.query.limit || 20,
      find: route.query.find || '',
      archiv: route.query.archiv || '',
      title: route.query.title || '',
      executor: +route.query.executor || '',
      status: +route.query.status || ''
    })
    const key = ref(1)
    const store = useStore()

    const user = computed(() => {
      return store.state.user.info
    })
    const moderator = computed(() => {
      return user?.roles?.includes('moderator')
    })
    const setList = (val) => {
      list.value = val
      // const queryString = Object.keys(listQuery.value).map(key => key + '=' + listQuery.value[key]).join('&');
      router.replace({ name: 'TaskList', query: listQuery.value, replace: true })
      // history.push(null, '', '/task/list?' + queryString)
      // setTimeout(() => {
      // router.replace('/task/list?' + queryString)
      // }, 1500)
    }
    onMounted(() => {
      // console.log('mounted')
    })
    const handleFilter = () => {
      listQuery.value.page = 1
      key.value++
    }
    return {
      func,
      data,
      list,
      listQuery,
      key,
      handleFilter,
      moderator,
      setList
    }
  }
})
</script>

<style scoped>

</style>
