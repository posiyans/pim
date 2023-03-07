<template>
  <div>
    <q-select
      :model-value="modelValue"
      :label="label"
      :clearable="clearable"
      :options="protokolTypeOptions"
      map-options
      emit-value
      :dense="dense"
      :outlined="outlined"
      option-label="display_name"
      option-value="key"
      @update:model-value="setValue"
    />
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
    outlined: {
      type: Boolean,
      boolean: false
    },
    dense: {
      type: Boolean,
      boolean: false
    },
    label: {
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
