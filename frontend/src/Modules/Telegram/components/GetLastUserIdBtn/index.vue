<template>
  <div>
    <slot>
      <q-btn round dense flat icon="add" @click="showDialog" />
    </slot>
  </div>
  <q-dialog v-model="dialogVisible">
    <q-card>
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6 q-pr-lg">Отправьте любое сообщение боту</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section class="q-pa-lg">
        <TelegramBotInfo />
      </q-card-section>
      <q-card-section class="q-pa-lg">
        <div class="row items-center justify-between">
          <div>
            После отправки нажмите
          </div>
          <q-btn color="primary" label="Получить id" @click="getTelegramId" />
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>

</template>

<script>
/* eslint-disable */
import { defineComponent, ref } from 'vue'
import { getLastUserFromTelegram } from 'src/Modules/Telegram/api/telegram.js'
import TelegramBotInfo from 'src/Modules/Telegram/components/TelegramBotInfo/index.vue'
import { useQuasar } from 'quasar'

export default defineComponent({
  components: {
    TelegramBotInfo
  },
  props: {},
  setup(props, { emit }) {
    const data = ref(null)
    const $q = useQuasar();
    const dialogVisible = ref(false)
    const getTelegramId = () => {
      getLastUserFromTelegram()
        .then(res => {
          if (res.data.status) {
            $q.dialog({
              title: 'Последний id: ' + res.data.data.id,
              message: 'Проверьте у пользователя: ' + res.data.data.first_name
            }).onOk(() => {
              emit('setId', res.data.data.id)
              dialogVisible.value = false
            })
          } else {
            $q.dialog({
              title: 'Ошибка ',
              color: 'negative',
              message: res.data.error
            })
          }
        })
    }
    const showDialog = () => {
      dialogVisible.value = true
    }
    return {
      data,
      showDialog,
      getTelegramId,
      dialogVisible
    }
  }
})
</script>

<style scoped>

</style>
