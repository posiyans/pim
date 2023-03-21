<template>
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
      <q-tab name="profile" label="Профиль" />
      <q-tab v-if="user.id" name="g2fa" label="2Fa" />
    </q-tabs>
    <q-separator />
    <q-tab-panels v-model="tab" animated>
      <q-tab-panel name="profile">
        <ProfileUser :user-id="userId"
                     @cancel="editCancel" />
      </q-tab-panel>

      <q-tab-panel name="g2fa">
        <User2FaSetting v-if="user.id" :user-id="user.id" />
      </q-tab-panel>
    </q-tab-panels>
  </div>
</template>

<script>
import { getLastUserFromTelegram, getUserInfo, updateUser } from 'src/Modules/User/api/user'
import User2FaSetting from 'src/Modules/User/components/User2FaSetting/index.vue'
import ProfileUser from './ProfileUser.vue'

export default {
  components: {
    User2FaSetting,
    ProfileUser
  },
  props: {
    userId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      tab: 'profile',
      user: {
        login: '',
        full_name: '',
        name: '',
        phone: '',
        aliases: [],
        options: {},
        hide: true,
        moderator: false
      }
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getTelegramId() {
      getLastUserFromTelegram()
        .then(res => {
          this.$q.dialog({
            title: 'Последний id: ' + res.data.id,
            message: 'Проверьте у пользователя: ' + res.data.first_name
          }).onOk(() => {
            this.user.options.telegram = res.data.id
          })
        })
    },
    editCancel() {
      this.$emit('cancel')
    },
    saveData() {
      updateUser(this.user)
        .then(() => {
          this.$emit('success')
        })
    },
    reload() {
      this.$emit('success')
    },
    getData() {
      if (this.userId) {
        const data = {
          id: this.userId
        }
        getUserInfo(data)
          .then(res => {
            this.user = res.data
            this.user.aliases = this.user.aliases.map(item => +item)
            this.user.moderator = this.user.roles.includes('moderator')
          })
      }
    }

  }
}
</script>

<style scoped>

</style>
