<template>
  <div v-if="user">
    <div class="row items-center q-col-gutter-md q-pa-md">
      <div>
        <q-checkbox
          v-model="user.two_factor"
          label="Двухэтапная аутентификация "
          color="negative"
          @update:model-value="saveData"
        />
      </div>
    </div>
    <div v-if="user.two_factor">
      <div class="text-grey q-pa-sm">Двухэтапная аутентификация через</div>
      <div v-for="item in user.two_factor_valid" :key="item.key" class="row items-center">
        <div>
          <q-checkbox v-model="user.two_factor_enable" :val="item.key" @update:model-value="saveData" />
        </div>
        <div class="q-py-md q-mr-lg" :class="{ 'text-negative': item.error}">
          <div>
            {{ item.label }}
          </div>
          <div v-if="item.error" class="text-small-80">
            {{ item.error_message }}
          </div>
        </div>
        <div v-if="item.key === 'google2fa'">
          <ChangeSecretKey v-if="showChangeSecret" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getTwoFactorSettingsForUser, updateTwoFactorSettingsForUser } from 'src/Modules/User/api/user'
import ChangeSecretKey from 'src/Modules/Google2Fa/components/ChangeSecretKey/index.vue'

export default {
  components: {
    ChangeSecretKey
  },
  props: {
    userId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      user: null
    }
  },
  computed: {
    showChangeSecret() {
      return this.$store.state.user.info.id === this.userId
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      const data = {
        id: this.userId
      }
      getTwoFactorSettingsForUser(data)
        .then(res => {
          this.user = res.data
        })
    },
    saveData() {
      updateTwoFactorSettingsForUser(this.user)
        .then(res => {
          this.getData()
        })
    }
  }
}
</script>

<style scoped>

</style>
