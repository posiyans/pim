<template>
  <div class="">
    <div v-if="loading" class="q-pa-lg text-center">
      <q-spinner-facebook
        color="primary"
        size="4em"
      />
    </div>
    <div v-else>
      <q-btn v-if="showCount" class="full-width bg-grey-2 text-grey-8" :loading="listLoading" label="Показать еще" @click="addItems" />
      <pagination v-show="total > 0" :total="total" v-model:page="currentPage" v-model:limit="itemLimit" :autoScroll="autoScroll" class="q-pt-sm" />
      <div v-if="total === 0 && !listLoading" class="q-pa-lg text-center text-h6 text-primary">
        Пусто
      </div>
    </div>
  </div>
</template>

<script>
import { computed, defineComponent, onMounted, ref, watch } from 'vue'
import Pagination from 'src/components/Pagination/index.vue'
import { SessionStorage, useQuasar } from 'quasar'
import { useRoute } from 'vue-router'

export default defineComponent({
  components: {
    Pagination
  },
  props: {
    listQuery: { type: Object },
    func: { type: Function }
  },
  setup(props, { emit }) {
    const route = useRoute()
    const addPage = ref(false)
    const loading = ref(true)
    const loadMore = ref(false)
    const autoScroll = ref(false)
    const listLoading = ref(true)
    const list = ref([])
    const total = ref(0)
    const $q = useQuasar()
    const showCount = computed(() => {
      if (Math.ceil(total.value / props.listQuery.limit) > currentPage.value) {
        return true
      }
      return listLoading.value
    })
    const itemLimit = computed({
      get: () => {
        return props.listQuery.limit
      },
      set: (val) => {
        const tmp = Object.assign({}, props.listQuery)
        tmp.page = 1
        tmp.limit = val
        emit('update:list-query', tmp)
      }
    })
    const currentPage = computed({
      get() {
        return props.listQuery.page
      },
      set(val) {
        const tmp = Object.assign({}, props.listQuery)
        tmp.page = val
        emit('update:list-query', tmp)
      }
    })
    // todo для совместимости со старыми кусками кода
    watch(
      props.listQuery,
      () => {
        addPage.value = false
        getList()
      }
    )
    watch(
      () => props.listQuery,
      () => {
        addPage.value = false
        getList()
      }
    )
    const getList = () => {
      listLoading.value = true
      const tmp = Object.assign({}, props.listQuery)
      if (addPage.value) {
        tmp.page = tmp.page + addPage.value
      }
      props.func(tmp)
        .then(response => {
          total.value = response.data.total || response.data.meta?.total || 0
          if (loadMore.value) {
            loadMore.value = false
          } else {
            list.value = []
          }
          response.data.data.forEach(val => {
            list.value.push(val)
          })
          const cacheName = 'loadMore' + route.path
          SessionStorage.set(cacheName, { data: list.value, total: total.value })
          emit('setList', list.value)
          emit('setTotal', total.value)
          if (autoScroll.value) {
            scrollTo(0, 0)
          }
          autoScroll.value = true
        })
        .catch(er => {
          $q.notify({
            message: er.response.data.errors,
            color: 'negative',
            position: 'top-right'
          })
        })
        .finally(() => {
          loading.value = false
          listLoading.value = false
        })
    }
    const addItems = () => {
      loadMore.value = true
      autoScroll.value = false
      addPage.value = +addPage.value + 1
      getList()
    }

    onMounted(() => {
      const cacheName = 'loadMore' + route.path
      if (SessionStorage.has(cacheName)) {
        const tmp = SessionStorage.getItem(cacheName)
        emit('setList', tmp.data)
        total.value = tmp.total
      }
      getList()
    })

    return {
      loading,
      showCount,
      listLoading,
      total,
      autoScroll,
      itemLimit,
      currentPage,
      addItems
    }
  }
})
</script>

<style scoped>

</style>
