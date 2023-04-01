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
      <q-tab v-if="userId" name="g2fa" label="2Fa" />
    </q-tabs>
    <q-separator />
    <q-tab-panels v-model="tab" animated>
      <q-tab-panel name="profile">
        <ProfileUser :user-id="userId"
                     @cancel="editCancel" />
      </q-tab-panel>

      <q-tab-panel name="g2fa">
        <User2FaSetting v-if="userId" :user-id="userId" />
      </q-tab-panel>
    </q-tab-panels>
  </div>
</template>

<script>
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
      tab: 'profile'
    }
  },
  methods: {
    editCancel() {
      this.$emit('cancel')
    }
  }
}
</script>

<style scoped>

</style>
