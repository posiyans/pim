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
      option-label="name"
      option-value="id"
      :rules="rules"
      @update:model-value="setValue"
    >
      <template v-if="add" v-slot:append>
        <q-btn round dense flat icon="add" @click.stop.prevent="dialogVisible = true" />
      </template>
    </q-select>
    <q-dialog v-model="dialogVisible">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Добавить тип протокола</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <q-input outlined v-model="name" dense label="Название типа" />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn color="negative" flan label="Отмена" v-close-popup />
          <q-btn color="primary" label="Добавить" @click="addType" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { createTypeProtocol, getTypeProtocol } from 'src/Modules/Protocol/api/protocol'

export default {
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    add: {
      type: Boolean,
      default: false
    },
    clearable: {
      type: Boolean,
      default: false
    },
    autoSelect: {
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
    rules: {
      type: Array,
      default: () => []
    },
    label: {
      type: String,
      default: 'Тип протокола'
    },
  },
  data() {
    return {
      protokolTypeOptions: [],
      dialogVisible: false,
      name: ''
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    setValue(val) {
      this.$emit('update:model-value', val)
    },
    getData() {
      getTypeProtocol()
        .then(res => {
          this.protokolTypeOptions = res.data
          if (this.autoSelect && !this.modelValue && this.protokolTypeOptions.length > 0) {
            this.setValue(this.protokolTypeOptions[0].id)
          }
        })
    },
    addType() {
      if (this.name !== '') {
        const data = {
          name: this.name
        }
        createTypeProtocol(data)
          .then(res => {
            this.setValue(res.data.id)
            this.getData()
            this.dialogVisible = false
          })
      }
    }
  }
}
</script>

<style scoped>

</style>
