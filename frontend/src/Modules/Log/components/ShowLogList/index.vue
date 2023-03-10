<template>
  <q-list bordered separator>
    <template v-for="log in list" :key="log.id">
      <q-item v-if="log.type === 'info'" clickable v-ripple>
        <q-item-section avatar class="">
          <div class="row justify-center full-width">
            <AvatarById :id="log.user_id" size="24px" />
          </div>
          <UserNameById :id="log.user_id" class="text-grey-8 text-center full-width " />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ log.value.text }}</q-item-label>
          <q-item-label caption>
            <ShowTime :time="log.created_at" format="DD.MM.YYYY HH:mm" />
          </q-item-label>
        </q-item-section>
      </q-item>
    </template>
  </q-list>
</template>

<script>
import UserNameById from 'src/Modules/User/components/UserNameById/index.vue'
import ShowTime from 'src/components/ShowTime/index.vue'
import AvatarById from 'src/Modules/User/components/AvatarById/index.vue'

export default {
  components: {
    AvatarById,
    UserNameById,
    ShowTime
  },
  props: {
    list: {
      type: Array,
      required: true
    }
  }
}
</script>

<style scoped>

</style>
