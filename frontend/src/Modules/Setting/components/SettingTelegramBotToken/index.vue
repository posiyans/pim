<template>
  <div>
    <q-input v-model="token" outlined label="Bot Token" />
    <div class="q-pa-sm">
      <q-btn label="Сохранить" color="primary" @click="saveToken" />
    </div>
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
    </div>
    <div v-else-if="!loading" class="text-red q-pa-md">
      Бот недоступен
    </div>
  </div>
</template>

<script>
import { getTelegramBotInfo, getTelegramBotToken, updateTelegramBotToken } from 'src/Modules/Setting/api/setting'

export default {
  data() {
    return {
      token: '',
      loading: false,
      bot: null
    }
  },
  mounted() {
    this.getData()
    this.getInfo()
  },
  methods: {
    openBot() {
      window.open('https://t.me/' + this.bot.username, '_blank')
    },
    getInfo() {
      this.loading = true
      getTelegramBotInfo()
        .then(res => {
          this.bot = res.data
        })
        .finally(() => {
          this.loading = false
        })
    },
    getData() {
      getTelegramBotToken()
        .then(res => {
          this.token = res.data
        })
    },
    saveToken() {
      const data = {
        token: this.token
      }
      updateTelegramBotToken(data)
        .then(res => {
          this.getData()
          this.getInfo()
          this.$q.notify({
            type: 'secondary',
            message: 'Ok'
          })
        })
    }
  }
}
</script>

<style scoped>

</style>
