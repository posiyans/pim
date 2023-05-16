<template>
  <q-input
    :model-value="newValue"
    :label="label"
    :outlined="outlined"
    :readonly="readonly"
    :dense="dense"
    @update:model-value="setValue"
  >
    <template v-if="!readonly" v-slot:after>
      <q-btn-group outline>
        <q-btn v-if="nosave" flat padding="sm" icon="restore_page" color="red" @click.stop="restoreData" />
        <q-btn v-if="nosave" flat padding="sm" icon="save" color="green" class="cursor-pointer" @click.stop="check" />
      </q-btn-group>
    </template>
    <template v-slot:append>
      <GetLastUserIdBtn @setId="setTelegramId" />
    </template>
  </q-input>
</template>

<script>

import GetLastUserIdBtn from 'src/Modules/Telegram/components/GetLastUserIdBtn/index.vue'

export default {
  components: {
    GetLastUserIdBtn
  },
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    func: {
      type: Function,
      required: true
    },
    userId: {
      type: [String, Number],
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    readonly: {
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
    name: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      newValue: '',
      nosave: false
    }
  },
  mounted() {
    this.newValue = this.modelValue
  },
  methods: {
    setTelegramId(val) {
      if (this.newValue !== val) {
        this.setValue(val)
      }
    },
    restoreData() {
      this.newValue = this.modelValue
      this.$nextTick(() => {
        this.nosave = false
      })
    },
    check() {
      const data = {
        user_id: this.userId,
        field: this.name,
        value: this.newValue,
      }
      this.func(data)
        .then(() => {
          this.$emit('reload')
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.$q.notify({
              type: 'negative',
              message: error.response.data.message
            })
          }
        })
    },
    setValue(val) {
      this.nosave = true
      this.newValue = val
    }
  }
}
</script>

<style scoped>

</style>
