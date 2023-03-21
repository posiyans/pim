<template>
  <div>
    <div>
      Настойки
    </div>
    <div>
      <q-tabs
        v-model="tab"
        dense
        class="text-grey"
        active-color="primary"
        indicator-color="primary"
        align="left"
        narrow-indicator
      >
        <q-tab name="telegram" label="Telegram" />
        <q-tab name="mail" label="Mail" />
      </q-tabs>

      <q-separator />

      <q-tab-panels v-model="tab" animated>
        <q-tab-panel name="telegram">
          <SettingTelegramBotToken />
        </q-tab-panel>

        <q-tab-panel name="mail">
          <SettingMail />
        </q-tab-panel>

      </q-tab-panels>
    </div>
  </div>
</template>

<script>
import SettingTelegramBotToken from 'src/Modules/Setting/components/SettingTelegramBotToken/index.vue'
import SettingMail from 'src/Modules/Setting/components/SettingMail/index.vue'

export default {
  components: {
    SettingTelegramBotToken,
    SettingMail
  },
  data() {
    return {
      tab: 'telegram'
    }
  },
}
</script>

<style scoped>

</style>
