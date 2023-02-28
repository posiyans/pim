<template>
  <div class="pagination-container">
    <div class="flex q-gutter-md">
      <div class="self-center">
        Всего {{ total }}
      </div>
      <div class="self-center page-size-block">
        <q-select
          dense
          :model-value="limit"
          :options="pageSizes"
          :display-value="`${limit ? limit + ' на странице' : ''}`"
          @update:model-value="handleSizeChange"
        >
        </q-select>
      </div>
      <div class="self-center">
        <q-pagination
          :model-value="page"
          :max="maxPage"
          :max-pages="6"
          glossy
          dense
          direction-links
          icon-prev="fast_rewind"
          icon-next="fast_forward"
          @update:model-value="handleCurrentChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    total: {
      required: true,
      type: Number
    },
    page: {
      type: Number,
      default: 1
    },
    limit: {
      type: Number,
      default: 20
    },
    pageSizes: {
      type: Array,
      default() {
        return [5, 10, 20, 30, 50]
      }
    }
  },
  computed: {
    maxPage() {
      return Math.ceil(this.total / this.limit)
    }
  },
  methods: {
    handleSizeChange(val) {
      this.$emit('update:limit', val)
    },
    handleCurrentChange(val) {
      this.$emit('update:page', val)
    }
  }
}
</script>

<style scoped lang="scss">
.pagination-container {
  background: #fff;
  padding: 32px 0;
}

.page-size-block {
  @media all and (max-width: 500px) {
    display: none;
  }
}
</style>
