<template>
  <div>
    <el-select
      :model-value="modelValue"
      :placeholder="placeholder"
      :clearable="clearable"
      :multiple="multiple"
      @update:model-value="setValue">
      <el-option
        v-for="item in allExecutor"
        :key="item.id"
        :label="item.name"
        :value="item.id" />
    </el-select>
  </div>
</template>

<script>
import { fetchExecutors } from 'src/Modules/User/api/user.js'

export default {
  props: {
    modelValue: {
      type: [String, Number, Array],
      default: ''
    },
    clearable: {
      type: Boolean,
      default: false
    },
    multiple: {
      type: Boolean,
      default: false
    },
    placeholder: {
      type: String,
      default: 'Исполнитель'
    }
  },
  data() {
    return {
      allExecutor: []
    }
  },

  mounted() {
    this.getExecutors()
  },
  methods: {
    setValue(val) {
      this.$emit('update:model-value', val)
    },
    getExecutors() {
      fetchExecutors()
        .then(response => {
          this.allExecutor = response.data
        })
    }
  }
}
</script>
<style scoped>

</style>
