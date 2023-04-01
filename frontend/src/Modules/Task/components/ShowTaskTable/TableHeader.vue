<template>
  <thead>
  <tr class="q-table--cell-separator">
    <th v-for="item in columns" :key="item.name" class="q-py-xs" :style="item.width ? 'width: ' + item.width + ';' : ''">
      {{ item.label }}
    </th>
  </tr>
  </thead>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  components: {},
  props: {
    columns: {
      type: Array,
      required: true
    }
  },
  setup() {
    return {}
  }
})
</script>

<style scoped>
th {
  /*border: 1px solid #a1a1a1;*/
  border-bottom-width: 1px;
  border-collapse: separate;
  border-color: rgba(0, 0, 0, 0.12);
}

tr {
  height: 48px;
  border-color: rgba(0, 0, 0, 0.12);

}
</style>
