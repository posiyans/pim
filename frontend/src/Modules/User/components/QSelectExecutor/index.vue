<template>
  <div>
    <q-select
      :model-value="modelValue"
      :options="allExecutor"
      :clearable="clearable"
      :multiple="multiple"
      :label="label"
      :outlined="outlined"
      map-options
      :readonly="readonly"
      :options-dense="optionsDense"
      :dense="dense"
      option-value="id"
      option-label="name"
      emit-value
      @update:model-value="setValue"
    />
  </div>
</template>

<script>

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
    readonly: {
      type: Boolean,
      default: false
    },
    optionsDense: {
      type: Boolean,
      default: false
    },
    outlined: {
      type: Boolean,
      default: false
    },
    dense: {
      type: Boolean,
      default: false
    },
    multiple: {
      type: Boolean,
      default: false
    },
    label: {
      type: String,
      default: 'Исполнитель'
    }
  },
  computed: {
    allExecutor() {
      if (this.$store.state.users.executors) {
        return this.$store.state.users.executors.filter(user => !user.hide)
      }
      return []
    }
  },
  mounted() {
    this.$store.dispatch('users/getExecutors')
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
