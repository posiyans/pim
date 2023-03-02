<template>
  <div>
    <q-input
      :model-value="showDate"
      :outlined="outlined"
      :dense="dense"
      :clearable="clearable"
      :label="label"
      :placeholder="placeholder"
      @clear="clear"
      @focus="showDialog"
      @click="showDialog"
      :rules="rules"
    >
      <template v-slot:append>
        <q-icon name="event" class="cursor-pointer">
          <q-popup-proxy
            ref="refDateDialog"
            cover
            transition-show="scale"
            transition-hide="scale"
            :breakpoint="600"
          >
            <div class="text-center">
              <q-date
                :model-value="modelValue"
                mask="YYYY-MM-DD"
                :options="options"
                @update:model-value="setValue"
              >
                <div class="text-right">
                  <q-btn label="OK" color="primary" flat v-close-popup />
                </div>
              </q-date>
            </div>
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: {
      validator: () => {
        return true
      },
      required: true
    },
    placeholder: {
      type: String,
      default: undefined
    },
    label: {
      type: String,
      default: undefined
    },
    outlined: {
      type: Boolean,
      default: false
    },
    dense: {
      type: Boolean,
      default: false
    },
    clearable: {
      type: Boolean,
      default: false
    },
    formatValue: {
      type: String,
      default: 'YYYY-MM-DD'
    },
    format: {
      type: String,
      default: 'DD-MM-YYYY'
    },
    options: {
      type: [Function, Array],
      default: undefined
    },
    autoClose: {
      type: Boolean,
      default: false
    },
    rules: {
      type: Array
    }
  },
  data() {
    return {
      show: false
    }
  },
  computed: {
    showDate: {
      get() {
        if (this.$moment(this.modelValue).isValid()) {
          return this.$moment(this.modelValue).format(this.format)
        }
        return this.modelValue
      },
      set() {
        return true
      }
    }
  },
  methods: {
    showDialog() {
      this.$refs.refDateDialog.show()
    },
    clear() {
      this.$emit('update:model-value', '')
    },
    setValue(val) {
      this.$emit('update:model-value', val)
      if (this.autoClose && val) {
        this.$refs.refDateDialog.hide()
      }
    }
  }
}
</script>

<style scoped>

</style>
