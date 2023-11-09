<template>
  <div>
    <q-select
      :model-value="modelValue"
      :options="options"
      :clearable="clearable"
      :multiple="multiple"
      :label="label"
      :outlined="outlined"
      map-options
      :readonly="readonly"
      :options-dense="optionsDense"
      :dense="dense"
      emit-value
      @update:model-value="setValue"
    />
  </div>
</template>

<script>
/* eslint-disable */
import { defineComponent } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default defineComponent({
  components: {},
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
      default: 'Статус'
    }
  },
  setup(props, { emit }) {
    const options = [
      {
        value: 1,
        label: 'Закрытые'
      },
      {
        value: 2,
        label: 'Не закрытые'
      },
      {
        value: 3,
        label: 'Просроченные'
      },
      {
        value: 4,
        label: 'Без ответа'
      }
    ]
    const router = useRouter()
    const route = useRoute()

    const setValue = (val) => {
      emit('update:model-value', val)
    }
    return {
      options,
      setValue
    }
  }
})
</script>

<style scoped>

</style>
