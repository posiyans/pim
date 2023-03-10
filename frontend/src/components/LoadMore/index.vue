<template>
  <div class="q-py-md">
    <div v-if="loading" class="q-pa-lg text-center">
      <q-spinner-facebook
        color="primary"
        size="4em"
      />
    </div>
    <div v-else>
      <q-btn
        v-if="showBtn"
        class="full-width bg-grey-2 text-grey-8"
        :loading="listLoading"
        no-caps
        :label="loadMore"
        @click="add"
      />
      <pagination
        v-show="total > 0"
        :total="total"
        :page="currentPage"
        :limit="pageSize"
        :autoScroll="autoScroll"
        @update:page="setPage"
        @update:limit="setLimit"
      />
    </div>
  </div>
</template>

<script>
import Pagination from 'src/components/Pagination/index.vue'
import { defineComponent, nextTick } from 'vue'
import { scrollTo } from 'src/utils/scroll-to'

export default defineComponent({
  props: {
    autoScroll: {
      type: Boolean,
      default: true
    },
    listQuery: { type: Object },
    func: { type: Function }
  },
  components: {
    Pagination
  },
  data() {
    return {
      tt: 1,
      loading: true,
      total: 0,
      offset: 0,
      list: [],
      showCount: 0,
      listLoading: false
    }
  },
  computed: {
    currentPage: {
      get() {
        return this.listQuery.page
      },
      set(val) {
        const tmp = Object.assign({}, this.listQuery)
        tmp.page = val
        this.$emit('update:listQuery', tmp)
      }
    },
    pageSize: {
      get() {
        return this.listQuery.limit
      },
      set(val) {
        const tmp = Object.assign({}, this.listQuery)
        tmp.limit = val
        this.$emit('update:listQuery', tmp)
      }
    },
    showBtn() {
      return this.total > this.showCount
    },
    loadMore() {
      if (this.total > this.showCount) {
        return 'Показать еще'
      }
      return ''
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    setPage(val) {
      const tmp = Object.assign({}, this.listQuery)
      tmp.page = val
      this.$emit('update:listQuery', tmp)
      nextTick(() => {
        this.getList()
      })
    },
    setLimit(val) {
      const tmp = Object.assign({}, this.listQuery)
      tmp.limit = val
      tmp.page = 1
      this.$emit('update:listQuery', tmp)
      nextTick(() => {
        this.getList()
      })
    },
    getList() {
      this.listLoading = true
      this.func(this.listQuery)
        .then(response => {
          this.total = response.data.total || 0
          this.offset = response.data.offset || 0
          this.list = response.data.data || response.data

          this.showCount = this.listQuery.page * this.listQuery.limit
          this.$emit('setList', this.list)
          this.$emit('setOffset', this.offset)
          this.$emit('setTotal', this.total)
        })
        .finally(() => {
          this.loading = false
          this.listLoading = false
          if (this.autoScroll) {
            scrollTo(0, 800)
          }
        })
    },
    add() {
      const tmp = Object.assign({}, this.listQuery)
      tmp.page++
      this.$emit('update:listQuery', tmp)
      this.listLoading = true
      this.func(tmp)
        .then(response => {
          this.total = response.data.total

          response.data.data.forEach(val => {
            this.list.push(val)
            this.showCount++
          })
          this.$emit('setList', this.list)
          setTimeout(() => {
            this.listLoading = false
          }, 500)
        })
    }
  }
})
</script>

<style scoped>

</style>
