<template>
  <div>
    <div v-if="bot">
      <div>
        Имя бота: <span class="text-primary">
                  {{ bot.first_name }}

      </span>
      </div>
      <div>
        Сылка на бот:
        <span class="cursor-pointer" @click="openBot">

        <span class="text-primary">
        @{{ bot.username }}

        </span>
        </span>
      </div>
      <slot></slot>
    </div>
    <div v-else-if="!loading" class="text-red q-pa-md">
      Бот недоступен
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import { defineComponent, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getTelegramBotInfo } from 'src/Modules/Setting/api/setting'

export default defineComponent({
  components: {},
  props: {},
  setup() {
    const bot = ref(null)
    const loading = ref(false)
    const router = useRouter()
    const route = useRoute()
    const openBot = () => {
      window.open('https://t.me/' + bot.value.username, '_blank')
    }
    const getInfo = () => {
      loading.value = true
      getTelegramBotInfo()
        .then(res => {
          bot.value = res.data
        })
        .finally(() => {
          loading.value = false
        })
    }
    onMounted(() => {
      getInfo()
    })
    return {
      bot,
      loading,
      openBot
    }
  }
})
</script>

<style scoped>

</style>
