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
  data() {
    return {
      // allExecutor: []
    }
  },
  computed: {
    allExecutor() {
      return this.$store.state.users.executors
    }
  },
  mounted() {
    // this.getExecutors()
    this.$store.dispatch('users/getExecutors')
  },
  methods: {
    setValue(val) {
      this.$emit('update:model-value', val)
    }
    // getExecutors() {
    //   fetchExecutors()
    //     .then(response => {
    //       this.allExecutor = response.data
    //     })
    // }
  }
}
</script>
<style scoped>

</style>
