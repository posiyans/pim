<template>
  <div>
    <el-select
      :model-value="modelValue"
      :placeholder="placeholder"
      :clearable="clearable"
      @update:model-value="setValue"
    >
      <el-option v-for="item in protokolTypeOptions" :key="item.key" :label="item.display_name" :value="item.key" />
    </el-select>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    clearable: {
      type: Boolean,
      default: false
    },
    placeholder: {
      type: String,
      default: 'Тип протокола'
    },
  },
  data() {
    return {
      protokolTypeOptions: [
        { key: 'psd', display_name: 'Протоколы СД' },
        { key: 'skype', display_name: 'Скайп Протоколы' },
        { key: 'year', display_name: 'Готодовые протоколы' }
      ]
    }
  },
  methods: {
    setValue(val) {
      this.$emit('update:model-value', val)
    }
  }
}
</script>

<style scoped>

</style>
