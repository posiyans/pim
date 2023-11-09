<template>
  <q-input
    :model-value="newValue"
    :label="label"
    :outlined="outlined"
    :readonly="readonly"
    :dense="dense"
    :rules="[rule]"
    @update:model-value="setValue"
  >
    <template v-if="!readonly" v-slot:after>
      <q-btn-group outline>
        <q-btn v-if="nosave" flat padding="sm" icon="restore_page" color="red" @click.stop="restoreData" />
        <q-btn v-if="nosave" flat padding="sm" icon="save" color="green" class="cursor-pointer" @click.stop="check" />
      </q-btn-group>
    </template>
  </q-input>
</template>

<script>

export default {
  props: {
    modelValue: {
      type: String,
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
      newValue: null,
      nosave: false
    }
  },
  mounted() {
    this.newValue = this.modelValue
  },
  methods: {
    getData() {
      this.nosave = false
      this.$store.dispatch('users/getInfo', this.uid)
      this.newValue = this.modelValue
    },
    rule(value) {
      // eslint-disable-next-line
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
      if (value.length === 0) {
        return true
      }
      if (!re.test(value)) {
        return 'Пожалуйста, введите действующий email'
      } else {

        return true

      }
    },
    restoreData() {
      this.newValue = this.modelValue
      this.$nextTick(() => {
        this.nosave = false
      })
    },
    check() {
      this.saveData()
    },
    saveData() {
      // todo проработать механизм изменения адраса почтового ящика
      const data = {
        user_id: this.userId,
        field: this.name,
        value: this.newValue,
      }
      this.func(data)
        .then(response => {
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
